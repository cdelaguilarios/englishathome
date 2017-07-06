<?php

namespace App\Models;

use Config;
use Carbon\Carbon;
use App\Helpers\Enum\TiposEntidad;
use App\Helpers\Enum\SexosEntidad;
use App\Helpers\Enum\EstadosPostulante;
use Illuminate\Database\Eloquent\Model;

class Docente extends Model {

  public static function listarDisponibles($datos = NULL) {
    $idsNoDisponibles = (isset($datos["horarioDocente"]) ? Clase::listarIdsEntidadesXHorario($datos["horarioDocente"], TRUE) : []);
    $idsDisponibles = (isset($datos["horarioDocente"]) ? Horario::listarIdsEntidadesXHorario($datos["horarioDocente"], $datos["tipoDocente"]) : []);
    $idsDisponiblesSel = array_diff($idsDisponibles, $idsNoDisponibles);
    
    $docentes = Docente::listarXFiltrosBusqueda($datos)->whereIn("entidad.id", $idsDisponiblesSel);
    if (isset($datos["estadoDocente"])) {
      $docentes->where("entidad.estado", $datos["estadoDocente"]);
    }
    return $docentes;
  }

  public static function listarIdsDisponiblesXDatosClasesGeneradas($clasesGeneradas, $tipoDocente = NULL) {
    $idsDisponiblesSel = [];
    $auxCont = 1;

    foreach ($clasesGeneradas as $claseGenerada) {
      if (isset($claseGenerada["fechaInicio"]) && isset($claseGenerada["fechaFin"])) {
        $fechaInicioOri = clone $claseGenerada["fechaInicio"];
        $fechaFinOri = clone $claseGenerada["fechaFin"];
        $fechaInicioCop = clone $fechaInicioOri;
        $fechaFinCop = clone $fechaFinOri;

        $idsNoDisponibles = Clase::listarIdsEntidadesXRangoFecha($fechaInicioOri->subMinutes((int) Config::get("eah.rangoMinutosBusquedaHorarioDocente")), $fechaFinOri->addMinutes((int) Config::get("eah.rangoMinutosBusquedaHorarioDocente")), TRUE);
        $idsDisponibles = Horario::listarIdsEntidadesXRangoFecha(($fechaInicioCop->dayOfWeek != 0 ? $fechaInicioCop->dayOfWeek : 7), $fechaInicioCop->format("H:i:s"), $fechaFinCop->format("H:i:s"), $tipoDocente);
        $idsDisponiblesSel = ($auxCont == 1 ? array_diff($idsDisponibles->toArray(), $idsNoDisponibles->toArray()) : array_intersect($idsDisponiblesSel, array_diff($idsDisponibles->toArray(), $idsNoDisponibles->toArray())));
        $auxCont++;
      }
    }
    return $idsDisponiblesSel;
  }

  public static function listarDisponiblesXDatosPago($idAlumno, $datos) {
    $idsDisponiblesSel = Docente::listarIdsDisponiblesXDatosClasesGeneradas(Clase::generarXDatosPago($idAlumno, $datos), $datos["tipoDocente"]);
    return Docente::listarXFiltrosBusqueda($datos)->whereIn("entidad.id", $idsDisponiblesSel);
  }

  public static function listarDisponiblesXDatosClase($datos) {
    $idsDisponiblesSel = [];
    if (count($datos["idsClases"]) > 0) {
      $auxCont = 1;
      $clases = Clase::listar()->whereIn(Clase::nombreTabla() . ".id", $datos["idsClases"])->orderBy(Clase::nombreTabla() . ".fechaInicio")->get();
      foreach ($clases as $clase) {
        $fechaInicio = new Carbon($clase->fechaInicio);
        if (isset($datos["horaInicio"])) {
          $fechaInicio->setTime(0, 0, 0)->addSeconds($datos["horaInicio"]);
        }
        $fechaFin = new Carbon($clase->fechaFin);
        if (isset($datos["duracion"])) {
          $fechaFin = clone $fechaInicio;
          $fechaFin->addSeconds($datos["duracion"]);
        }

        $auxFechaInicio = clone $fechaInicio;
        $auxFechaFin = clone $fechaFin;
        $idsNoDisponibles = Clase::listarIdsEntidadesXRangoFecha($fechaInicio->subMinutes((int) Config::get("eah.rangoMinutosBusquedaHorarioDocente")), $fechaFin->addMinutes((int) Config::get("eah.rangoMinutosBusquedaHorarioDocente")), TRUE);
        $idsDisponibles = Horario::listarIdsEntidadesXRangoFecha(($auxFechaInicio->dayOfWeek != 0 ? $auxFechaInicio->dayOfWeek : 7), $auxFechaInicio->format("H:i:s"), $auxFechaFin->format("H:i:s"), $datos["tipoDocente"]);
        $idsDisponiblesSel = ($auxCont == 1 ? array_diff($idsDisponibles->toArray(), $idsNoDisponibles->toArray()) : array_intersect($idsDisponiblesSel, array_diff($idsDisponibles->toArray(), $idsNoDisponibles->toArray())));
        $auxCont++;
      }
    } else {
      $fechaInicio = Carbon::createFromFormat("d/m/Y H:i:s", $datos["fecha"] . " 00:00:00")->addSeconds($datos["horaInicio"]);
      $fechaFin = clone $fechaInicio;
      $fechaFin->addSeconds($datos["duracion"]);

      $auxFechaInicio = clone $fechaInicio;
      $auxFechaFin = clone $fechaFin;
      $idsNoDisponibles = Clase::listarIdsEntidadesXRangoFecha($fechaInicio->subMinutes((int) Config::get("eah.rangoMinutosBusquedaHorarioDocente")), $fechaFin->addMinutes((int) Config::get("eah.rangoMinutosBusquedaHorarioDocente")), TRUE);
      $idsDisponibles = Horario::listarIdsEntidadesXRangoFecha(($auxFechaInicio->dayOfWeek != 0 ? $auxFechaInicio->dayOfWeek : 7), $auxFechaInicio->format("H:i:s"), $auxFechaFin->format("H:i:s"), $datos["tipoDocente"]);

      $idsDisponiblesSel = array_diff($idsDisponibles->toArray(), $idsNoDisponibles->toArray());

      if (isset($datos["idClase"])) {
        $clase = Clase::where("id", $datos["idClase"])->where("eliminado", 0)->first();
        if (isset($clase) && isset($clase->idProfesor) && in_array($clase->idProfesor, $idsDisponibles->toArray())) {
          array_push($idsDisponiblesSel, $clase->idProfesor);
        }
      }
    }
    return Docente::listarXFiltrosBusqueda($datos)->whereIn("entidad.id", $idsDisponiblesSel);
  }

  public static function verificarExistencia($idEntidad) {
    if (!(isset($idEntidad) && $idEntidad != "" && is_numeric($idEntidad))) {
      return FALSE;
    }
    $numProfesores = Entidad::where("id", $idEntidad)->where("tipo", TiposEntidad::Profesor)->count();
    $numPostulantes = Entidad::where("id", $idEntidad)->where("tipo", TiposEntidad::Postulante)->count();
    return ($numProfesores > 0 || $numPostulantes > 0);
  }

  private static function listarXFiltrosBusqueda($datos) {
    $sexoDocentePago = $datos["sexoDocente"];
    $idCursoDocentePago = $datos["idCursoDocente"];
    $docentes = ($datos["tipoDocente"] == TiposEntidad::Profesor ? Profesor::listar() : Postulante::listar());

    return $docentes->where(function ($q) use ($sexoDocentePago) {
              $q->whereNull("entidad.sexo")->orWhereIn("entidad.sexo", ($sexoDocentePago != "" ? [$sexoDocentePago] : array_keys(SexosEntidad::listar())));
            })->where(function ($q) use ($idCursoDocentePago) {
              $q->whereNull("entidadCurso.idCurso")->orWhereIn("entidadCurso.idCurso", ($idCursoDocentePago != "" ? [$idCursoDocentePago] : array_keys(Curso::listarSimple()->toArray())));
            })->where("entidad.tipo", $datos["tipoDocente"])->where("entidad.estado", '!=', EstadosPostulante::ProfesorRegistrado);
  }

  public static function registrarActualizarAudio($id, $audio) {
    if (isset($audio) && !is_null($audio)) {
      $entidad = Entidad::ObtenerXId($id);
      $docente = ($entidad->tipo == TiposEntidad::Profesor ? Profesor::obtenerXId($id, TRUE) : ($entidad->tipo == TiposEntidad::Postulante ? Postulante::obtenerXId($id, TRUE) : NULL));
      if ($docente !== NULL) {
        if (isset($docente->audio) && $docente->audio != "") {
          Archivo::eliminar($docente->audio);
        }
        $docente->audio = Archivo::registrar($id . "_pa_", $audio);
        $docente->save();
      }
    }
  }

}
