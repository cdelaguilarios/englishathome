<?php

namespace App\Models;

use DB;
use Carbon\Carbon;
use App\Helpers\Util;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Enum\TiposBusquedaFecha;

class Pago extends Model {

  public $timestamps = false;
  protected $table = "pago";
  protected $fillable = ["motivo", "descripcion", "monto", "imagenesComprobante", "saldoFavor", "saldoFavorUtilizado", "costoHoraClaseBase", "cuenta", "estado", "fecha"];

  public static function nombreTabla() {
    $modeloPago = new Pago();
    $nombreTabla = $modeloPago->getTable();
    unset($modeloPago);
    return $nombreTabla;
  }

  public static function obtenerXId($id) {
    return Pago::where("eliminado", 0)->where("id", $id)->firstOrFail();
  }

  public static function listar($datos = NULL) {
    $nombreTabla = Pago::nombreTabla();
    $pagos = Pago::where($nombreTabla . ".eliminado", 0)
            ->select($nombreTabla . ".*", "entidad.id AS idEntidad", "entidad.nombre AS nombreEntidad", "entidad.apellido AS apellidoEntidad", DB::raw(((isset($datos["tipoPago"]) && $datos["tipoPago"] !== "0") ? 1 : 0) . " AS esEntidadProfesor"));
    if (isset($datos["tipoPago"]) && $datos["tipoPago"] !== "0") {
      $pagos->leftJoin(PagoProfesor::nombreTabla() . " as pagoProfesor", "pagoProfesor.idPago", "=", $nombreTabla . ".id")
              ->leftJoin(Entidad::NombreTabla() . " as entidad", "entidad.id", "=", "pagoProfesor.idProfesor")
              ->whereIn($nombreTabla . ".id", PagoProfesor::lists("idPago"));
    } else {
      $pagos->leftJoin(PagoAlumno::nombreTabla() . " as pagoAlumno", "pagoAlumno.idPago", "=", $nombreTabla . ".id")
              ->leftJoin(Entidad::NombreTabla() . " as entidad", "entidad.id", "=", "pagoAlumno.idAlumno")
              ->whereIn($nombreTabla . ".id", PagoAlumno::lists("idPago"));
    }
    $datos["estado"] = $datos["estadoPago"];
    Util::filtrosBusqueda($nombreTabla, $pagos, "fecha", $datos);
    return $pagos;
  }

  public static function reporte($datos) {
    $pagos = Pago::where("eliminado", 0)
            ->select((($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::Mes || $datos["tipoBusquedaFecha"] == TiposBusquedaFecha::RangoMeses) ? DB::raw("MONTH(fecha) AS mes") : (($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::Anho || $datos["tipoBusquedaFecha"] == TiposBusquedaFecha::RangoAnhos) ? DB::raw("YEAR(fecha) AS anho") : "fecha")), "estado", DB::raw("SUM(monto) AS total"))
            ->groupBy((($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::Mes || $datos["tipoBusquedaFecha"] == TiposBusquedaFecha::RangoMeses) ? DB::raw("MONTH(fecha)") : (($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::Anho || $datos["tipoBusquedaFecha"] == TiposBusquedaFecha::RangoAnhos) ? DB::raw("YEAR(fecha)") : "fecha")), "estado")
            ->orderBy("fecha", "ASC");
    if (isset($datos["tipoPago"]) && $datos["tipoPago"] !== "0") {
      $pagos->whereIn("id", PagoProfesor::lists("idPago"));
    } else {
      $pagos->whereIn("id", PagoAlumno::lists("idPago"));
    }
    if (isset($datos["ids"]) && is_array($datos["ids"])) {
      return $pagos->whereIn("id", $datos["ids"])->get();
    } else {
      return [];
    }
  }

  public static function registrar($datos, $estado, $request) {
    $datos["fecha"] = (isset($datos["fecha"]) ? Carbon::createFromFormat("d/m/Y H:i:s", $datos["fecha"] . " 00:00:00") : Carbon::now());
    $pago = new Pago($datos);
    $pago->estado = $estado;
    $pago->fechaRegistro = Carbon::now()->toDateTimeString();
    
    //TODO: Realizar las validaciones necesarias
    if (isset($datos["registrarSinGenerarClases"]) && isset($datos["costoHoraClase"]) && (int) $datos["registrarSinGenerarClases"] == 0) {
      $pago->costoHoraClaseBase = $datos["costoHoraClase"];
    }
    
    $pago->save();
    Pago::registrarActualizarImagenes($pago["id"], $request);
    return Pago::obtenerXId($pago["id"]);
  }

  public static function actualizar($id, $datos, $request) {
    if (isset($datos["fecha"])) {
      $datos["fecha"] = Carbon::createFromFormat("d/m/Y H:i:s", $datos["fecha"] . " 00:00:00");
    }
    $pago = Pago::obtenerXId($id);
    $pago->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
    $pago->update($datos);
    Pago::registrarActualizarImagenes($id, $request);
    return Pago::obtenerXId($id);
  }

  private static function registrarActualizarImagenes($id, $request) {
    if (!isset($request)) {
      return;
    }
    $pago = Pago::obtenerXId($id);
    $imagenesComprobante = (isset($pago->imagenesComprobante) ? $pago->imagenesComprobante : "");
    $nombreImagenComprobante = explode(",", $imagenesComprobante)[0];

    $imagenComprobantePago = $request->file("imagenComprobante");
    if (isset($imagenComprobantePago) && $imagenComprobantePago != "") {
      $nombreNuevaImagenComprobante = Archivo::registrar($pago["id"] . "_icp_", $imagenComprobantePago);
      if ($nombreImagenComprobante != "") {
        Archivo::eliminar($nombreImagenComprobante);
        $imagenesComprobante = str_replace($nombreImagenComprobante . ",", "", $imagenesComprobante);
      }
      $nombreImagenComprobante = $nombreNuevaImagenComprobante;
    }

    $nombresImagenesDocumentosVerificacion = Archivo::procesarArchivosSubidos($imagenesComprobante, $request->all(), 20, "nombresDocumentosVerificacion", "nombresDocumentosVerificacion", "nombresDocumentosVerificacionEliminados");
    if ($nombreImagenComprobante != "" || $nombresImagenesDocumentosVerificacion != "") {
      $pago->imagenesComprobante = $nombreImagenComprobante . "," . $nombresImagenesDocumentosVerificacion;
      $pago->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
      $pago->save();
    }
  }

  public static function actualizarEstado($id, $estado) {
    $pago = Pago::obtenerXId($id);
    $pago->estado = $estado;
    $pago->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
    $pago->save();
  }

  public static function eliminar($id) {
    $pago = Pago::obtenerXId($id);
    $pago->eliminado = 1;
    $pago->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
    $pago->save();
    PagoClase::eliminarXIdPago($id);
  }

}
