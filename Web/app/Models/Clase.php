<?php

namespace App\Models;

use DB;
use Log;
use Auth;
use Config;
use Carbon\Carbon;
use App\Helpers\Util;
use App\Models\Horario;
use App\Models\Historial;
use App\Helpers\Enum\EstadosClase;
use App\Helpers\Enum\RolesUsuario;
use App\Helpers\Enum\TiposHistorial;
use App\Helpers\Enum\MensajesHistorial;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Enum\TiposBusquedaFecha;

class Clase extends Model {

  public $timestamps = false;
  protected $table = "clase";
  protected $fillable = [
      "idAlumno",
      "idProfesor",
      "numeroPeriodo",
      "duracion",
      "costoHora",
      "costoHoraProfesor",
      "pagoTotalProfesor",
      "fechaInicio",
      "fechaFin",
      "fechaCancelacion",
      "comentarioAlumno",
      "comentarioProfesor",
      "comentarioParaAlumno",
      "comentarioParaProfesor",
      "fechaConfirmacion",
      "estado"
  ];

  public static function nombreTabla()/* - */ {
    $modeloClase = new Clase();
    $nombreTabla = $modeloClase->getTable();
    unset($modeloClase);
    return $nombreTabla;
  }

  public static function listarBase($incluirSoloRealizadas = FALSE)/* - */ {
    $nombreTablaClase = Clase::nombreTabla();
    $nombreTablaPago = Pago::nombreTabla();
    $nombreTablaEntidad = Entidad::nombreTabla();
    $nombreTablaPagoClase = PagoClase::nombreTabla();
    $nombreTablaPagoAlumno = PagoAlumno::nombreTabla();
    $nombreTablaPagoProfesor = PagoProfesor::nombreTabla();

    $clases = Clase::
            //Datos del alumno
            join($nombreTablaEntidad . " as entidadAlumno", function ($q) use($nombreTablaClase) {
              $q->on($nombreTablaClase . ".idAlumno", "=", "entidadAlumno.id")
              ->on("entidadAlumno.eliminado", "=", DB::raw("0"));
            })
            //Pago del alumno asociado
            ->leftJoin($nombreTablaPagoAlumno . " AS relPagoAlumno", function ($q) use ($nombreTablaClase, $nombreTablaPagoClase) {
              $q->on("relPagoAlumno.idPago", "IN", DB::raw("(SELECT idPago 
                                                      FROM " . $nombreTablaPagoClase . "
                                                      WHERE idClase = " . $nombreTablaClase . ".id)"))
              ->on("relPagoAlumno.idAlumno", "=", $nombreTablaClase . ".idAlumno");
            })
            ->leftJoin($nombreTablaPago . " as pagoAlumno", "pagoAlumno.id", "=", "relPagoAlumno.idPago")

            //Datos del profesor
            ->leftJoin($nombreTablaEntidad . " as entidadProfesor", function ($q) use($nombreTablaClase) {
              $q->on($nombreTablaClase . ".idProfesor", "=", "entidadProfesor.id")
              ->on("entidadProfesor.eliminado", "=", DB::raw("0"));
            })
            //Pago al profesor asociado
            ->leftJoin($nombreTablaPagoProfesor . " AS relPagoProfesor", function ($q) use ($nombreTablaClase, $nombreTablaPagoClase) {
              $q->on("relPagoProfesor.idPago", "IN", DB::raw("(SELECT idPago 
                                                        FROM " . $nombreTablaPagoClase . "
                                                        WHERE idClase = " . $nombreTablaClase . ".id)"))
              ->on("relPagoProfesor.idProfesor", "=", $nombreTablaClase . ".idProfesor");
            })
            ->leftJoin($nombreTablaPago . " as pagoProfesor", "pagoProfesor.id", "=", "relPagoProfesor.idPago")
            ->where($nombreTablaClase . ".eliminado", DB::raw("0"))                    
            ->groupBy($nombreTablaClase . ".id")
            ->distinct();
    if ($incluirSoloRealizadas) {
      $clases->whereIn($nombreTablaClase . ".estado", [EstadosClase::ConfirmadaProfesorAlumno, EstadosClase::Realizada]);
    }
    return $clases;
  }

  public static function listarNUEVO($incluirSoloRealizadas = FALSE)/* - */ {
    $nombreTablaClase = Clase::nombreTabla();
    return Clase::listarBase($incluirSoloRealizadas)
                    ->select(DB::raw(
                                    $nombreTablaClase . ".*, 
                                    entidadAlumno.nombre AS nombreAlumno, 
                                    entidadAlumno.apellido AS apellidoAlumno, 
                                    pagoAlumno.id AS idPagoAlumno,
                                    entidadProfesor.nombre AS nombreProfesor, 
                                    entidadProfesor.apellido AS apellidoProfesor, 
                                    pagoProfesor.id AS idPagoProfesor")
                    )
                    ->groupBy($nombreTablaClase . ".id");
  }

  public static function listarXAlumnoNUEVO($idAlumno, $incluirSoloRealizadas = TRUE)/* - */ {
    return Clase::listarNUEVO($incluirSoloRealizadas)->where(Clase::nombreTabla() . ".idAlumno", $idAlumno);
  }

  public static function listarXProfesorNUEVO($idProfesor, $incluirSoloRealizadas = TRUE)/* - */ {
    return Clase::listarNUEVO($incluirSoloRealizadas)->where(Clase::nombreTabla() . ".idProfesor", $idProfesor);
  }

  public static function obtenerXIdNUEVO($id)/* - */ {
    $nombreTabla = Clase::nombreTabla();
    $clase = Clase::listarBase()
            ->select($nombreTabla . ".*")
            ->where($nombreTabla . ".id", $id)
            ->firstOrFail();
    return $clase;
  }

  public static function listarPeriodosXIdAlumno($idAlumno)/* - */ {
    return Clase::select(DB::raw("numeroPeriodo, 
                                  min(fechaInicio) AS fechaInicio, 
                                  max(fechaFin) AS fechaFin, 
                                  sum(duracion) AS horasTotal"))
                    ->where("idAlumno", $idAlumno)
                    ->where("eliminado", 0)
                    ->groupBy("numeroPeriodo");
  }

  public static function totalPeriodosXIdAlumno($idAlumno)/* - */ {
    $sub = Clase::listarPeriodosXIdAlumno($idAlumno);
    return DB::table(DB::raw("({$sub->toSql()}) as sub"))->mergeBindings($sub->getQuery())->count();
  }

  public static function calendario($datos)/* - */ {
    $nombreTablaClase = Clase::nombreTabla();

    if ($datos["tipoEntidad"] !== "0" && !is_null($datos["idProfesor"])) {
      $preClases = Clase::listarXProfesorNUEVO($datos["idProfesor"]);
    } else if (!is_null($datos["idAlumno"])) {
      $preClases = Clase::listarXAlumnoNUEVO($datos["idAlumno"]);
    } else {
      $preClases = Clase::listarNUEVO(TRUE);
    }

    $fechaInicio = Carbon::createFromFormat("Y-m-d H:i:s", $datos["start"] . " 00:00:00");
    $fechaFin = Carbon::createFromFormat("Y-m-d H:i:s", $datos["end"] . " 23:59:59");
    $clases = $preClases
                    ->where(function ($q) use ($nombreTablaClase, $fechaInicio, $fechaFin) {
                      $q->whereBetween($nombreTablaClase . ".fechaConfirmacion", [$fechaInicio, $fechaFin])
                      ->orWhereBetween($nombreTablaClase . ".fechaInicio", [$fechaInicio, $fechaFin])
                      ->orWhereBetween($nombreTablaClase . ".fechaFin", [$fechaInicio, $fechaFin]);
                    })->get();

    $eventos = [];
    $estadosClase = EstadosClase::listar();
    foreach ($clases as $clase) {
      $titulo = "Clase " . $estadosClase[$clase->estado][0];
      if (is_null($datos["idAlumno"])) {
        $titulo .= "\n- Alumno: " . $clase->nombreAlumno . " " . $clase->apellidoAlumno;
      }
      if (is_null($datos["idProfesor"])) {
        $titulo .= "\n- Profesor: " . $clase->nombreProfesor . " " . $clase->apellidoProfesor;
      }

      $fechaIni = $clase->fechaInicio;
      $fechaFin = $clase->fechaFin;

      if (isset($clase->fechaConfirmacion)) {
        $fechaConfirmacion = Carbon::createFromFormat("Y-m-d H:i:s", $clase->fechaConfirmacion);

        $fechaIni = $fechaConfirmacion->subSeconds($clase->duracion)->toDateTimeString();
        $fechaFin = $clase->fechaConfirmacion;
      }

      array_push($eventos, [
          "id" => $clase->id,
          "idAlumno" => $clase->idAlumno,
          "idProfesor" => $clase->idProfesor,
          "title" => $titulo,
          "start" => $fechaIni,
          "end" => $fechaFin,
          "backgroundColor" => $estadosClase[$clase->estado][2]
      ]);
    }
    return $eventos;
  }

  public static function listarIdsEntidadesXHorario($datosJsonHorario, $idsProfesores = FALSE, $incluirClasesCanceladas = FALSE) {
    $idsEntidades = [];
    $datosHorario = json_decode($datosJsonHorario);
    foreach ($datosHorario as $horario) {
      $dias = explode(",", $horario->dias);
      $horas = $horario->horas;
      foreach ($dias as $dia) {
        foreach ($horas as $rangoHora) {
          $rangoHora = explode("-", $rangoHora);

          $diaSel = ((int) $dia != 7 ? ((int) $dia) + 1 : 1);
          $fechaActual = Carbon::now();
          $horaInicio = Carbon::createFromFormat("d/m/Y H:i:s", "01/01/1970 " . $rangoHora[0] . ":00")->subMinutes((int) Config::get("eah.rangoMinutosBusquedaHorarioDocente"))->toTimeString();
          $horaFin = Carbon::createFromFormat("d/m/Y H:i:s", "01/01/1970 " . $rangoHora[1] . ":00")->addMinutes((int) Config::get("eah.rangoMinutosBusquedaHorarioDocente"))->toTimeString();

          $clases = Clase::where("eliminado", 0)
                  ->where(function ($q) use ($horaInicio, $horaFin) {
                    $q->where(function ($q) use ($horaInicio) {
                      $q->whereRaw("TIME(fechaInicio) <= '" . $horaInicio . "'")->whereRaw("TIME(fechaFin) >= '" . $horaInicio . "'");
                    })->orWhere(function ($q) use ($horaFin) {
                      $q->whereRaw("TIME(fechaInicio) <= '" . $horaFin . "'")->whereRaw("TIME(fechaFin) >= '" . $horaFin . "'");
                    })->orWhere(function ($q) use ($horaInicio, $horaFin) {
                      $q->whereRaw("TIME(fechaInicio) >= '" . $horaInicio . "'")->whereRaw("TIME(fechaFin) <= '" . $horaFin . "'");
                    });
                  })
                  ->where("fechaInicio", ">=", $fechaActual)
                  ->whereRaw("DAYOFWEEK(fechaInicio) = " . $diaSel)
                  ->whereRaw("DAYOFWEEK(fechaFin) = " . $diaSel);
          if (!$incluirClasesCanceladas) {
            $clases->where("estado", '!=', EstadosClase::Cancelada);
          }
          $idsEntidadesHorario = ($idsProfesores ? $clases->groupBy("idProfesor")->lists("idProfesor") : $clases->groupBy("idAlumno")->lists("idAlumno"));
          $idsEntidades = array_merge($idsEntidades, $idsEntidadesHorario->toArray());
        }
      }
    }
    return $idsEntidades;
  }

  public static function actualizarEstadoNUEVO($id, $estado)/* - */ {
    $clase = Clase::obtenerXIdNUEVO($id);
    $clase->estado = $estado;
    $clase->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
    $clase->save();
  }

  public static function actualizarComentarios($id, $datos)/* - */ {
    $clase = Clase::obtenerXIdNUEVO($id);

    if (Auth::user()->rol == RolesUsuario::Alumno && Auth::user()->idEntidad == $clase->idAlumno) {
      $clase->comentarioAlumno = $datos["comentario"];
    }if (Auth::user()->rol == RolesUsuario::Profesor && Auth::user()->idEntidad == $clase->idProfesor) {
      if ($clase->idAlumno != $datos["idAlumno"]) {
        return;
      }
      $clase->comentarioProfesor = $datos["comentario"];
    } else if (in_array(Auth::user()->rol, [RolesUsuario::Principal, RolesUsuario::Secundario])) {
      switch ($datos["tipo"]) {
        case 1:
          $clase->comentarioAlumno = $datos["comentario"];
          break;
        case 2:
          $clase->comentarioProfesor = $datos["comentario"];
          break;
        case 3:
          $clase->comentarioParaAlumno = $datos["comentario"];
          break;
        case 4:
          $clase->comentarioParaProfesor = $datos["comentario"];
          break;
      }
    } else {
      return;
    }
    $clase->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
    $clase->save();
  }

  public static function verificarExistencia($idAlumno, $id) {
    try {
      Clase::listarXAlumnoNUEVO($idAlumno, FALSE)->where(Clase::nombreTabla() . ".id", $id)->firstOrFail();
    } catch (\Exception $e) {
      Log::error($e);
      return FALSE;
    }
    return TRUE;
  }

  public static function eliminadXIdPago($idAlumno, $idPago) {
    $pagosClases = PagoClase::obtenerXIdPago($idPago);
    foreach ($pagosClases as $pagoClase) {
      if (Clase::verificarExistencia($idAlumno, $pagoClase->idClase)) {
        Clase::eliminar($idAlumno, $pagoClase->idClase);
      }
    }
  }

  public static function eliminadXIdAdlumno($idAlumno) {
    $clases = Clase::where("eliminado", 0)->where("idAlumno", $idAlumno)->get();
    foreach ($clases as $clase) {
      Clase::eliminar($idAlumno, $clase->id);
    }
  }

  public static function eliminar($idAlumno, $id)/* - */ {
    if (Clase::verificarExistencia($idAlumno, $id)) {
      $clase = Clase::obtenerXIdNUEVO($id);
      $clase->eliminado = 1;
      $clase->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
      $clase->save();
      Historial::eliminarXIdClase($id);
    }
  }

  // <editor-fold desc="TODO: ELIMINAR">
  public static function obtenerXIdPago($idAlumno, $idPago) {
    $clases = [];
    $pagosClases = PagoClase::obtenerXIdPago($idPago);
    foreach ($pagosClases as $pagoClase) {
      try {
        $clase = Clase::obtenerXId($idAlumno, $pagoClase->idClase);
      } catch (\Exception $e) {
        Log::error($e);
        continue;
      }
      $clases[] = $clase;
    }
    return $clases;
  }

  public static function obtenerUltimaClase($idAlumno) {
    $nombreTabla = Clase::nombreTabla();
    return Clase::listarBase()
                    ->select($nombreTabla . ".*", "entidadProfesor.id AS idProfesor", "entidadProfesor.nombre AS nombreProfesor", "entidadProfesor.apellido AS apellidoProfesor")
                    ->where($nombreTabla . ".idAlumno", $idAlumno)
                    ->orderBy($nombreTabla . ".fechaInicio", "DESC")
                    ->first();
  }

  public static function obtenerProximaClaseXIdAlumno($idAlumno) {
    $nombreTabla = Clase::nombreTabla();
    return Clase::listarBase()
                    ->select($nombreTabla . ".*", "entidadProfesor.nombre AS nombreProfesor", "entidadProfesor.apellido AS apellidoProfesor")
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

  public static function listarXAlumno($idAlumno, $numeroPeriodo = NULL) {
    $nombreTabla = Clase::nombreTabla();
    $preClases = Clase::listarBase()
            ->select($nombreTabla . ".*", "entidadProfesor.nombre AS nombreProfesor", "entidadProfesor.apellido AS apellidoProfesor", DB::raw("max(historial.id) AS idHistorial"))
            ->where($nombreTabla . ".idAlumno", $idAlumno)
            ->whereIn($nombreTabla . ".estado", [EstadosClase::ConfirmadaProfesorAlumno, EstadosClase::Realizada]);
    if (!is_null($numeroPeriodo)) {
      $preClases->where($nombreTabla . ".numeroPeriodo", $numeroPeriodo)
              ->orderBy($nombreTabla . ".fechaInicio", "ASC");
    } else {
      $preClases->orderBy($nombreTabla . ".numeroPeriodo", "ASC")
              ->orderBy($nombreTabla . ".fechaInicio", "ASC");
    }
    $clases = $preClases->get();
    foreach ($clases as $clase) {
      $pagoProfesor = PagoProfesor::ObtenerXClase($clase["id"]);
      $pagoAlumno = PagoAlumno::ObtenerXClase($idAlumno, $clase["id"]);

      $clase->estadoPagoProfesor = (!is_null($pagoProfesor) ? $pagoProfesor["estado"] : NULL);
      $clase->estadoPagoAlumno = (!is_null($pagoAlumno) ? $pagoAlumno["estado"] : NULL);
    }
    return $clases;
  }

  public static function confirmarProfesorAlumno($datos) {
    if (Auth::user()->rol == RolesUsuario::Profesor) {
      $nombreTabla = Clase::nombreTabla();
      $idClase = Clase::listarXProfesor(Auth::user()->idEntidad)
                      ->where($nombreTabla . ".idAlumno", $datos["idAlumno"])
                      ->whereIn($nombreTabla . ".estado", [EstadosClase::Programada, EstadosClase::PendienteConfirmar])
                      ->orderBy($nombreTabla . ".fechaInicio", "ASC")->lists($nombreTabla . ".id")->first();
      if (isset($idClase) && $idClase != null) {
        $fechaConfirmacion = Carbon::now()->toDateTimeString();
        $clase = Clase::obtenerXId($datos["idAlumno"], $idClase);
        $clase->estado = EstadosClase::ConfirmadaProfesorAlumno;
        $clase->fechaConfirmacion = $fechaConfirmacion;
        $clase->fechaUltimaActualizacion = $fechaConfirmacion;
        $clase->save();

        //TODO: actualizar duración de la clase confirmada y las clases restantes
      }
    }
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
    $clases->whereIn($nombreTabla . ".estado", [EstadosClase::ConfirmadaProfesorAlumno, EstadosClase::Realizada]);
    return $clases;
  }

  public static function listarPropias($idAlumno, $datos = NULL) {
    $clases = Clase::listarBase();
    $nombreTabla = Clase::nombreTabla();

    if (isset($datos["estado"])) {
      $clases->where(Clase::nombreTabla() . ".estado", $datos["estado"]);
    }
    return $clases->where($nombreTabla . ".idAlumno", $idAlumno)
                    ->select($nombreTabla . ".id", $nombreTabla . ".idAlumno", $nombreTabla . ".idProfesor", $nombreTabla . ".numeroPeriodo", $nombreTabla . ".duracion", $nombreTabla . ".estado", $nombreTabla . ".fechaInicio", $nombreTabla . ".fechaFin", $nombreTabla . ".fechaConfirmacion", $nombreTabla . ".fechaCancelacion", $nombreTabla . (Auth::user()->rol == RolesUsuario::Alumno ? ".comentarioAlumno" : ".comentarioProfesor") . " AS comentarioEntidad", $nombreTabla . (Auth::user()->rol == RolesUsuario::Alumno ? ".comentarioParaAlumno" : ".comentarioParaProfesor") . " AS comentarioAdministrador", "entidadAlumno.nombre AS nombreAlumno", "entidadAlumno.apellido AS apellidoAlumno", "entidadProfesor.nombre AS nombreProfesor", "entidadProfesor.apellido AS apellidoProfesor");
  }

  public static function sincronizarEstados() {
    $clasesProgramadas = Clase::listarXEstados(EstadosClase::Programada)->where("fechaFin", "<=", Carbon::now())->get();
    foreach ($clasesProgramadas as $claseProgramada) {
      $claseProgramada->estado = EstadosClase::PendienteConfirmar;
      $claseProgramada->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
      $claseProgramada->save();
    }
  }

  public static function listarXEstados($estados) {
    return Clase::where("eliminado", 0)->whereIn("estado", (is_array($estados) ? $estados : [$estados]));
  }

  public static function listarIdsEntidadesXRangoFecha($fechaInicio, $fechaFin, $listarIdsProfesores = FALSE, $incluirClasesCanceladas = FALSE) {
    $clases = Clase::listarXRangoFecha($fechaInicio, $fechaFin, $incluirClasesCanceladas);
    return ($listarIdsProfesores ? $clases->groupBy("idProfesor")->lists("idProfesor") : $clases->groupBy("idAlumno")->lists("idAlumno"));
  }

  public static function listarXRangoFecha($fechaInicio, $fechaFin, $incluirClasesCanceladas = FALSE)/* - */ {
    $clases = Clase::where("eliminado", 0)->where(function ($q) use ($fechaInicio, $fechaFin) {
      $q->where(function ($q) use ($fechaInicio) {
        $q->where("fechaInicio", "<=", $fechaInicio)->where("fechaFin", ">=", $fechaInicio);
      })->orWhere(function ($q) use ($fechaFin) {
        $q->where("fechaInicio", "<=", $fechaFin)->where("fechaFin", ">=", $fechaFin);
      })->orWhere(function ($q) use ($fechaInicio, $fechaFin) {
        $q->where("fechaInicio", ">=", $fechaInicio)->where("fechaFin", "<=", $fechaFin);
      });
    });
    if (!$incluirClasesCanceladas) {
      $clases->where("estado", '!=', EstadosClase::Cancelada);
    }
    return $clases;
  }

  public static function datosGrupo($idAlumno, $datos) {
    if (isset($datos["ids"]) && is_array($datos["ids"])) {
      $nombreTabla = Clase::nombreTabla();
      $clases = Clase::listarBase()
              ->select($nombreTabla . ".*", "entidadProfesor.nombre AS nombreProfesor", "entidadProfesor.apellido AS apellidoProfesor", DB::raw("max(pago.id) AS idPago"))
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
          "idPago" => NULL,
          "idProfesor" => NULL,
          "nombreProfesor" => NULL,
          "apellidoProfesor" => NULL,
          "costoHoraProfesor" => NULL
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
          $datosGrupo["idProfesor"] = ($datosGrupo["idProfesor"] != $clase->idProfesor ? NULL : $datosGrupo["idProfesor"]);
          $datosGrupo["nombreProfesor"] = ($datosGrupo["nombreProfesor"] != $clase->nombreProfesor ? NULL : $datosGrupo["nombreProfesor"]);
          $datosGrupo["apellidoProfesor"] = ($datosGrupo["apellidoProfesor"] != $clase->apellidoProfesor ? NULL : $datosGrupo["apellidoProfesor"]);
          $datosGrupo["costoHoraProfesor"] = ($datosGrupo["costoHoraProfesor"] != $clase->costoHoraProfesor ? NULL : $datosGrupo["costoHoraProfesor"]);
        } else {
          $datosGrupo["numeroPeriodo"] = $clase->numeroPeriodo;
          $datosGrupo["estado"] = $clase->estado;
          $datosGrupo["fechaInicio"] = $clase->fechaInicio;
          $datosGrupo["duracion"] = $clase->duracion;
          $datosGrupo["costoHora"] = $clase->costoHora;
          $datosGrupo["idPago"] = $clase->idPago;
          $datosGrupo["idProfesor"] = $clase->idProfesor;
          $datosGrupo["nombreProfesor"] = $clase->nombreProfesor;
          $datosGrupo["apellidoProfesor"] = $clase->apellidoProfesor;
          $datosGrupo["costoHoraProfesor"] = $clase->costoHoraProfesor;
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
            ->select((($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::Mes || $datos["tipoBusquedaFecha"] == TiposBusquedaFecha::RangoMeses) ? DB::raw("MONTH(fechaInicio) AS mes") : (($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::Anio || $datos["tipoBusquedaFecha"] == TiposBusquedaFecha::RangoAnios) ? DB::raw("YEAR(fechaInicio) AS anho") : "fechaInicio")), "estado", DB::raw("count(id) AS total"))
            ->groupBy((($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::Mes || $datos["tipoBusquedaFecha"] == TiposBusquedaFecha::RangoMeses) ? DB::raw("MONTH(fechaInicio)") : (($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::Anio || $datos["tipoBusquedaFecha"] == TiposBusquedaFecha::RangoAnios) ? DB::raw("YEAR(fechaInicio)") : "fechaInicio")), "estado")
            ->orderBy("fechaInicio", "ASC");
    if (isset($datos["ids"]) && is_array($datos["ids"])) {
      return $clases->whereIn("id", $datos["ids"])->get();
    } else {
      return [];
    }
  }

  public static function registrarXDatosPago($idAlumno, $idPago, $datos) {
    $clasesGeneradas = Clase::generarXDatosPago($idAlumno, $datos);

    for ($i = 0; $i < count($clasesGeneradas); $i++) {
      if (!isset($clasesGeneradas[$i]["duracion"])) {
        continue;
      }
      $datos["fechaInicio"] = $clasesGeneradas[$i]["fechaInicio"];
      $datos["fechaFin"] = $clasesGeneradas[$i]["fechaFin"];
      $datos["duracion"] = $clasesGeneradas[$i]["duracion"];
      $datos["costoHora"] = $datos["costoXHoraClase"];
      $datos["numeroPeriodo"] = $datos["periodoClases"];
      $datos["notificar"] = 0;
      $datos["estado"] = EstadosClase::Programada;
      $datos["idPago"] = $idPago;

      Clase::registrarActualizar($idAlumno, $datos);
    }
  }

  public static function generarXDatosPago($idAlumno, $datos) {
    $clasesGeneradas = [];

    $preHorasPagadas = ((float) $datos["monto"] / (float) $datos["costoXHoraClase"]);
    $horasPagadas = ($preHorasPagadas - fmod($preHorasPagadas, 0.5));

    $fechaInicioClase = Carbon::createFromFormat("d/m/Y H:i:s", $datos["fechaInicioClases"] . " 00:00:00");
    $horarioAlumno = Horario::obtenerXIdEntidad($idAlumno);

    $duracionTotalSeg = 0;
    while ($duracionTotalSeg < ($horasPagadas * 3600)) {
      foreach ($horarioAlumno as $datHorarioAlumno) {
        if (($fechaInicioClase->dayOfWeek != 0 ? $fechaInicioClase->dayOfWeek : 7) == $datHorarioAlumno->numeroDiaSemana) {
          $fechaInicio = Carbon::createFromFormat("d/m/Y H:i:s", $fechaInicioClase->format("d/m/Y") . " " . $datHorarioAlumno->horaInicio);

          $preFechaFin = Carbon::createFromFormat("d/m/Y H:i:s", $fechaInicioClase->format("d/m/Y") . " " . $datHorarioAlumno->horaFin);
          $tiempoAdicionalSeg = (($duracionTotalSeg + $preFechaFin->diffInSeconds($fechaInicio)) - ($horasPagadas * 3600));
          $fechaFin = (($tiempoAdicionalSeg > 0) ? $preFechaFin->subSeconds($tiempoAdicionalSeg) : $preFechaFin);

          $duracion = $fechaFin->diffInSeconds($fechaInicio);
          $clasesGeneradas[] = ["fechaInicio" => $fechaInicio, "fechaFin" => $fechaFin, "duracion" => $duracion];

          $duracionTotalSeg += $duracion;
        }
      }
      $fechaInicioClase = $fechaInicioClase->addDay();
    }
    $clasesGeneradas["montoRestante"] = ($datos["monto"] - ($horasPagadas * (float) $datos["costoXHoraClase"]));
    return $clasesGeneradas;
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
      $claseCancelada->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
      $claseCancelada->save();
      Historial::eliminarXIdClase($datos["idClase"]);

      if ($datos["reprogramarCancelacion"] == 1) {
        unset($datos["idClase"]);
        $datos["numeroPeriodo"] = $claseCancelada["numeroPeriodo"];
        $datos["notificar"] = ((isset($claseCancelada["idHistorial"])) ? 1 : 0);
        $datos["idClaseCancelada"] = $claseCancelada["id"];
        $datos["estado"] = EstadosClase::Programada;
        $claseReprogramada = Clase::registrarActualizar($idAlumno, $datos);
        return $claseReprogramada->numeroPeriodo;
      }
    }
    return $claseCancelada->numeroPeriodo;
  }

  public static function actualizarEstado($idAlumno, $datos) {
    $clase = Clase::obtenerXId($idAlumno, $datos["idClase"]);
    $clase->estado = $datos["estado"];
    $clase->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
    $clase->save();
  }

  public static function actualizarGrupo($idAlumno, $datos) {
    $nroPeriodo = 1;
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

        if (isset($datos["costoHora"])) {
          $datosActualizar["costoHora"] = $datos["costoHora"];
        }

        if ($datos["editarDatosProfesor"] == 1) {
          $idProfesor = $datos["idDocente"];
          if (Postulante::verificarExistencia($datos["idDocente"])) {
            $idProfesor = Postulante::registrarProfesor($datos["idDocente"]);
          }
          $datosActualizar["idProfesor"] = $idProfesor;
          $datosActualizar["costoHoraProfesor"] = $datos["pagoXHoraProfesor"];
        }

        $claseSel->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
        $claseSel->update($datosActualizar);
        $nroPeriodo = $claseSel->numeroPeriodo;
        if ($datos["editarDatosPago"] == 1 && isset($datos["idPago"])) {
          PagoClase::registrarActualizar($datos["idPago"], $clase->id, $idAlumno);
        }
      }
    }
    return $nroPeriodo;
  }

  public static function registrarActualizar($idAlumno, $datos) {
    if (!(isset($datos["fechaInicio"]) && isset($datos["fechaFin"]))) {
      $datos["fechaInicio"] = Carbon::createFromFormat("d/m/Y H:i:s", $datos["fecha"] . " 00:00:00")->addSeconds($datos["horaInicio"]);
      $datos["fechaFin"] = clone $datos["fechaInicio"];
      $datos["fechaFin"]->addSeconds($datos["duracion"]);
    }

    $datos["idAlumno"] = $idAlumno;

    $idProfesor = $datos["idDocente"];
    if (Postulante::verificarExistencia($datos["idDocente"])) {
      $idProfesor = Postulante::registrarProfesor($datos["idDocente"]);
    }
    $datos["idProfesor"] = $idProfesor;
    $datos["costoHoraProfesor"] = $datos["pagoXHoraProfesor"];

    $notificar = ($datos["notificar"] == 1);
    $clase = ((isset($datos["idClase"])) ? Clase::obtenerXId($idAlumno, $datos["idClase"]) : NULL);
    if (isset($clase)) {
      $fechaInicio = new Carbon($clase->fechaInicio);
      $tieneHistorialReg = (!is_null($clase->idHistorial));
      $cambioFechaInicio = ($datos["fechaInicio"]->ne($fechaInicio));

      if (($clase->estado == EstadosClase::Cancelada) || ($tieneHistorialReg && !$notificar) || ($tieneHistorialReg && $cambioFechaInicio)) {
        Historial::eliminarXIdClase($datos["idClase"]);
      }
      if (($tieneHistorialReg && !$cambioFechaInicio && $notificar) || ($clase->estado == EstadosClase::Cancelada)) {
        $notificar = FALSE;
      }
      $clase->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
      $clase->update($datos);
    } else {
      $clase = new Clase($datos);
      $clase->fechaRegistro = Carbon::now()->toDateTimeString();
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
    return $clase;
  }

  public static function obtenerXId($idAlumno, $id) {
    $nombreTabla = Clase::nombreTabla();
    return Clase::listarBase()
                    ->select(DB::raw(
                                    $nombreTabla . ".*, 
                            entidadAlumno.nombre AS nombreAlumno, 
                            entidadAlumno.apellido AS apellidoAlumno, 
                            entidadProfesor.nombre AS nombreProfesor, 
                            entidadProfesor.apellido AS apellidoProfesor, 
                            max(pagoAlumno.id) AS idPago"))
                    ->where($nombreTabla . ".idAlumno", $idAlumno)
                    ->where($nombreTabla . ".id", $id)
                    ->orderBy($nombreTabla . ".fechaInicio", "ASC")
                    ->firstOrFail();
  }

  // </editor-fold>
}
