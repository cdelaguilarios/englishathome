<?php

namespace App\Models;

use Auth;
use App\Helpers\Enum\MotivosPago;
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

  public static function listar($idAlumno) {
    $nombreTabla = PagoAlumno::nombreTabla();
    return PagoAlumno::leftJoin(Pago::nombreTabla() . " as pago", $nombreTabla . ".idPago", "=", "pago.id")
                    ->where("pago.eliminado", 0)
                    ->where($nombreTabla . ".idAlumno", $idAlumno);
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
    $datosPago = Pago::registrar($datos, $datos["estado"], $request);
    $pagoAlumno = new PagoAlumno([
        "idPago" => $datosPago["id"],
        "idAlumno" => $idAlumno
    ]);
    $pagoAlumno->save();

    if ($datos["usarSaldoFavor"] == 1) {
      Pago::whereIn("id", function($q) use ($idAlumno) {
        $nombreTabla = PagoAlumno::nombreTabla();
        $q->select($nombreTabla . ".idPago")
                ->from($nombreTabla)
                ->where($nombreTabla . ".idAlumno", $idAlumno)
                ->where($nombreTabla . ".eliminado", 0);
      })->update(["saldoFavorUtilizado" => 1]);
    }

    $listaMotivosPago = MotivosPago::listar();
    $mensajeHistorial = str_replace(["[MOTIVO]", "[DESCRIPCION]", "[MONTO]"], [$listaMotivosPago[$datos["motivo"]], (isset($datos["descripcion"]) && $datos["descripcion"] != "" ? "<br/><strong>Descripción:</strong> " . $datos["descripcion"] : ""), number_format((float) ($datos["monto"]), 2, ".", "")], MensajesHistorial::MensajeAlumnoRegistroPago);
    Historial::registrar([
        "idEntidades" => [$idAlumno, Auth::user()->idEntidad],
        "titulo" => MensajesHistorial::TituloAlumnoRegistroPago,
        "mensaje" => $mensajeHistorial,
        "imagenes" => $datosPago["imagenesComprobante"],
        "idPago" => $datosPago["id"],
        "tipo" => TiposHistorial::Pago
    ]);

    if ($datos["motivo"] == MotivosPago::Clases) {
      Alumno::actualizarEstado($idAlumno, EstadosAlumno::Activo);
      Clase::registrarXDatosPago($idAlumno, $datosPago["id"], $datos);
    }
  }

  public static function actualizar($idAlumno, $request) {
    $datos = $request->all();
    Pago::actualizar($datos["idPago"], $datos, $request);

    if ($datos["usarSaldoFavor"] == 1) {
      Pago::whereIn("id", function($q) use ($idAlumno) {
        $nombreTabla = PagoAlumno::nombreTabla();
        $q->select($nombreTabla . ".idPago")
                ->from($nombreTabla)
                ->where($nombreTabla . ".idAlumno", $idAlumno)
                ->where($nombreTabla . ".eliminado", 0);
      })->update(["saldoFavorUtilizado" => 1]);
    }
  }

  public static function actualizarEstado($idAlumno, $datos) {
    PagoAlumno::obtenerXId($idAlumno, $datos["idPago"]);
    Pago::actualizarEstado($datos["idPago"], $datos["estado"]);
  }

  public static function totalSaldoFavor($idAlumno) {
    return PagoAlumno::listar($idAlumno)->where("pago.saldoFavorUtilizado", 0)->sum("pago.saldoFavor");
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
    Pago::eliminar($id);
    Clase::eliminadXIdPago($idAlumno, $id);
  }

}
