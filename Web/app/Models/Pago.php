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
    Util::filtrosBusqueda($nombreTabla, $pagos, "fechaRegistro", $datos);
    return $pagos;
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
    if (isset($datos["ids"]) && is_array($datos["ids"])) {
      return $pagos->whereIn("id", $datos["ids"])->get();
    } else {
      return [];
    }
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
    return Pago::obtenerXId($id);
  }

  private static function registrarActualizarImagenes($id, $request) {
    $pago = Pago::obtenerXId($id);
    if (isset($request)) {
      $imagenesAnt = ((!is_null($pago->imagenesComprobante) && $pago->imagenesComprobante != "") ? $pago->imagenesComprobante : NULL);
      $rutaImagenComprobante = $rutaImagenComprobanteAnt = $rutaImagenDocumentoVerificacion = $rutaImagenDocumentoVerificacionAnt = "";

      $imagenComprobantePago = $request->file("imagenComprobante");
      if (isset($imagenComprobantePago) && $imagenComprobantePago != "") {
        $rutaImagenComprobante = Archivo::registrar($pago["id"] . "_icp_", $imagenComprobantePago);
      }
      $imagenDocumentoVerificacion = $request->file("imagenDocumentoVerificacion");
      if (isset($imagenDocumentoVerificacion) && $imagenDocumentoVerificacion != "") {
        $rutaImagenDocumentoVerificacion = Archivo::registrar($pago["id"] . "_idv_", $imagenDocumentoVerificacion);
      }

      if (!is_null($imagenesAnt)) {
        $imagenesComprobante = explode(",", $imagenesAnt);
        if (count($imagenesComprobante) == 2) {
          $rutaImagenComprobanteAnt = $imagenesComprobante[0];
          $rutaImagenDocumentoVerificacionAnt = $imagenesComprobante[1];
        } else {
          $rutaImagenComprobanteAnt = $imagenesAnt;
        }
      }

      if ($rutaImagenComprobante != "") {
        if ($rutaImagenComprobanteAnt != "") {
          Archivo::eliminar($rutaImagenComprobanteAnt);
        }
      } else {
        $rutaImagenComprobante = $rutaImagenComprobanteAnt;
      }

      if ($rutaImagenDocumentoVerificacion != "") {
        if ($rutaImagenDocumentoVerificacionAnt != "") {
          Archivo::eliminar($rutaImagenDocumentoVerificacionAnt);
        }
      } else {
        $rutaImagenDocumentoVerificacion = $rutaImagenDocumentoVerificacionAnt;
      }
      $pago->imagenesComprobante = $rutaImagenComprobante . ($rutaImagenDocumentoVerificacion != "" ? "," . $rutaImagenDocumentoVerificacion : "");
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
    PagoClase::eliminarXIdPago($id);
  }

}
