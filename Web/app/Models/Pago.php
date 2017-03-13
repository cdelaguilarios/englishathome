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
  protected $fillable = ["motivo", "descripcion", "monto", "imagenesComprobante", "saldoFavor", "saldoFavorUtilizado", "cuenta", "estado"];

  public static function nombreTabla() {
    $modeloPago = new Pago();
    $nombreTabla = $modeloPago->getTable();
    unset($modeloPago);
    return $nombreTabla;
  }

  public static function obtenerXId($id) {
    return Pago::where("eliminado", 0)->where("id", $id)->firstOrFail();
  }

  public static function reporte($datos) {
    $nombreTabla = Pago::nombreTabla();
    $pagos = Pago::where("eliminado", 0)
            ->select(($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::Mes ? DB::raw("MONTH(fechaRegistro) AS mes") : ($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::Anho ? DB::raw("YEAR(fechaRegistro) AS anho") : "fechaRegistro")), "estado", DB::raw("SUM(monto) AS total"))
            ->groupBy(($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::Mes ? DB::raw("MONTH(fechaRegistro)") : ($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::Anho ? DB::raw("YEAR(fechaRegistro)") : "fechaRegistro")), "estado")
            ->orderBy("fechaRegistro", "ASC");
    if (isset($datos["tipoPago"]) && $datos["tipoPago"] !== "0") {
      $pagos->whereIn("id", PagoProfesor::lists("idPago"));
    } else {
      $pagos->whereIn("id", PagoAlumno::lists("idPago"));
    }
    $datos["estado"] = $datos["estadoPago"];
    Util::filtrosBusqueda($nombreTabla, $pagos, "fechaRegistro", $datos);
    return $pagos->get();
  }

  public static function registrar($datos, $estado, $request) {
    $pago = new Pago($datos);
    $pago->estado = $estado;
    $pago->save();
    Pago::registrarActualizarImagenes($pago["id"], $request);
    return Pago::obtenerXId($pago["id"]);
  }

  public static function actualizar($id, $datos, $request) {
    $pago = Pago::obtenerXId($id);
    $pago->update($datos);
    Pago::registrarActualizarImagenes($id, $request);
  }

  private static function registrarActualizarImagenes($id, $request) {
    $pago = Pago::obtenerXId($id);
    if (isset($request)) {
      $rutaImagenesComprobantes = NULL;
      $imagenComprobantePago = $request->file("imagenComprobante");
      if (isset($imagenComprobantePago) && $imagenComprobantePago != "") {
        $rutaImagenesComprobantes = Archivo::registrar($pago["id"] . "_icp_", $imagenComprobantePago);
      }
      $imagenDocumentoVerificacion = $request->file("imagenDocumentoVerificacion");
      if (isset($imagenDocumentoVerificacion) && $imagenDocumentoVerificacion != "") {
        $rutaImagenesComprobantes .= "," . Archivo::registrar($pago["id"] . "_idv_", $imagenDocumentoVerificacion);
      }
      if (!is_null($pago->imagenesComprobante) && $pago->imagenesComprobante != "") {
        $imagenesComprobante = explode(",", $pago->imagenesComprobante);
        foreach ($imagenesComprobante as $imagenComprobante) {
          if ($imagenComprobante != "") {
            Archivo::eliminar($imagenComprobante);
          }
        }
      }
      $pago->imagenesComprobante = $rutaImagenesComprobantes;
      $pago->save();
    }
  }

  public static function actualizarEstado($id, $estado) {
    $pago = Pago::obtenerXId($id);
    $pago->estado = $estado;
    $pago->save();
  }

  public static function eliminar($id) {
    $pago = Pago::obtenerXId($id);
    $pago->eliminado = 1;
    $pago->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
    $pago->save();
  }

}
