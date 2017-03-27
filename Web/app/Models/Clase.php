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

class Clase extends Model {

  public $timestamps = false;
  protected $table = "clase";
  protected $fillable = ["idAlumno", "idProfesor", "numeroPeriodo", "duracion", "costoHora", "costoHoraProfesor", "pagoTotalProfesor", "fechaInicio", "fechaFin", "fechaCancelacion", "estado"];

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
              $q->whereNull("historial.id")->orWhere(function ($q) {
                $q->where("historial.eliminado", 0)->where("historial.enviarCorreo", 1);
              });
            })
            ->groupBy($nombreTabla . ".id")
            ->distinct();
  }

  public static function obtenerXId($idAlumno, $id, $incluirFechaProximaClase = FALSE) {
    $nombreTabla = Clase::nombreTabla();
    $clase = Clase::listarBase()
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
    if ($incluirFechaProximaClase) {
      $ultimaClase = Clase::obtenerUltimaClase($idAlumno);
      if (isset($ultimaClase)) {
        $fechaProximaClase = new Carbon($ultimaClase->fechaInicio);
        $horarioAlumno = Horario::obtener($idAlumno);
        $flg = TRUE;

        while ($flg) {
          $fechaProximaClase->addDay();
          foreach ($horarioAlumno as $datHorarioAlumno) {
            if ($fechaProximaClase->dayOfWeek == $datHorarioAlumno->numeroDiaSemana) {
              $flg = FALSE;
            }
          }
        }
        $clase->fechaProximaClase = (string) $fechaProximaClase;
      }
    }
    return $clase;
  }

  public static function obtenerUltimaClase($idAlumno) {
    $nombreTabla = Clase::nombreTabla();
    return Clase::listarBase()
            ->select($nombreTabla . ".*", "entidadProfesor.id AS idProfesor", "entidadProfesor.nombre AS nombreProfesor", "entidadProfesor.apellido AS apellidoProfesor")
            ->where($nombreTabla . ".idAlumno", $idAlumno)
            ->orderBy($nombreTabla . ".fechaInicio", "DESC")
            ->first();
  }

  public static function obtenerProximaClase($idAlumno) {
    $nombreTabla = Clase::nombreTabla();
    return Clase::listarBase()
            ->select($nombreTabla . ".*", "entidadProfesor.id AS idProfesor", "entidadProfesor.nombre AS nombreProfesor", "entidadProfesor.apellido AS apellidoProfesor")
            ->where($nombreTabla . ".idAlumno", $idAlumno)
            ->where($nombreTabla . ".fechaInicio", ">=", Carbon::now())
            ->orderBy($nombreTabla . ".fechaInicio", "ASC")
            ->first();
  }

  public static function listar($datos = NULL) {
    $nombreTabla = Clase::nombreTabla();
    $clases = Clase::listarBase()
        ->select($nombreTabla . ".*", "entidadAlumno.nombre AS nombreAlumno", "entidadAlumno.apellido AS apellidoAlumno", "entidadProfesor.nombre AS nombreProfesor", "entidadProfesor.apellido AS apellidoProfesor", DB::raw("max(historial.id) AS idHistorial"), DB::raw("max(pago.estado) AS estadoPago"))
        ->leftJoin(PagoProfesor::nombreTabla() . " as pagoProfesor", "pagoClase.idPago", "=", "pagoProfesor.idPago")
        ->leftJoin(Pago::NombreTabla() . " as pago", "pagoProfesor.idPago", "=", "pago.id");
    $datos["estado"] = (isset($datos["estadoClase"]) ? $datos["estadoClase"] : NULL);
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
        ->where($nombreTabla . ".idProfesor", $idProfesor)
        ->where(function ($q) use ($idProfesor) {
          $q->whereNull("pagoProfesor.idProfesor")->orWhere("pagoProfesor.idProfesor", $idProfesor);
        })
        ->orderBy($nombreTabla . ".fechaInicio", "ASC");
    if (isset($datos["estadoPago"])) {
      $clases->where("pago.estado", $datos["estadoPago"]);
    }
    $datos["estado"] = (isset($datos["estadoClase"]) ? $datos["estadoClase"] : NULL);
    Util::filtrosBusqueda($nombreTabla, $clases, "fechaInicio", $datos);
    return $clases;
  }

  public static function listarXEstados($estados) {
    return Clase::where("eliminado", 0)->whereIn("estado", (is_array($estados) ? $estados : [$estados]));
  }

  public static function listarPeriodos($idAlumno) {
    return Clase::select("numeroPeriodo", DB::raw("min(fechaInicio) AS fechaInicio, max(fechaFin) AS fechaFin, sum(duracion) AS horasTotal"))->where("idAlumno", $idAlumno)->where("eliminado", 0)->groupBy("numeroPeriodo");
  }

  public static function totalPeriodos($idAlumno) {
    $sub = Clase::listarPeriodos($idAlumno);
    return DB::table(DB::raw("({$sub->toSql()}) as sub"))->mergeBindings($sub->getQuery())->count();
  }

  public static function listarXRangoFecha($idEntidad, $datos) {
    $fechaInicio = Carbon::createFromFormat("Y-m-d H:i:s", $datos["start"] . " 00:00:00");
    $fechaFin = Carbon::createFromFormat("Y-m-d H:i:s", $datos["end"] . " 23:59:59");
    return Clase::where("eliminado", 0)
            ->where("idAlumno", $idEntidad)
            ->where("fechaInicio", ">=", $fechaInicio)->where("fechaFin", "<=", $fechaFin)->get();
  }

  public static function listarIdsEntidadesXRangoFecha($fechaInicio, $fechaFin, $idsProfesores = FALSE) {
    $clases = Clase::where("eliminado", 0)->where(function ($q) use ($fechaInicio, $fechaFin) {
      $q->where(function ($q) use ($fechaInicio) {
        $q->where("fechaInicio", "<=", $fechaInicio)->where("fechaFin", ">=", $fechaInicio);
      })->orWhere(function ($q) use ($fechaFin) {
        $q->where("fechaInicio", "<=", $fechaFin)->where("fechaFin", ">=", $fechaFin);
      })->orWhere(function ($q) use ($fechaInicio, $fechaFin) {
        $q->where("fechaInicio", ">=", $fechaInicio)->where("fechaFin", "<=", $fechaFin);
      });
    });
    return ($idsProfesores ? $clases->groupBy("idProfesor")->lists("idProfesor") : $clases->groupBy("idAlumno")->lists("idAlumno"));
  }

  public static function datosGrupo($idAlumno, $datos) {
    if (isset($datos["ids"]) && is_array($datos["ids"])) {
      $nombreTabla = Clase::nombreTabla();
      $clases = Clase::listarBase()
          ->select($nombreTabla . ".*", DB::raw("max(pago.id) AS idPago"))
          ->leftJoin(PagoAlumno::nombreTabla() . " as pagoAlumno", "pagoClase.idPago", "=", "pagoAlumno.idPago")
          ->leftJoin(Pago::nombreTabla() . " as pago", "pagoAlumno.idPago", "=", "pago.id")
          ->where($nombreTabla . ".idAlumno", $idAlumno)
          ->whereIn($nombreTabla . ".id", $datos["ids"])
          ->where(function ($q) use ($idAlumno) {
            $q->whereNull("pagoAlumno.idAlumno")->orWhere("pagoAlumno.idAlumno", $idAlumno);
          })
          ->orderBy($nombreTabla . ".fechaInicio", "ASC")
          ->get();
      $datosGrupo = [
        "numeroPeriodo" => "",
        "estado" => NULL,
        "fechaInicio" => NULL,
        "duracion" => NULL,
        "costoHora" => "",
        "idPago" => NULL
      ];
      for ($i = 0; $i < count($clases); $i++) {
        $clase = $clases[$i];
        if ($i > 0) {
          $fechaInicioBase = new Carbon($datosGrupo["fechaInicio"]);
          $fechaInicio = new Carbon($clase->fechaInicio);

          $datosGrupo["numeroPeriodo"] = ($datosGrupo["numeroPeriodo"] != $clase->numeroPeriodo ? "" : $datosGrupo["numeroPeriodo"]);
          $datosGrupo["estado"] = ($datosGrupo["estado"] != $clase->estado ? NULL : $datosGrupo["estado"]);
          $datosGrupo["fechaInicio"] = ($fechaInicioBase->toTimeString() != $fechaInicio->toTimeString() ? NULL : $datosGrupo["fechaInicio"]);
          $datosGrupo["duracion"] = ($datosGrupo["duracion"] != $clase->duracion ? NULL : $datosGrupo["duracion"]);
          $datosGrupo["costoHora"] = ($datosGrupo["costoHora"] != $clase->costoHora ? "" : $datosGrupo["costoHora"]);
          $datosGrupo["idPago"] = ($datosGrupo["idPago"] != $clase->idPago ? NULL : $datosGrupo["idPago"]);
        } else {
          $datosGrupo["numeroPeriodo"] = $clase->numeroPeriodo;
          $datosGrupo["estado"] = $clase->estado;
          $datosGrupo["fechaInicio"] = $clase->fechaInicio;
          $datosGrupo["duracion"] = $clase->duracion;
          $datosGrupo["costoHora"] = $clase->costoHora;
          $datosGrupo["idPago"] = $clase->idPago;
        }
      }
      return $datosGrupo;
    } else {
      return [];
    }
  }

  public static function totalXHorario($idAlumno, $datos) {
    if (!(isset($datos["fecha"]) || count($datos["ids"]) > 0)) {
      return 0;
    }

    $nombreTabla = Clase::nombreTabla();
    $auxIds = (isset($datos["fecha"]) ? [1] : $datos["ids"]);
    foreach ($auxIds as $auxId) {
      if (isset($datos["fecha"])) {
        $fechaInicio = Carbon::createFromFormat("d/m/Y H:i:s", $datos["fecha"] . " 00:00:00");
      } else {
        $clase = Clase::obtenerXId($idAlumno, $auxId);
        $fechaInicio = new Carbon($clase->fechaInicio);
        $fechaInicio->setTime(0, 0, 0);
      }
      $fechaInicio->addSeconds($datos["horaInicio"]);
      $fechaFin = clone $fechaInicio;
      $fechaFin->addSeconds($datos["duracion"]);
      $total = Clase::listarBase()
              ->where($nombreTabla . ".idAlumno", $idAlumno)
              ->where(function ($q) use ($fechaInicio, $fechaFin) {
                $q->where(function ($q) use ($fechaInicio) {
                  $q->where("fechaInicio", "<=", $fechaInicio)->where("fechaFin", ">=", $fechaInicio);
                })->orWhere(function ($q) use ($fechaFin) {
                  $q->where("fechaInicio", "<=", $fechaFin)->where("fechaFin", ">=", $fechaFin);
                })->orWhere(function ($q) use ($fechaInicio, $fechaFin) {
                  $q->where("fechaInicio", ">=", $fechaInicio)->where("fechaFin", "<=", $fechaFin);
                });
              })->whereNotIn($nombreTabla . ".estado", [EstadosClase::Cancelada])
              ->whereNotIn($nombreTabla . ".id", $datos["ids"])->count();
      if ($total > 0) {
        return $total;
      }
    }
    return 0;
  }

  public static function reporte($datos) {
    $clases = Clase::where("eliminado", 0)
        ->select(($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::Mes ? DB::raw("MONTH(fechaInicio) AS mes") : ($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::Anho ? DB::raw("YEAR(fechaInicio) AS anho") : "fechaInicio")), "estado", DB::raw("count(id) AS total"))
        ->groupBy(($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::Mes ? DB::raw("MONTH(fechaInicio)") : ($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::Anho ? DB::raw("YEAR(fechaInicio)") : "fechaInicio")), "estado")
        ->orderBy("fechaInicio", "ASC");
    if (isset($datos["ids"]) && is_array($datos["ids"])) {
      return $clases->whereIn("id", $datos["ids"])->get();
    } else {
      return [];
    }
  }

  public static function generarXDatosPago($idAlumno, $datos) {
    $duracionTotalSeg = 0;
    $clasesGeneradas = [];

    $preHorasPagadas = ((float) $datos["monto"] / (float) $datos["costoHoraClase"]);
    $horasPagadas = ($preHorasPagadas - fmod($preHorasPagadas, 0.5));
    $fechaInicioClase = Carbon::createFromFormat("d/m/Y H:i:s", $datos["fechaInicioClases"] . " 00:00:00");
    $horarioAlumno = Horario::obtener($idAlumno);

    while ($duracionTotalSeg < ($horasPagadas * 3600)) {
      foreach ($horarioAlumno as $datHorarioAlumno) {
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
      $datos["idPago"] = $idPago;
      Clase::registrarActualizar($idAlumno, $datos);
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
    $clase = ((isset($datos["idClase"])) ? Clase::obtenerXId($idAlumno, $datos["idClase"]) : NULL);
    if (!is_null($clase)) {
      if (is_null($datos["estado"])) {
        unset($datos["estado"]);
      }
      if (!is_null($clase->idHistorial) && !$notificar) {
        Historial::eliminarXIdClase($datos["idClase"]);
      }
      if (!is_null($clase->idHistorial) && $notificar) {
        $notificar = FALSE;
      }
      $clase->update($datos);
    } else {
      $clase = new Clase($datos);
      $clase->save();
    }

    if (isset($datos["idPago"])) {
      PagoClase::registrarActualizar($datos["idPago"], $clase["id"], $idAlumno);
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

  public static function actualizarGrupo($idAlumno, $datos) {
    $clases = Clase::listar()->whereIn(Clase::nombreTabla() . ".id", $datos["idsClases"])->orderBy(Clase::nombreTabla() . ".fechaInicio")->get();
    foreach ($clases as $clase) {
      $claseSel = Clase::obtenerXId($idAlumno, $clase->id);
      if (!is_null($claseSel) && $claseSel->estado != EstadosClase::Cancelada) {
        $datosActualizar = [];
        if ($datos["editarDatosGenerales"] == 1) {
          $datosActualizar["numeroPeriodo"] = $datos["numeroPeriodo"];
          $datosActualizar["estado"] = $datos["estado"];
        }
        if ($datos["editarDatosTiempo"] == 1) {
          $fechaInicio = new Carbon($claseSel->fechaInicio);
          $datosActualizar["fechaInicio"] = $fechaInicio->setTime(0, 0, 0)->addSeconds($datos["horaInicio"]);
          $datosActualizar["fechaFin"] = clone $datosActualizar["fechaInicio"];
          $datosActualizar["fechaFin"]->addSeconds($datos["duracion"]);
          $datosActualizar["duracion"] = $datos["duracion"];
        }

        if ($datos["editarDatosProfesor"] == 1) {
          $datosActualizar["idProfesor"] = $datos["idDocente"];
          $datosActualizar["costoHoraProfesor"] = $datos["costoHoraDocente"];
        }

        $claseSel->update($datosActualizar);
        if ($datos["editarDatosPago"] == 1 && isset($datos["idPago"])) {
          PagoClase::registrarActualizar($datos["idPago"], $clase->id, $idAlumno);
        }
      }
    }
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
      if (isset($datos["idProfesor"]) && isset($datos["pagoProfesor"])) {
        $claseCancelada->pagoTotalProfesor = $datos["pagoProfesor"];
      }
      $claseCancelada->save();
      Historial::eliminarXIdClase($datos["idClase"]);

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
    } catch (\Exception $ex) {
      return FALSE;
    }
    return TRUE;
  }

  public static function eliminadXIdPago($idAlumno, $idPago) {
    $pagosClases = PagoClase::obtenerXIdPago($idPago);
    foreach ($pagosClases as $pagoClase) {
      try {
        Clase::obtenerXId($idAlumno, $pagoClase->idClase);
      } catch (\Exception $e) {
        continue;
      }
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
    Historial::eliminarXIdClase($id);
  }

  public static function sincronizarEstados() {
    $clasesProgramadas = Clase::listarXEstados(EstadosClase::Programada)
        ->where("fechaFin", "<=", Carbon::now())
        ->get();
    foreach ($clasesProgramadas as $claseProgramada) {
      $claseProgramada->estado = EstadosClase::PendienteConfirmar;
      $claseProgramada->save();
    }
  }

}
