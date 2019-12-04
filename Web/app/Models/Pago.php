<?php

namespace App\Models;

use DB;
use Carbon\Carbon;
use App\Helpers\Util;
use App\Helpers\Enum\EstadosPago;
use App\Helpers\Enum\MotivosPago;
use App\Helpers\Enum\EstadosClase;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Enum\TiposBusquedaFecha;

class Pago extends Model {

  public $timestamps = false;
  protected $table = "pago";
  protected $fillable = [
      "motivo",
      "cuenta",
      "fecha",
      "estado",
      "descripcion",
      "imagenesComprobante",
      "monto",
      "saldoFavor",
      "saldoFavorUtilizado",
      "costoXHoraClase",
      "idProfesorClases",
      "pagoXHoraProfesor",
      "periodoClases"
  ];

  public static function nombreTabla()/* - */ {
    $modeloPago = new Pago();
    $nombreTabla = $modeloPago->getTable();
    unset($modeloPago);
    return $nombreTabla;
  }

  public static function listarBase()/* - */ {
    return Pago::where(Pago::nombreTabla() . ".eliminado", 0);
  }

  public static function obtenerXId($id)/* - */ {
    return Pago::listarBase()->where("id", $id)->firstOrFail();
  }

  public static function listarNUEVO($soloDeAlumnosVigentes = FALSE)/* - */ {
    $nombreTabla = Pago::nombreTabla();
    $pagos = Pago::listarBase()
            ->select(DB::raw(
                            $nombreTabla . ".*, 
                     pagoAlumno.*, 
                    (SELECT COALESCE(SUM(duracion), 0)
                        FROM " . Clase::nombreTabla() . " 
                        WHERE id IN (SELECT idClase 
                                        FROM " . PagoClase::nombreTabla() . " 
                                        WHERE idPago = " . $nombreTabla . ".id) 
                          AND estado IN ('" . EstadosClase::Realizada . "','" . EstadosClase::ConfirmadaProfesorAlumno . "')
                          AND eliminado = 0
                    ) AS duracionXClasesRealizadas"))
            ->join(PagoAlumno::nombreTabla() . " as pagoAlumno", Pago::nombreTabla() . ".id", "=", "pagoAlumno.idPago")
            ->where("motivo", MotivosPago::Clases)
            ->where("estado", EstadosPago::Realizado)
            ->orderBy("fecha", "DESC");

    if ($soloDeAlumnosVigentes) {
      $nombreTablaClase = Clase::nombreTabla();
      $nombreTablaPagoClase = PagoClase::nombreTabla();
      //La duración total por clases pagadas debe ser mayor a la suma de duraciones de las clases relacionadas con el pago, 
      //en caso contrario se determina que se ha utilizado todas las horas de la bolsa
      $pagos->whereRaw("(((" . $nombreTabla . ".monto/" . $nombreTabla . ".costoXHoraClase) - ((" . $nombreTabla . ".monto/" . $nombreTabla . ".costoXHoraClase)%0.5))*3600) 
                          > (SELECT COALESCE(SUM(" . $nombreTablaClase . ".duracion), 0) 
                              FROM " . $nombreTablaClase . "
                              WHERE " . $nombreTablaClase . ".Id IN (SELECT idClase 
                                                  FROM " . $nombreTablaPagoClase . "
                                                  WHERE " . $nombreTablaPagoClase . ".idPago = " . $nombreTabla . ".id) 
                                AND " . $nombreTablaClase . ".estado IN ('" . EstadosClase::ConfirmadaProfesorAlumno . "', '" . EstadosClase::Realizada . "')
                                AND " . $nombreTablaClase . ".eliminado = 0)");
    }
    return $pagos;
  }

  public static function listarXIdProfesorClases($idProfesorClases, $soloDeAlumnosVigentes = FALSE)/* - */ {
    return Pago::listarNUEVO($soloDeAlumnosVigentes)->where("idProfesorClases", $idProfesorClases);
  }

  public static function registrar($datos, $estado, $request)/* - */ {
    $datos["fecha"] = (isset($datos["fecha"]) ? Carbon::createFromFormat("d/m/Y H:i:s", $datos["fecha"] . " 00:00:00") : Carbon::now());
    $pago = new Pago($datos);
    $pago->estado = $estado;
    $pago->fechaRegistro = Carbon::now()->toDateTimeString();
    $pago->save();

    Pago::registrarActualizarImagenes($pago["id"], $request);
    return Pago::obtenerXId($pago["id"]);
  }

  public static function actualizar($id, $datos, $request)/* - */ {
    if (isset($datos["fecha"])) {
      $datos["fecha"] = Carbon::createFromFormat("d/m/Y H:i:s", $datos["fecha"] . " 00:00:00");
    }
    $pago = Pago::obtenerXId($id);
    $pago->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
    $pago->update($datos);
    Pago::registrarActualizarImagenes($id, $request);
    return Pago::obtenerXId($id);
  }

  private static function registrarActualizarImagenes($id, $req)/* - */ {
    if (!isset($req)) {
      return;
    }
    $pago = Pago::obtenerXId($id);
    $imagenesComprobante = (isset($pago->imagenesComprobante) ? $pago->imagenesComprobante : "");
    $nombreImagenComprobante = explode(",", $imagenesComprobante)[0];

    $nuevaImagenComprobante = $req->file("imagenComprobante");
    if (isset($nuevaImagenComprobante) && $nuevaImagenComprobante != "") {
      $nombreNuevaImagenComprobante = Archivo::registrar($pago["id"] . "_icp_", $nuevaImagenComprobante);
      if ($nombreImagenComprobante != "") {
        Archivo::eliminar($nombreImagenComprobante);
        $imagenesComprobante = str_replace($nombreImagenComprobante . ",", "", $imagenesComprobante);
      }
      $nombreImagenComprobante = $nombreNuevaImagenComprobante;
    }

    //TODO: Falta validar el procesamiento de archivos
    /*$nombresImagenesDocumentosVerificacion = Archivo::procesarArchivosSubidos($imagenesComprobante, $request->all(), 20, "nombresDocumentosVerificacion", "nombresDocumentosVerificacion", "nombresDocumentosVerificacionEliminados");*/
    if ($nombreImagenComprobante != "") {
      $pago->imagenesComprobante = $nombreImagenComprobante;
      $pago->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
      $pago->save();
    }
  }

  public static function actualizarEstado($id, $estado)/* - */ {
    $pago = Pago::obtenerXId($id);
    $pago->estado = $estado;
    $pago->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
    $pago->save();
  }

  public static function eliminar($id)/* - */ {
    $pago = Pago::obtenerXId($id);
    $pago->eliminado = 1;
    $pago->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
    $pago->save();
    PagoClase::eliminarXIdPago($id);
  }

  // <editor-fold desc="TODO: ELIMINAR">
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
            ->select((($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::Mes || $datos["tipoBusquedaFecha"] == TiposBusquedaFecha::RangoMeses) ? DB::raw("MONTH(fecha) AS mes") : (($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::Anio || $datos["tipoBusquedaFecha"] == TiposBusquedaFecha::RangoAnios) ? DB::raw("YEAR(fecha) AS anho") : "fecha")), "estado", DB::raw("SUM(monto) AS total"))
            ->groupBy((($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::Mes || $datos["tipoBusquedaFecha"] == TiposBusquedaFecha::RangoMeses) ? DB::raw("MONTH(fecha)") : (($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::Anio || $datos["tipoBusquedaFecha"] == TiposBusquedaFecha::RangoAnios) ? DB::raw("YEAR(fecha)") : "fecha")), "estado")
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

  // </editor-fold>
}
