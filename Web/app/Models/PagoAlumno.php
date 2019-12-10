<?php

namespace App\Models;

use DB;
use Log;
use Auth;
use App\Helpers\Enum\EstadosPago;
use App\Helpers\Enum\MotivosPago;
use App\Helpers\Enum\EstadosClase;
use App\Helpers\Enum\EstadosAlumno;
use App\Helpers\Enum\TiposHistorial;
use App\Helpers\Enum\EstadosInteresado;
use App\Helpers\Enum\MensajesHistorial;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PagoAlumno extends Model {

  public $timestamps = false;
  protected $table = "pagoAlumno";
  protected $fillable = [
      "idAlumno",
      "idPago"
  ];

  public static function nombreTabla()/* - */ {
    $modeloPagoAlumno = new PagoAlumno();
    $nombreTabla = $modeloPagoAlumno->getTable();
    unset($modeloPagoAlumno);
    return $nombreTabla;
  }

  public static function listarBase()/* - */ {
    return PagoAlumno::leftJoin(Pago::nombreTabla() . " as pago", PagoAlumno::nombreTabla() . ".idPago", "=", "pago.id")
                    ->where("pago.eliminado", 0);
  }

  public static function listar($idAlumno)/* - */ {
    $nombreTablaPagoAlumno = PagoAlumno::nombreTabla();
    $nombreTablaPagoClase = PagoClase::nombreTabla();
    $nombreTablaClase = Clase::nombreTabla();

    $pagosAlumno = PagoAlumno::listarBase()
            ->where($nombreTablaPagoAlumno . ".idAlumno", $idAlumno)
            ->groupBy("pago.id");

    //Pagos-Clases asociadas 
    $pagosAlumno->leftJoin($nombreTablaPagoClase . " AS pagoClase", function ($q) {
      $q->on("pagoClase.idPago", "=", "pago.id");
    });

    //Clases asociadas
    $pagosAlumno->leftJoin($nombreTablaClase . " AS clase", function ($q) {
      $q->on("clase.id", "=", "pagoClase.idClase")
              ->on("clase.eliminado", "=", DB::raw("0"));
    });

    $queryNumeroClasesCanceladas = "SUM(CASE WHEN clase.estado = '" . EstadosClase::Cancelada . "' THEN 1 ELSE 0 END)";
    $queryMontoXClasesCanceladas = "SUM(CASE WHEN clase.estado = '" . EstadosClase::Cancelada . "'
                                            THEN IFNULL(pago.costoXHoraClase, 0)
                                            ELSE 0 
                                          END)";
    $queryDuracionXClasesRealizadas = "SUM(CASE WHEN clase.estado = '" . EstadosClase::ConfirmadaProfesorAlumno . "' OR clase.estado = '" . EstadosClase::Realizada . "' 
                                            THEN pagoClase.duracionCubierta 
                                            ELSE 0 
                                          END)";
    $queryMontoXClasesRealizadas = "SUM(CASE WHEN clase.estado = '" . EstadosClase::ConfirmadaProfesorAlumno . "' OR clase.estado = '" . EstadosClase::Realizada . "' 
                                            THEN (pagoClase.duracionCubierta/3600) * IFNULL(pago.costoXHoraClase, 0)
                                            ELSE 0 
                                          END)";

    //Nota: cada clase cancelada consume 1 hora del pago
    $pagosAlumno->select(DB::raw(
                    "pago.*,                    
                    " . $queryNumeroClasesCanceladas . " AS numeroClasesCanceladas,
                    " . $queryNumeroClasesCanceladas . " * 3600 AS duracionTotalXClasesCanceladas,
                    " . $queryMontoXClasesCanceladas . " AS montoTotalXClasesCanceladas,
                    (CASE WHEN IFNULL(pago.costoXHoraClase, 0) > 0 
                      THEN ROUND(((IFNULL(pago.monto, 0) - IFNULL(pago.saldoFavor, 0))  / (pago.costoXHoraClase)), 6) * 3600
                      ELSE 0
                    END) AS duracionTotalXClases,
                    " . $queryDuracionXClasesRealizadas . " AS duracionTotalXClasesRealizadas,
                    (IFNULL(pago.monto, 0) - IFNULL(pago.saldoFavor, 0)) AS montoTotalXClases,
                    ROUND(" . $queryMontoXClasesRealizadas . ", 6) AS montoTotalXClasesRealizadas")
    );

    return DB::table(DB::raw("({$pagosAlumno->toSql()}) AS T"))
                    ->mergeBindings($pagosAlumno->getQuery())
                    ->select(DB::raw(
                                    "T.*,
                                      (CASE WHEN duracionTotalXClases > (duracionTotalXClasesRealizadas + duracionTotalXClasesCanceladas) 
                                        THEN duracionTotalXClases - (duracionTotalXClasesRealizadas + duracionTotalXClasesCanceladas)
                                        ELSE 0
                                      END) AS duracionTotalXClasesPendientes,
                                      (CASE WHEN duracionTotalXClases < (duracionTotalXClasesRealizadas + duracionTotalXClasesCanceladas) 
                                        THEN duracionTotalXClasesRealizadas + duracionTotalXClasesCanceladas - duracionTotalXClases
                                        ELSE 0
                                      END) AS duracionTotalXClasesNoPagadas,
                                      ((duracionTotalXClasesRealizadas + duracionTotalXClasesCanceladas)*100/duracionTotalXClases) AS porcentajeAvanceXClases,
                                      (CASE WHEN montoTotalXClases > (montoTotalXClasesRealizadas + montoTotalXClasesCanceladas)
                                        THEN montoTotalXClases - (montoTotalXClasesRealizadas + montoTotalXClasesCanceladas)
                                        ELSE 0
                                      END) AS montoTotalXClasesPendientes,
                                      (CASE WHEN montoTotalXClases < (montoTotalXClasesRealizadas + montoTotalXClasesCanceladas) 
                                        THEN montoTotalXClasesRealizadas + montoTotalXClasesCanceladas - montoTotalXClases
                                        ELSE 0
                                      END) AS montoTotalXClasesNoPagadas")
    );
  }

  public static function listarXBolsaHoras($idAlumno)/* - */ {
    $nombreTablaAlumnoBolsaHoras = AlumnoBolsaHoras::nombreTabla();
    return PagoAlumno::listar($idAlumno)
                    ->where("estado", EstadosPago::Realizado)
                    ->whereRaw("id IN (SELECT idPago 
                                          FROM " . $nombreTablaAlumnoBolsaHoras . "
                                          WHERE idAlumno = " . $idAlumno . ")")
                    ->orderBy("fecha", "asc");
  }

  public static function obtenerXId($idAlumno, $id)/* - */ {
    $datosPago = PagoAlumno::listar($idAlumno)->where("id", $id)->first();
    if (!isset($datosPago)) {
      throw new ModelNotFoundException;
    }
    return $datosPago;
  }

  public static function registrarActualizar($idAlumno, $req)/* - */ {
    $datos = $req->all();

    $datos["saldoFavor"] = 0;
    if ($datos["motivo"] == MotivosPago::Clases) {
      $preHorasPagadas = ((float) $datos["monto"] / (float) $datos["costoXHoraClase"]);
      $horasPagadas = ($preHorasPagadas - fmod($preHorasPagadas, 0.5));
      $datos["saldoFavor"] = ((float) $datos["monto"] - ($horasPagadas * (float) $datos["costoXHoraClase"]));
    }

    if (!(isset($datos["idPago"]) && $datos["idPago"] != "")) {
      //Registro
      $datosPago = Pago::registrar($datos, $datos["estado"], $req);
      $pagoAlumno = new PagoAlumno([
          "idPago" => $datosPago["id"],
          "idAlumno" => $idAlumno
      ]);
      $pagoAlumno->save();


      if ($datos["motivo"] == MotivosPago::Clases) {
        Alumno::actualizarEstado($idAlumno, EstadosAlumno::Activo);
        $bolsaHoras = new AlumnoBolsaHoras([
            "idAlumno" => $idAlumno,
            "idPago" => $datosPago["id"]
        ]);
        $bolsaHoras->save();
      }
    } else {
      //Actualización
      $datosPago = Pago::actualizar($datos["idPago"], $datos, $req);
    }

    if ($datos["usarSaldoFavor"] == 1) {
      Pago::whereIn("id", function($q) use ($idAlumno) {
                $q->select("idPago")->from(PagoAlumno::nombreTabla())->where("idAlumno", $idAlumno);
              })->where("eliminado", 0)
              ->where("id", "!=", $datosPago["id"])
              ->update(["saldoFavorUtilizado" => 1]);
    }

    if ($datos["motivo"] == MotivosPago::Clases) {
      $interesado = Interesado::obtenerXIdAlumno($idAlumno);
      if (isset($interesado)) {
        Interesado::actualizarEstado($interesado->id, EstadosInteresado::AlumnoRegistrado);
      }
    }
    PagoAlumno::registrarActualizarEvento($idAlumno, $datosPago);
  }

  public static function actualizarEstado($idAlumno, $id, $datos)/* - */ {
    if (PagoAlumno::verificarExistencia($idAlumno, $id)) {
      Pago::actualizarEstado($id, $datos["estado"]);
    }
  }

  public static function verificarExistencia($idAlumno, $id)/* - */ {
    try {
      PagoAlumno::obtenerXId($idAlumno, $id);
    } catch (\Exception $e) {
      Log::error($e);
      return FALSE;
    }
    return TRUE;
  }

  public static function eliminar($idAlumno, $id)/* - */ {
    if (PagoAlumno::verificarExistencia($idAlumno, $id)) {
      Clase::eliminadXIdPago($idAlumno, $id);
      Pago::eliminar($id);
      AlumnoBolsaHoras::where("idAlumno", $idAlumno)->where("idPago", $id)->delete();
      //TODO: Falta validar
    }
  }

  //Util
  private static function registrarActualizarEvento($idAlumno, $datosPago)/* - */ {
    //TODO: El historial de eventos y tareas va a cambiar
    $listaMotivosPago = MotivosPago::listar();
    $motivo = $listaMotivosPago[$datosPago["motivo"]];
    $descripcion = (isset($datosPago["descripcion"]) && $datosPago["descripcion"] != "" ? "<br/><strong>Descripción:</strong> " . $datosPago["descripcion"] : "");
    $monto = number_format((float) ($datosPago["monto"]), 2, ".", "");
    $mensajeHistorial = str_replace(["[MOTIVO]", "[DESCRIPCION]", "[MONTO]"], [$motivo, $descripcion, $monto], MensajesHistorial::MensajeAlumnoRegistroPago);

    $datos = [
        "idEntidades" => [$idAlumno, Auth::user()->idEntidad],
        "titulo" => MensajesHistorial::TituloAlumnoRegistroPago,
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
  public static function obtenerXClase($idAlumno, $idClase) {
    $nombreTabla = PagoAlumno::nombreTabla();
    return PagoAlumno::listar($idAlumno)->select("pago.*")
                    ->leftJoin(PagoClase::nombreTabla() . " as pagoClase", $nombreTabla . ".idPago", "=", "pagoClase.idPago")
                    ->where("pagoClase.idClase", $idClase)->first();
  }

  public static function obtenerTiemposClasesXId($idAlumno, $id) {
    $datosPago = PagoAlumno::obtenerXId($idAlumno, $id);
    if (isset($datosPago) && $datosPago->motivo == MotivosPago::Clases) {
      $montoTotal = (!is_null($datosPago->monto) ? (float) $datosPago->monto : 0);
      $saldoFavor = (!is_null($datosPago->saldoFavor) ? (float) $datosPago->saldoFavor : 0);
      $montoTotalClases = ($montoTotal - $saldoFavor);
      $costoXHoraClase = (!is_null($datosPago->costoXHoraClase) ? (float) $datosPago->costoXHoraClase : 0);

      $duracionTotal = ($costoXHoraClase > 0 ? (($montoTotalClases / $costoXHoraClase) * 3600) : 0);
      $duracionRealizada = (!is_null($datosPago->duracionTotalXClasesRealizadas) ? (float) explode("-", $datosPago->duracionTotalXClasesRealizadas)[0] : 0);
      $duracionPendiente = ($duracionTotal - $duracionRealizada);
      $duracionPendienteReal = (!is_null($datosPago->duracionTotalXClasesPendientes) ? (float) explode("-", $datosPago->duracionTotalXClasesPendientes)[0] : 0);
      $duracionNoPagada = ($costoXHoraClase > 0 ? (($duracionRealizada + $duracionPendienteReal) - $duracionTotal) : 0);

      return (object) [
                  "duracionTotal" => $duracionTotal,
                  "duracionRealizada" => $duracionRealizada,
                  "duracionPendiente" => $duracionPendiente,
                  "duracionPendienteReal" => $duracionPendienteReal,
                  "duracionNoPagada" => $duracionNoPagada
      ];
    }
    return null;
  }

  public static function obtenerUltimoXClases($idAlumno) {
    return PagoAlumno::listar($idAlumno)
                    ->where("motivo", MotivosPago::Clases)
                    ->orderBy("fechaRegistro", "DESC")
                    ->first();
  }

  // </editor-fold>
}
