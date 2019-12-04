<?php

namespace App\Models;

use DB;
use Log;
use Auth;
use App\Helpers\Util;
use App\Helpers\Enum\MotivosPago;
use App\Helpers\Enum\EstadosClase;
use App\Helpers\Enum\EstadosPago;
use App\Helpers\Enum\TiposHistorial;
use App\Helpers\Enum\MensajesHistorial;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PagoProfesor extends Model {

  public $timestamps = false;
  protected $table = "pagoProfesor";
  protected $fillable = [
      "idProfesor",
      "idPago"
  ];

  public static function nombreTabla()/* - */ {
    $modeloPagoProfesor = new PagoProfesor();
    $nombreTabla = $modeloPagoProfesor->getTable();
    unset($modeloPagoProfesor);
    return $nombreTabla;
  }

  public static function listarBase()/* - */ {
    return PagoProfesor::leftJoin(Pago::nombreTabla() . " as pago", PagoProfesor::nombreTabla() . ".idPago", "=", "pago.id")
                    ->where("pago.eliminado", 0);
  }

  public static function listar($idProfesor)/* - */ {
    $nombreTabla = PagoProfesor::nombreTabla();
    $pagosProfesor = PagoProfesor::listarBase()
            ->where($nombreTabla . ".idProfesor", $idProfesor)
            ->groupBy("pago.id");

    //Clases asociadas
    $nombreTablaClase = Clase::nombreTabla();
    $pagosProfesor->leftJoin($nombreTablaClase . " AS clase", function ($q) {
      $nombreTablaPagoClase = PagoClase::nombreTabla();
      $q->on("clase.id", "IN", DB::raw("(SELECT idClase 
                                                      FROM " . $nombreTablaPagoClase . "
                                                      WHERE idPago = pago.id)"))
              ->whereIn("clase.estado", [EstadosClase::ConfirmadaProfesorAlumno, EstadosClase::Realizada])
              ->where("clase.eliminado", "=", 0);
    });

    $pagosProfesor->select(DB::raw(
                    "pago.*,  
                    SUM(clase.duracion) AS duracionTotalXClases")
    );

    return DB::table(DB::raw("({$pagosProfesor->toSql()}) AS T"))
                    ->mergeBindings($pagosProfesor->getQuery())
                    ->select(DB::raw("T.*")
    );
  }

  public static function obtenerXId($idProfesor, $id)/* - */ {
    $datosPago = PagoProfesor::listar($idProfesor)->where("id", $id)->first();
    if (!isset($datosPago)) {
      throw ModelNotFoundException;
    }
    return $datosPago;
  }

  // <editor-fold desc="Pagos generales/otros">
  public static function registrarGeneral($idProfesor, $req)/* - */ {
    //Pago general es del tipo "OTROS"
    $datos = $req->all();
    $datos["motivo"] = MotivosPago::Otros;

    $datosPago = Pago::registrar($datos, $datos["estado"], $req);
    $pagoProfesor = new PagoProfesor([
        "idPago" => $datosPago["id"],
        "idProfesor" => $idProfesor
    ]);
    $pagoProfesor->save();
    PagoProfesor::registrarActualizarEvento($idProfesor, $datosPago);
  }

  public static function actualizarGeneral($idProfesor, $req)/* - */ {
    //Pago general es del tipo "OTROS"
    $datos = $req->all();
    $datos["motivo"] = MotivosPago::Otros;

    if (PagoProfesor::verificarExistencia($idProfesor, $datos["idPago"])) {
      $pago = Pago::obtenerXId($datos["idPago"]);
      if ($pago->motivo == MotivosPago::Otros) {
        $datosPago = Pago::actualizar($datos["idPago"], $datos, $req);
        PagoProfesor::registrarActualizarEvento($idProfesor, $datosPago);
      }
    }
  }

  public static function actualizarEstadoGeneral($idProfesor, $id, $datos)/* - */ {
    //Pago general es del tipo "OTROS"
    if (PagoProfesor::verificarExistencia($idProfesor, $id)) {
      $pago = Pago::obtenerXId($id);
      if ($pago->motivo == MotivosPago::Otros) {
        Pago::actualizarEstado($id, $datos["estado"]);
      }
    }
  }

  // </editor-fold>
  // <editor-fold desc="Pagos por clases">
  public static function listarXClasesBase($datos) {
    $nombreTablaClase = Clase::nombreTabla();

    $clases = Clase::listarBase(TRUE);
    $clases->leftJoin(EntidadCuentaBancaria::nombreTabla() . " as cuentaBancariaProfesor", $nombreTablaClase . ".idProfesor", "=", "cuentaBancariaProfesor.idEntidad");
    if ($datos["estadoPago"] == EstadosPago::Realizado) {
      $clases->whereRaw("pagoProfesor.id IS NOT NULL");
    } else if ($datos["estadoPago"] == EstadosPago::Pendiente) {
      $clases->whereRaw("pagoProfesor.id IS NULL");
    }
    $clases->whereNotNull("entidadProfesor.id");
    Util::aplicarFiltrosBusquedaXFechas($clases, $nombreTablaClase, "fechaConfirmacion", $datos);

    return $clases->select(DB::raw(
                            "pagoProfesor.id AS idPago,
                            pagoProfesor.fecha AS fechaPago,
                            pagoProfesor.descripcion AS descripcionPago,
                            pagoProfesor.imagenesComprobante AS imagenesComprobantePago,
                            (CASE WHEN pagoProfesor.id IS NULL 
                              THEN '" . EstadosPago::Pendiente . "'
                              ELSE '" . EstadosPago::Realizado . "'
                            END) AS estadoPago,"
                            . $nombreTablaClase . ".*,                            
                            CONCAT(entidadAlumno.nombre, ' ', entidadAlumno.apellido) AS alumno, 
                            CONCAT(entidadProfesor.nombre, ' ', entidadProfesor.apellido) AS profesor,
                            GROUP_CONCAT(
                              DISTINCT CONCAT(cuentaBancariaProfesor.banco, '|', cuentaBancariaProfesor.numeroCuenta) 
                              SEPARATOR ';'
                            ) AS cuentasBancariasProfesor")
    );
  }

  public static function listarXClases($datos)/* - */ {
    $clases = PagoProfesor::listarXClasesBase($datos);

    return DB::table(DB::raw("({$clases->toSql()}) AS T"))
                    ->mergeBindings($clases->getQuery())
                    ->select(DB::raw(
                                    "T.idPago,
                                     T.fechaPago,
                                     T.descripcionPago,
                                     T.imagenesComprobantePago,
                                     T.estadoPago,
                                     T.idProfesor, 
                                     T.profesor, 
                                     T.cuentasBancariasProfesor, 
                                     COUNT(T.id) AS numeroTotalClases,
                                     SUM(T.duracion) AS duracionTotalClases,
                                     SUM(T.costoHoraProfesor * (T.duracion/3600))/SUM(T.duracion/3600) AS costoHoraPromedioProfesor,
                                     SUM(T.costoHoraProfesor * (T.duracion/3600)) AS montoTotalXClases")
                    )->groupBy("T.idPago", "T.idProfesor");
  }

  public static function listarXClasesDetalle($idProfesor, $datos) {
    $clases = PagoProfesor::listarXClasesBase($datos);
    return DB::table(DB::raw("({$clases->toSql()}) AS T"))
                    ->mergeBindings($clases->getQuery())
                    ->select(DB::raw(
                                    "T.*,
                                    (T.costoHoraProfesor * (T.duracion/3600)) AS pagoTotalFinalProfesor")
                    )->where("T.idProfesor", $idProfesor);
  }

  public static function registrarActualizarXClases($idProfesor, $req)/* - */ {
    $datos = $req->all();

    $datosPagoIni = PagoProfesor::listarXClases($datos)->where("idProfesor", $idProfesor)->first();
    if ($datosPagoIni != null) {
      $datos["motivo"] = MotivosPago::Clases;
      $datos["monto"] = $datosPagoIni->montoTotalXClases;

      if (!(isset($datos["idPago"]) && $datos["idPago"] != "")) {
        //Registro
        $datos["imagenesComprobante"] = Archivo::procesarArchivosSubidosNUEVO("", $datos, 5, "ImagenesComprobantes");
        $datosPago = Pago::registrar($datos, EstadosPago::Realizado, null);
        $pagoProfesor = new PagoProfesor([
            "idPago" => $datosPago["id"],
            "idProfesor" => $idProfesor
        ]);
        $pagoProfesor->save();

        $clases = PagoProfesor::listarXClasesDetalle($idProfesor, $datos)->get();
        foreach ($clases as $clase) {
          $pagoClase = new PagoClase([
              "idPago" => $datosPago["id"],
              "idClase" => $clase->id
          ]);
          $pagoClase->save();
        }
      } else {
        //Actualización
        $pago = Pago::obtenerXId($datos["idPago"]);
        $datos["imagenesComprobante"] = Archivo::procesarArchivosSubidosNUEVO($pago->imagenesComprobante, $datos, 5, "ImagenesComprobantes");
        $datosPago = Pago::actualizar($datos["idPago"], $datos, null);
      }

      PagoProfesor::registrarActualizarEvento($idProfesor, $datosPago);
    }
  }

  // </editor-fold>

  public static function verificarExistencia($idProfesor, $id)/* - */ {
    try {
      PagoProfesor::obtenerXId($idProfesor, $id);
    } catch (\Exception $e) {
      Log::error($e);
      return FALSE;
    }
    return TRUE;
  }

  public static function eliminar($idProfesor, $id)/* - */ {
    if (PagoProfesor::verificarExistencia($idProfesor, $id)) {
      Pago::eliminar($id);
      PagoProfesor::where("idProfesor", $idProfesor)->where("idPago", $id)->delete();
    }
  }

  //Util
  private static function registrarActualizarEvento($idProfesor, $datosPago)/* - */ {
    $listaMotivosPago = MotivosPago::listar();
    $motivo = $listaMotivosPago[$datosPago["motivo"]];
    $descripcion = (isset($datosPago["descripcion"]) && $datosPago["descripcion"] != "" ? "<br/><strong>Descripción:</strong> " . $datosPago["descripcion"] : "");
    $monto = number_format((float) ($datosPago["monto"]), 2, ".", "");
    $mensajeHistorial = str_replace(["[MOTIVO]", "[DESCRIPCION]", "[MONTO]"], [$motivo, $descripcion, $monto], MensajesHistorial::MensajeProfesorRegistroPago);

    $datos = [
        "idEntidades" => [$idProfesor, Auth::user()->idEntidad],
        "titulo" => MensajesHistorial::TituloProfesorRegistroPago,
        "mensaje" => $mensajeHistorial,
        "imagenes" => $datosPago["imagenesComprobante"],
        "idPago" => $datosPago["id"],
        "tipo" => TiposHistorial::Pago
    ];

    $historial = Historial::where("eliminado", 0)->where("idPago", $datosPago["id"])->first();
    if (isset($historial)) {
      Historial::actualizar($historial->id, $datos);
    } else {
      Historial::registrar($datos);
    }
  }

  // <editor-fold desc="TODO: ELIMINAR">
  public static function obtenerXClase($idClase) {
    $nombreTabla = PagoProfesor::nombreTabla();
    return PagoProfesor::select("pago.*")
                    ->leftJoin(Pago::nombreTabla() . " as pago", $nombreTabla . ".idPago", "=", "pago.id")
                    ->leftJoin(PagoClase::nombreTabla() . " as pagoClase", $nombreTabla . ".idPago", "=", "pagoClase.idPago")
                    ->where("pago.eliminado", 0)
                    ->where("pagoClase.idClase", $idClase)->first();
  }

  // </editor-fold>
}
