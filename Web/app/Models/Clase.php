<?php

namespace App\Models;

use DB;
use Auth;
use Carbon\Carbon;
use App\Helpers\Util;
use App\Models\Horario;
use App\Models\Historial;
use App\Helpers\Enum\EstadosClase;
use App\Helpers\Enum\TiposHistorial;
use App\Helpers\Enum\MensajesHistorial;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Enum\TiposBusquedaFecha;
use App\Helpers\Enum\TiposCancelacionClase;

class Clase extends Model {

  public $timestamps = false;
  protected $table = "clase";
  protected $fillable = ["idAlumno", "idProfesor", "numeroPeriodo", "duracion", "costoHora", "costoHoraProfesor", "fechaInicio", "fechaFin", "fechaCancelacion", "estado"];

  public static function nombreTabla() {
    $modeloClase = new Clase();
    $nombreTabla = $modeloClase->getTable();
    unset($modeloClase);
    return $nombreTabla;
  }

  private static function listarBase() {
    $nombreTabla = Clase::nombreTabla();
    return Clase::leftJoin(Entidad::nombreTabla() . " as entidadAlumno", $nombreTabla . ".idAlumno", "=", "entidadAlumno.id")
                    ->leftJoin(Entidad::nombreTabla() . " as entidadProfesor", $nombreTabla . ".idProfesor", "=", "entidadProfesor.id")
                    ->leftJoin(Historial::nombreTabla() . " as historial", $nombreTabla . ".id", "=", "historial.idClase")
                    ->leftJoin(PagoClase::nombreTabla() . " as pagoClase", $nombreTabla . ".id", "=", "pagoClase.idClase")
                    ->where($nombreTabla . ".eliminado", 0)
                    ->where(function ($q) {
                      $q->whereNull("historial.id")->orWhere("historial.enviarCorreo", 1);
                    })
                    ->groupBy($nombreTabla . ".id")
                    ->distinct();
  }

  public static function obtenerXId($idAlumno, $id) {
    $nombreTabla = Clase::nombreTabla();
    return Clase::listarBase()
                    ->select($nombreTabla . ".*", "entidadProfesor.nombre AS nombreProfesor", "entidadProfesor.apellido AS apellidoProfesor", DB::raw("max(historial.id) AS idHistorial"), DB::raw("max(pago.id) AS idPago"))
                    ->leftJoin(PagoAlumno::nombreTabla() . " as pagoAlumno", "pagoClase.idPago", "=", "pagoAlumno.idPago")
                    ->leftJoin(Pago::nombreTabla() . " as pago", "pagoAlumno.idPago", "=", "pago.id")
                    ->where($nombreTabla . ".idAlumno", $idAlumno)
                    ->where($nombreTabla . ".id", $id)
                    ->where(function ($q) use ($idAlumno) {
                      $q->whereNull("pagoAlumno.idAlumno")->orWhere("pagoAlumno.idAlumno", $idAlumno);
                    })
                    ->orderBy($nombreTabla . ".fechaInicio", "ASC")
                    ->firstOrFail();
  }

  public static function obtenerUltimaClase($idAlumno) {
    $nombreTabla = Clase::nombreTabla();
    return Clase::listarBase()
                    ->select($nombreTabla . ".*", "entidadProfesor.id AS idProfesor", "entidadProfesor.nombre AS nombreProfesor", "entidadProfesor.apellido AS apellidoProfesor")
                    ->where($nombreTabla . ".idAlumno", $idAlumno)
                    ->orderBy($nombreTabla . ".fechaInicio", "DESC")
                    ->first();
  }

  public static function listar($datos = NULL) {
    $nombreTabla = Clase::nombreTabla();
    $clases = Clase::listarBase()
            ->select($nombreTabla . ".*", "entidadAlumno.nombre AS nombreAlumno", "entidadAlumno.apellido AS apellidoAlumno", "entidadProfesor.nombre AS nombreProfesor", "entidadProfesor.apellido AS apellidoProfesor", DB::raw("max(historial.id) AS idHistorial"), DB::raw("max(pago.estado) AS estadoPago"))
            ->leftJoin(PagoProfesor::nombreTabla() . " as pagoProfesor", "pagoClase.idPago", "=", "pagoProfesor.idPago")
            ->leftJoin(Pago::NombreTabla() . " as pago", "pagoProfesor.idPago", "=", "pago.id")
            ->orderBy($nombreTabla . ".fechaInicio", "ASC");
    Util::filtrosBusqueda($nombreTabla, $clases, "fechaInicio", $datos);
    return $clases;
  }

  public static function listarXAlumno($idAlumno, $numeroPeriodo) {
    $nombreTabla = Clase::nombreTabla();
    $clases = Clase::listarBase()
                    ->select($nombreTabla . ".*", "entidadProfesor.nombre AS nombreProfesor", "entidadProfesor.apellido AS apellidoProfesor", DB::raw("max(historial.id) AS idHistorial"))
                    ->where($nombreTabla . ".numeroPeriodo", $numeroPeriodo)
                    ->where($nombreTabla . ".idAlumno", $idAlumno)
                    ->orderBy($nombreTabla . ".fechaInicio", "ASC")->get();

    foreach ($clases as $clase) {
      $pagoProfesor = PagoProfesor::ObtenerXClase($clase["id"]);
      $pagoAlumno = PagoAlumno::ObtenerXClase($idAlumno, $clase["id"]);

      $clase->estadoPagoProfesor = (!is_null($pagoProfesor) ? $pagoProfesor["estado"] : NULL);
      $clase->estadoPagoAlumno = (!is_null($pagoAlumno) ? $pagoAlumno["estado"] : NULL);
    }
    return $clases;
  }

  public static function listarXProfesor($idProfesor, $datos = NULL) {
    $nombreTabla = Clase::nombreTabla();
    $clases = Clase::listarBase()
            ->select($nombreTabla . ".*", "entidadAlumno.nombre AS nombreAlumno", "entidadAlumno.apellido AS apellidoAlumno", DB::raw("max(historial.id) AS idHistorial"), DB::raw("max(pago.estado) AS estadoPago"))
            ->leftJoin(PagoProfesor::nombreTabla() . " as pagoProfesor", "pagoClase.idPago", "=", "pagoProfesor.idPago")
            ->leftJoin(Pago::nombreTabla() . " as pago", "pagoProfesor.idPago", "=", "pago.id")
            ->where(function ($q) use ($idProfesor) {
              $q->whereNull("pagoProfesor.idProfesor")->orWhere("pagoProfesor.idProfesor", $idProfesor);
            })
            ->where($nombreTabla . ".idProfesor", $idProfesor)
            ->orderBy($nombreTabla . ".fechaInicio", "ASC");

    if (isset($datos["estadoClase"])) {
      $clases->where($nombreTabla . ".estado", $datos["estadoClase"]);
    }
    if (isset($datos["estadoPago"])) {
      $clases->where("pago.estado", $datos["estadoPago"]);
    }
    return $clases;
  }

  public static function listarXEstados($estados) {
    $nombreTabla = Clase::nombreTabla();
    return Clase::where($nombreTabla . ".eliminado", 0)->whereIn($nombreTabla . ".estado", (is_array($estados) ? $estados : [$estados]));
  }

  public static function listarPeriodos($idAlumno) {
    return Clase::select("numeroPeriodo", DB::raw("min(fechaInicio) AS fechaInicio, max(fechaFin) AS fechaFin, sum(duracion) AS horasTotal"))->where("idAlumno", $idAlumno)->where("eliminado", 0)->groupBy("numeroPeriodo");
  }

  public static function totalPeriodos($idAlumno) {
    return count(Clase::selectRaw("count(*) as total")->where("idAlumno", $idAlumno)->where("eliminado", 0)->groupBy("numeroPeriodo")->get()->toArray());
  }

  public static function listarIdsEntidadesXRangoFecha($fechaInicio, $fechaFin, $idsProfesores = FALSE) {
    $clases = Clase::where("fechaInicio", "<=", $fechaInicio)->where("fechaFin", ">=", $fechaFin);
    return ($idsProfesores ? $clases->groupBy("idProfesor")->lists("idProfesor") : $clases->groupBy("idAlumno")->lists("idAlumno"));
  }

  public static function reporte($datos) {
    $datos["tipoBusquedaFecha"] = (isset($datos["tipoBusquedaFecha"]) && $datos["tipoBusquedaFecha"] != TiposBusquedaFecha::Dia ? $datos["tipoBusquedaFecha"] : TiposBusquedaFecha::Mes);
    $nombreTabla = Clase::nombreTabla();

    $clases = Clase::where("eliminado", 0)
            ->select(($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::Mes ? DB::raw("MONTH(fechaInicio) AS mes") : ($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::Anho ? DB::raw("YEAR(fechaInicio) AS anho") : "fechaInicio")), "estado", DB::raw("count(id) AS total"))
            ->groupBy(($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::Mes ? DB::raw("MONTH(fechaInicio)") : ($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::Anho ? DB::raw("YEAR(fechaInicio)") : "fechaInicio")), "estado")
            ->orderBy("fechaInicio", "ASC");
    Util::filtrosBusqueda($nombreTabla, $clases, "fechaInicio", $datos);
    return $clases->get();
  }

  public static function generarXDatosPago($idAlumno, $datos) {
    $duracionTotalSeg = 0;
    $clasesGeneradas = [];

    $preHorasPagadas = ((float) $datos["monto"] / (float) $datos["costoHoraClase"]);
    $horasPagadas = ($preHorasPagadas - fmod($preHorasPagadas, 0.5));
    $fechaInicioClase = Carbon::createFromFormat("d/m/Y H:i:s", $datos["fechaInicioClases"] . " 00:00:00");

    while ($duracionTotalSeg < ($horasPagadas * 3600)) {
      foreach (Horario::obtener($idAlumno) as $datHorarioAlumno) {
        if ($fechaInicioClase->dayOfWeek == $datHorarioAlumno->numeroDiaSemana) {
          $fechaInicio = Carbon::createFromFormat("d/m/Y H:i:s", $fechaInicioClase->format("d/m/Y") . " " . $datHorarioAlumno->horaInicio);
          $preFechaFin = Carbon::createFromFormat("d/m/Y H:i:s", $fechaInicioClase->format("d/m/Y") . " " . $datHorarioAlumno->horaFin);

          $tiempoAdicionalSeg = (($duracionTotalSeg + $preFechaFin->diffInSeconds($fechaInicio)) - ($horasPagadas * 3600));
          $fechaFin = (($tiempoAdicionalSeg > 0) ? $preFechaFin->subSeconds($tiempoAdicionalSeg) : $preFechaFin);
          $duracion = $fechaFin->diffInSeconds($fechaInicio);
          $clasesGeneradas[] = ["fechaInicio" => $fechaInicio, "fechaFin" => $fechaFin, "duracion" => $duracion, "tiempoAdicional" => ($tiempoAdicionalSeg > 0 ? $tiempoAdicionalSeg : 0)];
          $duracionTotalSeg += $duracion;
        }
      }
      $fechaInicioClase = $fechaInicioClase->addDay();
    }
    $clasesGeneradas["montoRestante"] = ($datos["monto"] - ($horasPagadas * (float) $datos["costoHoraClase"]));

    $datosUltimaClase = Clase::obtenerUltimaClase($idAlumno);
    if (!is_null($datosUltimaClase)) {
      $idsDocentesDisponibles = Docente::listarIdsDisponiblesXDatosClasesGeneradas($clasesGeneradas);
      if (in_array($datosUltimaClase->idProfesor, $idsDocentesDisponibles)) {
        $clasesGeneradas["idProfesor"] = $datosUltimaClase->idProfesor;
        $clasesGeneradas["nombreCompletoProfesor"] = $datosUltimaClase->nombreProfesor . " " . $datosUltimaClase->apellidoProfesor;
      }
    }
    return $clasesGeneradas;
  }

  public static function registrarXDatosPago($idAlumno, $idPago, $datos) {
    $datosClases = Clase::generarXDatosPago($idAlumno, $datos);
    $datosNotificacionClases = json_decode($datos["datosNotificacionClases"]);

    for ($i = 0; $i < count($datosClases); $i++) {
      if (!isset($datosClases[$i]["duracion"])) {
        continue;
      }
      $datos["duracion"] = $datosClases[$i]["duracion"];
      $datos["costoHora"] = $datos["costoHoraClase"];
      $datos["fechaInicio"] = $datosClases[$i]["fechaInicio"];
      $datos["fechaFin"] = $datosClases[$i]["fechaFin"];
      $datos["numeroPeriodo"] = $datos["periodoClases"];
      $datos["notificar"] = (($datosNotificacionClases[$i]->notificar != "" && $datosNotificacionClases[$i]->notificar) ? 1 : 0);
      $datos["estado"] = EstadosClase::Programada;
      $idClase = Clase::registrarActualizar($idAlumno, $datos);
      PagoClase::registrarActualizar($idPago, $idClase);
    }
  }

  public static function registrarActualizar($idAlumno, $datos) {
    if (!(isset($datos["fechaInicio"]) && isset($datos["fechaFin"]))) {
      $datos["fechaInicio"] = Carbon::createFromFormat("d/m/Y H:i:s", $datos["fecha"] . " 00:00:00")->addSeconds($datos["horaInicio"]);
      $datos["fechaFin"] = clone $datos["fechaInicio"];
      $datos["fechaFin"]->addSeconds($datos["duracion"]);
    }
    $datos["idAlumno"] = $idAlumno;
    $datos["idProfesor"] = $datos["idDocente"];
    $datos["costoHoraProfesor"] = $datos["costoHoraDocente"];

    $notificar = ($datos["notificar"] == 1);
    $claseAnt = ((isset($datos["idClase"])) ? Clase::obtenerXId($idAlumno, $datos["idClase"]) : NULL);
    if ($claseAnt !== NULL) {
      $notificar = ($notificar && $claseAnt->idNotificar == NULL);
    }
    if (isset($datos["idClase"])) {
      $clase = Clase::obtenerXId($idAlumno, $datos["idClase"]);
      $clase->update($datos);
    } else {
      $clase = new Clase($datos);
      $clase->save();
    }

    if (isset($datos["idPago"])) {
      PagoClase::registrarActualizar($datos["idPago"], $clase["id"]);
    }

    if ($notificar) {
      $tituloHistorial = str_replace(["[DIAS]"], ["1 día"], (!is_null($datos["idDocente"]) ? MensajesHistorial::TituloCorreoAlumnoClase : MensajesHistorial::TituloCorreoAlumnoClaseSinProfesor));
      $mensajeHistorial = str_replace(["[FECHA]", "[PERIODO]", "[DURACION]"], [$datos["fechaInicio"]->format("d/m/Y H:i:s"), $datos["numeroPeriodo"], gmdate("H:i", $datos["duracion"])], (!is_null($datos["idDocente"]) ? MensajesHistorial::MensajeCorreoAlumnoClase : MensajesHistorial::MensajeCorreoAlumnoClaseSinProfesor));
      Historial::registrar([
          "idEntidades" => [$idAlumno, $datos["idDocente"], Auth::user()->idEntidad],
          "titulo" => $tituloHistorial,
          "mensaje" => $mensajeHistorial,
          "enviarCorreo" => 1,
          "mostrarEnPerfil" => 0,
          "idClase" => $clase["id"],
          "fechaNotificacion" => $datos["fechaInicio"]->subDays(1),
          "tipo" => TiposHistorial::Correo
      ]);
    }
    return $clase["id"];
  }

  public static function actualizarEstado($idAlumno, $datos) {
    $clase = Clase::obtenerXId($idAlumno, $datos["idClase"]);
    $clase->estado = $datos["estado"];
    $clase->save();
  }

  public static function cancelar($idAlumno, $datos) {
    $claseCancelada = Clase::obtenerXId($idAlumno, $datos["idClase"]);
    if ($claseCancelada !== EstadosClase::Cancelada && $claseCancelada !== EstadosClase::Realizada) {
      $claseCancelada->tipoCancelacion = $datos["tipoCancelacion"];
      $claseCancelada->fechaCancelacion = Carbon::now()->toDateTimeString();
      $claseCancelada->estado = EstadosClase::Cancelada;
      $claseCancelada->save();

      if ($datos["tipoCancelacion"] == TiposCancelacionClase::CancelacionAlumno && isset($datos["idProfesor"]) && isset($datos["pagoProfesor"])) {
        PagoProfesor::registrarXDatosClaseCancelada($datos["idProfesor"], $datos["idClase"], $datos["pagoProfesor"]);
      }

      if ($datos["reprogramarCancelacion"] == 1) {
        unset($datos["idClase"]);
        $datos["numeroPeriodo"] = $claseCancelada["numeroPeriodo"];
        $datos["costoHora"] = $claseCancelada["costoHora"];
        $datos["notificar"] = ((isset($claseCancelada["idHistorial"])) ? 1 : 0);
        $datos["idClaseCancelada"] = $claseCancelada["id"];
        $datos["estado"] = EstadosClase::Programada;
        Clase::registrarActualizar($idAlumno, $datos);
      }
    }
  }

  public static function verificarExistencia($idAlumno, $id) {
    try {
      Clase::obtenerXId($idAlumno, $id);
    } catch (Exception $ex) {
      return FALSE;
    }
    return TRUE;
  }

  public static function eliminadXIdPago($idAlumno, $idPago) {
    $pagosClases = PagoClase::obtenerXIdPago($idPago);
    foreach ($pagosClases as $pagoClase) {
      Clase::eliminar($idAlumno, $pagoClase->idClase);
    }
  }

  public static function eliminadXIdAdlumno($idAlumno) {
    $nombreTabla = Clase::nombreTabla();
    $clases = Clase::where($nombreTabla . ".eliminado", 0)->where($nombreTabla . ".idAlumno", $idAlumno)->get();
    foreach ($clases as $clase) {
      Clase::eliminar($idAlumno, $clase->id);
    }
  }

  public static function eliminar($idAlumno, $id) {
    $clase = Clase::obtenerXId($idAlumno, $id);
    $clase->eliminado = 1;
    $clase->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
    $clase->save();
  }

  public static function sincronizarEstados() {
    $nombreTabla = Clase::nombreTabla();
    $clasesProgramadas = Clase::listarXEstados(EstadosClase::Programada)
            ->where($nombreTabla . ".fechaFin", "<=", Carbon::now())
            ->get();
    foreach ($clasesProgramadas as $claseProgramada) {
      $claseProgramada->estado = EstadosClase::PendienteConfirmar;
      $claseProgramada->save();
    }
  }

}
