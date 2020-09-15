<?php

namespace App\Models;

use DB;
use Log;
use Auth;
use Config;
use Carbon\Carbon;
use App\Helpers\Enum\EstadosClase;
use App\Helpers\Enum\RolesUsuario;
use Illuminate\Database\Eloquent\Model;

class Clase extends Model {

  public $timestamps = false;
  protected $table = "clase";
  protected $fillable = [
      "idAlumno",
      "idProfesor",
      "numeroPeriodo",
      "fechaInicio",
      "fechaFin",
      "fechaConfirmacion",
      "duracion",
      "comentarioAlumno",
      "comentarioProfesor",
      "comentarioParaAlumno",
      "comentarioParaProfesor",
      "fechaCancelacion",
      "estado"
  ];

  public static function nombreTabla() {
    $modeloClase = new Clase();
    $nombreTabla = $modeloClase->getTable();
    unset($modeloClase);
    return $nombreTabla;
  }

  public static function listarBase($incluirSoloConfirmadasYRealizadas = FALSE, $incluirSoloRealizadas = FALSE) {
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
            ->join($nombreTablaPago . " AS pagoAlumno", function ($q) use ($nombreTablaClase, $nombreTablaPagoClase, $nombreTablaPagoAlumno) {
              $q->on("pagoAlumno.id", "IN", DB::raw("(SELECT idPago 
                                                        FROM " . $nombreTablaPagoClase . "
                                                        WHERE idClase = " . $nombreTablaClase . ".id)"))
              ->on("pagoAlumno.id", "IN", DB::raw("(SELECT idPago 
                                                      FROM " . $nombreTablaPagoAlumno . "
                                                      WHERE idAlumno = " . $nombreTablaClase . ".idAlumno)"))
              ->on("pagoAlumno.eliminado", "=", DB::raw("0"));
            })
            ->join($nombreTablaPagoClase . " AS pagoClaseAlumno", function ($q) use ($nombreTablaClase) {
              $q->on("pagoClaseAlumno.idPago", "=", "pagoAlumno.id")
              ->on("pagoClaseAlumno.idClase", "=", $nombreTablaClase . ".id");
            })
            //Datos del profesor
            ->leftJoin($nombreTablaEntidad . " as entidadProfesor", function ($q) use($nombreTablaClase) {
              $q->on($nombreTablaClase . ".idProfesor", "=", "entidadProfesor.id")
              ->on("entidadProfesor.eliminado", "=", DB::raw("0"));
            })
            //Pago al profesor asociado
            ->leftJoin($nombreTablaPago . " AS pagoProfesor", function ($q) use ($nombreTablaClase, $nombreTablaPagoClase, $nombreTablaPagoProfesor) {
              $q->on("pagoProfesor.id", "IN", DB::raw("(SELECT idPago 
                                                          FROM " . $nombreTablaPagoClase . "
                                                          WHERE idClase = " . $nombreTablaClase . ".id)"))
              ->on("pagoProfesor.id", "IN", DB::raw("(SELECT idPago 
                                                        FROM " . $nombreTablaPagoProfesor . "
                                                        WHERE idProfesor = " . $nombreTablaClase . ".idProfesor)"))
              ->on("pagoProfesor.eliminado", "=", DB::raw("0"));
            })
            ->whereRaw("(pagoAlumno.eliminado = 0 AND pagoAlumno.id > 0)")
            ->whereRaw("(pagoProfesor.id IS NULL OR pagoProfesor.id > 0)")
            ->where($nombreTablaClase . ".eliminado", DB::raw("0"))
            ->groupBy($nombreTablaClase . ".id")
            ->distinct();
    if ($incluirSoloConfirmadasYRealizadas) {
      $clases->whereIn($nombreTablaClase . ".estado", [EstadosClase::ConfirmadaProfesor, EstadosClase::ConfirmadaProfesorAlumno, EstadosClase::Realizada]);
    } else if ($incluirSoloRealizadas) {
      $clases->whereIn($nombreTablaClase . ".estado", [EstadosClase::Realizada]);
    }
    return $clases;
  }

  public static function listar($incluirSoloConfirmadasYRealizadas = FALSE) {
    $nombreTablaClase = Clase::nombreTabla();
    return Clase::listarBase($incluirSoloConfirmadasYRealizadas)
                    ->select(DB::raw(
                                    $nombreTablaClase . ".*, 
                                    entidadAlumno.nombre AS nombreAlumno, 
                                    entidadAlumno.apellido AS apellidoAlumno, 
                                    GROUP_CONCAT(pagoAlumno.id SEPARATOR ', ') AS idsPagosAlumno,
                                    entidadProfesor.nombre AS nombreProfesor, 
                                    entidadProfesor.apellido AS apellidoProfesor, 
                                    GROUP_CONCAT(pagoProfesor.id SEPARATOR ', ') AS idsPagosProfesor")
                    )
                    ->groupBy($nombreTablaClase . ".id");
  }

  public static function listarXAlumno($idAlumno, $incluirSoloConfirmadasYRealizadas = TRUE) {
    return Clase::listar($incluirSoloConfirmadasYRealizadas)->where(Clase::nombreTabla() . ".idAlumno", $idAlumno);
  }

  public static function listarXProfesor($idProfesor, $incluirSoloConfirmadasYRealizadas = TRUE) {
    return Clase::listar($incluirSoloConfirmadasYRealizadas)->where(Clase::nombreTabla() . ".idProfesor", $idProfesor);
  }

  public static function obtenerXId($id, $idAlumno = NULL) {
    $nombreTablaClase = Clase::nombreTabla();
    $clase = Clase::listarBase()->where($nombreTablaClase . ".id", $id);
    if (isset($idAlumno)) {
      $clase->where($nombreTablaClase . ".idAlumno", $idAlumno);
    }
    return $clase->select(DB::raw(
                            $nombreTablaClase . ".*, 
                            entidadAlumno.nombre AS nombreAlumno, 
                            entidadAlumno.apellido AS apellidoAlumno, 
                            entidadProfesor.nombre AS nombreProfesor, 
                            entidadProfesor.apellido AS apellidoProfesor,
                            max(pagoAlumno.id) AS idPago,
                            SUM(pagoAlumno.costoXHoraClase)/COUNT(pagoAlumno.id) AS costoPromedioXHoraClase,
                            SUM(pagoAlumno.pagoXHoraProfesor)/COUNT(pagoAlumno.id) AS pagoPromedioXHoraProfesor")
            )->firstOrFail();
  }

  public static function listarPeriodosXIdAlumno($idAlumno) {
    return Clase::select(DB::raw("numeroPeriodo, 
                                  min(fechaInicio) AS fechaInicio, 
                                  max(fechaFin) AS fechaFin, 
                                  sum(duracion) AS horasTotal"))
                    ->where("idAlumno", $idAlumno)
                    ->where("eliminado", 0)
                    ->groupBy("numeroPeriodo");
  }

  public static function totalPeriodosXIdAlumno($idAlumno) {
    $sub = Clase::listarPeriodosXIdAlumno($idAlumno);
    return DB::table(DB::raw("({$sub->toSql()}) as sub"))->mergeBindings($sub->getQuery())->count();
  }

  public static function calendario($datos) {
    $nombreTablaClase = Clase::nombreTabla();

    if ($datos["tipoEntidad"] !== "0" && !is_null($datos["idProfesor"])) {
      $preClases = Clase::listarXProfesor($datos["idProfesor"]);
    } else if (!is_null($datos["idAlumno"])) {
      $preClases = Clase::listarXAlumno($datos["idAlumno"]);
    } else {
      $preClases = Clase::listar(TRUE);
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

  public static function actualizarEstado($id, $estado) {
    $clase = Clase::obtenerXId($id);
    $clase->estado = $estado;
    $clase->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
    $clase->save();
  }

  public static function actualizarComentarios($id, $datos) {
    $clase = Clase::obtenerXId($id);

    if (Auth::user()->rol == RolesUsuario::Alumno && Auth::user()->idEntidad == $clase->idAlumno) {
      $clase->comentarioAlumno = $datos["comentario"];
    } else if (Auth::user()->rol == RolesUsuario::Profesor && Auth::user()->idEntidad == $clase->idProfesor) {
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
      Clase::obtenerXId($id, $idAlumno);
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

  public static function eliminarXIdAdlumno($idAlumno) {
    $clases = Clase::where("eliminado", 0)->where("idAlumno", $idAlumno)->get();
    foreach ($clases as $clase) {
      Clase::eliminar($idAlumno, $clase->id);
    }
  }

  public static function eliminar($idAlumno, $id) {
    if (Clase::verificarExistencia($idAlumno, $id)) {
      $clase = Clase::obtenerXId($id);
      $clase->eliminado = 1;
      $clase->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
      $clase->save();
      Notificacion::eliminarXIdClase($id);

      //Se actualiza la bolsa de horas del alumno
      $idsPagosRelacionados = PagoClase::where("idClase", $id)->lists("idPago")->toArray();
      foreach ($idsPagosRelacionados as $idPagoRelacionado) {
        if (PagoAlumno::verificarExistencia($idAlumno, $idPagoRelacionado)) {
          $pagoXBolsaHoras = AlumnoBolsaHoras::where("idAlumno", $idAlumno)->where("idPago", $idPagoRelacionado)->first();
          if (!isset($pagoXBolsaHoras)) {
            $bolsaHoras = new AlumnoBolsaHoras([
                "idAlumno" => $idAlumno,
                "idPago" => $idPagoRelacionado
            ]);
            $bolsaHoras->save();
          }
        }
      }
    }
  }
}
