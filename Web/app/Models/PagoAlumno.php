<?php

namespace App\Models;

use DB;
use Auth;
use App\Helpers\Enum\MotivosPago;
use App\Helpers\Enum\EstadosClase;
use App\Helpers\Enum\EstadosAlumno;
use App\Helpers\Enum\TiposHistorial;
use App\Helpers\Enum\MensajesHistorial;
use Illuminate\Database\Eloquent\Model;

class PagoAlumno extends Model {

  public $timestamps = false;
  protected $table = "pagoAlumno";
  protected $fillable = ["idAlumno", "idPago"];

  public static function nombreTabla() {
    $modeloPagoAlumno = new PagoAlumno();
    $nombreTabla = $modeloPagoAlumno->getTable();
    unset($modeloPagoAlumno);
    return $nombreTabla;
  }

  public static function listar($idAlumno, $soloMotivoClases = FALSE) {
    $nombreTabla = PagoAlumno::nombreTabla();
    $pagosAlumno = PagoAlumno::leftJoin(Pago::nombreTabla() . " as pago", $nombreTabla . ".idPago", "=", "pago.id")
            ->where("pago.eliminado", 0)
            ->where($nombreTabla . ".idAlumno", $idAlumno)
            ->select($nombreTabla . ".*", "pago.*", DB::raw("(SELECT SUM(costoHora)/COUNT(*) FROM " . Clase::nombreTabla() . " WHERE id IN (SELECT idClase FROM " . PagoClase::nombreTabla() . " WHERE idPago = pago.id) AND eliminado = 0) AS costoHoraPromedio"), DB::raw("(SELECT CONCAT(SUM(duracion), '-', ((SUM(duracion)/3600) * (SUM(costoHora)/COUNT(*)))) FROM " . Clase::nombreTabla() . " WHERE id IN (SELECT idClase FROM " . PagoClase::nombreTabla() . " WHERE idPago = pago.id) AND estado = '" . EstadosClase::Realizada . "' AND estado <> '" . EstadosClase::Cancelada . "' AND eliminado = 0) AS duracionMontoRealizado"), DB::raw("(SELECT CONCAT(SUM(duracion), '-', ((SUM(duracion)/3600) * (SUM(costoHora)/COUNT(*)))) FROM " . Clase::nombreTabla() . " WHERE id IN (SELECT idClase FROM " . PagoClase::nombreTabla() . " WHERE idPago = pago.id) AND estado <> '" . EstadosClase::Realizada . "' AND estado <> '" . EstadosClase::Cancelada . "' AND eliminado = 0) AS duracionMontoPendiente"));
    if ($soloMotivoClases) {
      $pagosAlumno->where("pago.motivo", MotivosPago::Clases);
    }
    return $pagosAlumno;
  }

  public static function obtenerXId($idAlumno, $id) {
    return PagoAlumno::listar($idAlumno)->where("pago.id", $id)->firstOrFail();
  }

  public static function obtenerXClase($idAlumno, $idClase) {
    $nombreTabla = PagoAlumno::nombreTabla();
    return PagoAlumno::listar($idAlumno)->select("pago.*")
                    ->leftJoin(PagoClase::nombreTabla() . " as pagoClase", $nombreTabla . ".idPago", "=", "pagoClase.idPago")
                    ->where("pagoClase.idClase", $idClase)->first();
  }

  public static function registrar($idAlumno, $request) {
    $datos = $request->all();
    $datos["saldoFavor"] = ((float) $datos["saldoFavor"]) + ((float) ($datos["considerarClasesIncompletas"] == 0 ? $datos["saldoFavorAdicional"] : 0));
    $datosPago = Pago::registrar($datos, $datos["estado"], $request);

    $pagoAlumno = new PagoAlumno([
        "idPago" => $datosPago["id"],
        "idAlumno" => $idAlumno
    ]);
    $pagoAlumno->save();

    if ($datos["usarSaldoFavor"] == 1) {
      Pago::whereIn("id", function($q) use ($idAlumno) {
                $q->select("idPago")->from(PagoAlumno::nombreTabla())->where("idAlumno", $idAlumno);
              })->where("eliminado", 0)
              ->where("id", "!=", $datosPago["id"])
              ->update(["saldoFavorUtilizado" => 1]);
    }
    if ($datos["motivo"] == MotivosPago::Clases) {
      Alumno::actualizarEstado($idAlumno, EstadosAlumno::Activo);
      if ((int) $datos["registrarSinGenerarClases"] == 0) {
        Clase::registrarXDatosPago($idAlumno, $datosPago["id"], $datos);
      }
    }
    PagoAlumno::registrarActualizarEventoPago($idAlumno, $datosPago);
  }

  public static function actualizar($idAlumno, $request) {
    $datos = $request->all();
    PagoAlumno::obtenerXId($idAlumno, $datos["idPago"]);
    $datosPago = Pago::actualizar($datos["idPago"], $datos, $request);

    if ($datos["usarSaldoFavor"] == 1) {
      Pago::whereIn("id", function($q) use ($idAlumno) {
        $nombreTabla = PagoAlumno::nombreTabla();
        $q->select($nombreTabla . ".idPago")
                ->from($nombreTabla)
                ->where($nombreTabla . ".idAlumno", $idAlumno)
                ->where($nombreTabla . ".eliminado", 0);
      })->update(["saldoFavorUtilizado" => 1]);
    }
    PagoAlumno::registrarActualizarEventoPago($idAlumno, $datosPago);
  }

  public static function actualizarEstado($idAlumno, $datos) {
    PagoAlumno::obtenerXId($idAlumno, $datos["idPago"]);
    Pago::actualizarEstado($datos["idPago"], $datos["estado"]);
  }

  public static function verificarExistencia($idAlumno, $id) {
    try {
      PagoAlumno::obtenerXId($idAlumno, $id);
    } catch (\Exception $ex) {
      return FALSE;
    }
    return TRUE;
  }

  public static function eliminar($idAlumno, $id) {
    PagoAlumno::obtenerXId($idAlumno, $id);
    Clase::eliminadXIdPago($idAlumno, $id);
    PagoAlumno::where("idAlumno", $idAlumno)->where("idPago", $id)->delete();
    Pago::eliminar($id);
  }

  private static function registrarActualizarEventoPago($idAlumno, $datosPago) {
    $listaMotivosPago = MotivosPago::listar();
    $mensajeHistorial = str_replace(["[MOTIVO]", "[DESCRIPCION]", "[MONTO]"], [$listaMotivosPago[$datosPago["motivo"]], (isset($datosPago["descripcion"]) && $datosPago["descripcion"] != "" ? "<br/><strong>Descripción:</strong> " . $datosPago["descripcion"] : ""), number_format((float) ($datosPago["monto"]), 2, ".", "")], MensajesHistorial::MensajeAlumnoRegistroPago);

    $historial = Historial::where("eliminado", 0)->where("idPago", $datosPago["id"])->first();
    $datos = [
        "idEntidades" => [$idAlumno, Auth::user()->idEntidad],
        "titulo" => MensajesHistorial::TituloAlumnoRegistroPago,
        "mensaje" => $mensajeHistorial,
        "imagenes" => $datosPago["imagenesComprobante"],
        "idPago" => $datosPago["id"],
        "tipo" => TiposHistorial::Pago
    ];
    if (isset($historial)) {
      Historial::actualizar($historial->id, $datos);
    } else {
      Historial::registrar($datos);
    }
  }

}
