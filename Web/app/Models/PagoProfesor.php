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

  public static function nombreTabla() {
    $modeloPagoProfesor = new PagoProfesor();
    $nombreTabla = $modeloPagoProfesor->getTable();
    unset($modeloPagoProfesor);
    return $nombreTabla;
  }

  public static function listar($idProfesor) {
    $nombreTabla = PagoProfesor::nombreTabla();
    return PagoProfesor::leftJoin(Pago::nombreTabla() . " as pago", $nombreTabla . ".idPago", "=", "pago.id")
                    ->where("pago.eliminado", 0)
                    ->where($nombreTabla . ".idProfesor", $idProfesor);
  }

  public static function obtenerXId($idProfesor, $id) {
    return PagoProfesor::listar($idProfesor)
                    ->select("pago.*")
                    ->where("pago.id", $id)->firstOrFail();
  }

  public static function obtenerXClase($idClase) {
    $nombreTabla = PagoProfesor::nombreTabla();
    return PagoProfesor::select("pago.*")
                    ->leftJoin(Pago::nombreTabla() . " as pago", $nombreTabla . ".idPago", "=", "pago.id")
                    ->leftJoin(PagoClase::nombreTabla() . " as pagoClase", $nombreTabla . ".idPago", "=", "pagoClase.idPago")
                    ->where("pago.eliminado", 0)
                    ->where("pagoClase.idClase", $idClase)->first();
  }

  public static function registrar($idProfesor, $request) {
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
    Historial::registrar([
        "idEntidades" => [$idProfesor, Auth::user()->idEntidad],
        "titulo" => MensajesHistorial::TituloProfesorRegistroPago,
        "mensaje" => $mensajeHistorial,
        "imagenes" => $datosPago["imagenesComprobante"],
        "idPago" => $datosPago["id"],
        "tipo" => TiposHistorial::Pago
    ]);
  }

  public static function actualizarEstado($idProfesor, $datos) {
    PagoProfesor::obtenerXId($idProfesor, $datos["idPago"]);
    Pago::actualizarEstado($datos["idPago"], $datos["estado"]);
  }

  public static function verificarExistencia($idProfesor, $id) {
    try {
      PagoProfesor::obtenerXId($idProfesor, $id);
    } catch (\Exception $ex) {
      return FALSE;
    }
    return TRUE;
  }

  public static function eliminar($idProfesor, $id) {
    PagoProfesor::obtenerXId($idProfesor, $id);
    Pago::eliminar($id);
  }

}
