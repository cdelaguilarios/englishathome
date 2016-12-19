<?php

namespace App\Models;

use Auth;
use App\Helpers\Enum\MotivosPago;
use App\Helpers\Enum\EstadosPago;
use App\Helpers\Enum\TiposHistorial;
use App\Helpers\Enum\MensajesHistorial;
use Illuminate\Database\Eloquent\Model;

class PagoProfesor extends Model {

  public $timestamps = false;
  protected $table = "pagoProfesor";
  protected $fillable = ["idProfesor", "idPago"];

  public static function NombreTabla() {
    $modeloPagoProfesor = new PagoProfesor();
    $nombreTabla = $modeloPagoProfesor->getTable();
    unset($modeloPagoProfesor);
    return $nombreTabla;
  }

  protected static function obtenerXId($idProfesor, $id) {
    $nombreTabla = PagoProfesor::nombreTabla();
    return PagoProfesor::select("pago.*")
                    ->leftJoin(Pago::nombreTabla() . " as pago", $nombreTabla . ".idPago", "=", "pago.id")
                    ->where("pago.eliminado", 0)
                    ->where($nombreTabla . ".idProfesor", $idProfesor)
                    ->where("pago.id", $id)->firstOrFail();
  }

  protected static function obtenerXClase($idClase) {
    $nombreTabla = PagoProfesor::nombreTabla();
    return PagoProfesor::select("pago.*")
                    ->leftJoin(Pago::nombreTabla() . " as pago", $nombreTabla . ".idPago", "=", "pago.id")
                    ->leftJoin(PagoClase::NombreTabla() . " as pagoClase", $nombreTabla . ".idPago", "=", "pagoClase.idPago")
                    ->where("pago.eliminado", 0)
                    ->where("pagoClase.idClase", $idClase)->first();
  }

  protected static function listar($idProfesor) {
    $nombreTabla = PagoProfesor::nombreTabla();
    return PagoProfesor::leftJoin(Pago::nombreTabla() . " as pago", $nombreTabla . ".idPago", "=", "pago.id")
                    ->where("pago.eliminado", 0)
                    ->where($nombreTabla . ".idProfesor", $idProfesor);
  }

  protected static function registrar($idProfesor, $request) {
    $datos = $request->all();
    $datosPago = Pago::registrar($datos, EstadosPago::Realizado, $request);
    $pagoProfesor = new PagoProfesor([
        "idPago" => $datosPago["id"],
        "idProfesor" => $idProfesor
    ]);
    $pagoProfesor->save();

    if ($datos["motivo"] == MotivosPago::Clases) {
      $datosClases = explode(",", $datos["datosClases"]);
      foreach ($datosClases as $datClase) {
        if (trim($datClase) == "") {
          continue;
        }
        $idClaseAlumno = explode("-", $datClase);
        PagoClase::registrar($datosPago["id"], $idClaseAlumno[1]);
      }
    }

    $listaMotivosPago = MotivosPago::listar();
    $mensajeHistorial = str_replace(["[MOTIVO]", "[DESCRIPCION]", "[MONTO]"], [$listaMotivosPago[$datos["motivo"]], "", number_format((float) ($datos["monto"]), 2, ".", "")], MensajesHistorial::MensajeProfesorRegistroPago);
    Historial::Registrar([$idProfesor, Auth::user()->idEntidad], MensajesHistorial::TituloProfesorRegistroPago, $mensajeHistorial, $datosPago["rutasImagenesComprobante"], FALSE, TRUE, $datosPago["id"], NULL, NULL, TiposHistorial::Pago);
  }

  protected static function registrarXDatosClaseCancelada($idProfesor, $idClaseCancelada, $monto) {
    $datos = ["motivo" => MotivosPago::ClaseCancelada, "monto" => $monto];
    $datosPago = Pago::registrar($datos, EstadosPago::Pendiente);
    $pagoProfesor = new PagoProfesor([
        "idPago" => $datosPago["id"],
        "idProfesor" => $idProfesor
    ]);
    $pagoProfesor->save();
    PagoClase::registrar($datosPago["id"], $idClaseCancelada);
    $mensajeHistorial = str_replace(["[MOTIVO]", "[DESCRIPCION]", "[MONTO]"], [$datos["motivo"], "", number_format((float) ($datos["monto"]), 2, ".", "")], MensajesHistorial::MensajeProfesorRegistroPago);
    Historial::Registrar([$idProfesor, Auth::user()->idEntidad], MensajesHistorial::TituloProfesorRegistroPago, $mensajeHistorial, NULL, FALSE, TRUE, $datosPago["id"], NULL, NULL, TiposHistorial::Pago);
  }
  
  protected static function actualizarEstado($idProfesor, $datos) {
    Pago::actualizarEstado($datos["idPago"], $datos["estado"]);
  }

  protected static function eliminar($idProfesor, $id) {
    PagoProfesor::obtenerXId($idProfesor, $id);
    Pago::eliminar($id);
  }

  
  protected static function verificarExistencia($idProfesor, $id) {
    try {
      PagoProfesor::obtenerXId($idProfesor, $id);
    } catch (Exception $ex) {
      return FALSE;
    }
    return TRUE;
  }
}
