<?php

namespace App\Models;

use Auth;
use App\Helpers\Enum\MotivosPago;
use App\Helpers\Enum\EstadosPago;
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

  protected static function obtenerXId($idAlumno, $id) {
    $nombreTabla = PagoAlumno::nombreTabla();
    return PagoAlumno::select("pago.*")
                    ->leftJoin(Pago::nombreTabla() . " as pago", $nombreTabla . ".idPago", "=", "pago.id")
                    ->where("pago.eliminado", 0)
                    ->where($nombreTabla . ".idAlumno", $idAlumno)
                    ->where("pago.id", $id)->firstOrFail();
  }

  protected static function obtenerXClase($idClase) {
    $nombreTabla = PagoAlumno::nombreTabla();
    return PagoAlumno::select("pago.*")
                    ->leftJoin(Pago::nombreTabla() . " as pago", $nombreTabla . ".idPago", "=", "pago.id")
                    ->leftJoin(PagoClase::NombreTabla() . " as pagoClase", $nombreTabla . ".idPago", "=", "pagoClase.idPago")
                    ->where("pago.eliminado", 0)
                    ->where("pagoClase.idClase", $idClase)->first();
  }

  protected static function totalSaldoFavor($idAlumno) {
    $nombreTabla = PagoAlumno::nombreTabla();
    return PagoAlumno::leftJoin(Pago::nombreTabla() . " as pago", $nombreTabla . ".idPago", "=", "pago.id")
                    ->where("pago.eliminado", 0)
                    ->where($nombreTabla . ".idAlumno", $idAlumno)
                    ->where("pago.saldoFavorUtilizado", 0)->sum("pago.saldoFavor");
  }

  protected static function listar($idAlumno) {
    $nombreTabla = PagoAlumno::nombreTabla();
    return PagoAlumno::leftJoin(Pago::nombreTabla() . " as pago", $nombreTabla . ".idPago", "=", "pago.id")
                    ->where("pago.eliminado", 0)
                    ->where($nombreTabla . ".idAlumno", $idAlumno);
  }

  protected static function registrar($idAlumno, $request) {
    $datos = $request->all();
    $datosPago = Pago::registrar($datos, EstadosPago::Realizado, $request);
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
    Historial::Registrar([$idAlumno, Auth::user()->idEntidad], MensajesHistorial::TituloAlumnoRegistroPago, $mensajeHistorial, $datosPago["rutasImagenesComprobante"], FALSE, TRUE, $datosPago["id"], NULL, NULL, TiposHistorial::Pago);

    if ($datos["motivo"] == MotivosPago::Clases) {
      Clase::registrarXDatosPago($idAlumno, $datosPago["id"], $datos);
    }
  }
  
  protected static function actualizarEstado($idAlumno, $datos) {
    Pago::actualizarEstado($datos["idPago"], $datos["estado"]);
  }
  
  protected static function eliminar($idAlumno, $id) {
    PagoAlumno::obtenerXId($idAlumno, $id);
    Pago::eliminar($id);
  }
  
  protected static function verificarExistencia($idAlumno, $id) {
    try {
      PagoAlumno::obtenerXId($idAlumno, $id);
    } catch (Exception $ex) {
      return FALSE;
    }
    return TRUE;
  }

}
