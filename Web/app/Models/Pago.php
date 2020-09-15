<?php

namespace App\Models;

use DB;
use Carbon\Carbon;
use App\Helpers\Enum\EstadosPago;
use App\Helpers\Enum\MotivosPago;
use App\Helpers\Enum\EstadosClase;
use Illuminate\Database\Eloquent\Model;

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
      "pagoXHoraProfesor",
      "periodoClases"
  ];

  public static function nombreTabla() {
    $modeloPago = new Pago();
    $nombreTabla = $modeloPago->getTable();
    unset($modeloPago);
    return $nombreTabla;
  }

  public static function listarBase() {
    return Pago::where("eliminado", 0);
  }

  public static function obtenerXId($id) {
    return Pago::listarBase()->where("id", $id)->firstOrFail();
  }

  public static function listar($soloDeAlumnosVigentes = FALSE) {
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
                          AND estado IN ('" . EstadosClase::ConfirmadaProfesor . "','" . EstadosClase::ConfirmadaProfesorAlumno . "','" . EstadosClase::Realizada . "')
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
                                AND " . $nombreTablaClase . ".estado IN ('" . EstadosClase::ConfirmadaProfesor . "', '" . EstadosClase::ConfirmadaProfesorAlumno . "', '" . EstadosClase::Realizada . "')
                                AND " . $nombreTablaClase . ".eliminado = 0)");
    }
    return $pagos;
  }

  public static function registrar($datos, $estado) {
    $datos["fecha"] = (isset($datos["fecha"]) ? Carbon::createFromFormat("d/m/Y H:i:s", $datos["fecha"] . " 00:00:00") : Carbon::now());
    $pago = new Pago($datos);
    $pago->estado = $estado;
    $pago->fechaRegistro = Carbon::now()->toDateTimeString();
    $pago->save();

    Pago::registrarActualizarImagenes($pago["id"], $datos);
    return Pago::obtenerXId($pago["id"]);
  }

  public static function actualizar($id, $datos) {
    if (isset($datos["fecha"])) {
      $datos["fecha"] = Carbon::createFromFormat("d/m/Y H:i:s", $datos["fecha"] . " 00:00:00");
    }
    $pago = Pago::obtenerXId($id);
    $pago->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
    $pago->update($datos);
    Pago::registrarActualizarImagenes($id, $datos);
    return Pago::obtenerXId($id);
  }

  private static function registrarActualizarImagenes($id, $datos) {
    $pago = Pago::obtenerXId($id);
    $imagenesComprobante = Archivo::procesarArchivosSubidos($pago->imagenesComprobante, $datos, 5, "ImagenesComprobantes");
    if ($imagenesComprobante != "") {
      $pago->imagenesComprobante = $imagenesComprobante;
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
  }

}
