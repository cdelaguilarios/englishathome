<?php

namespace App\Models;

use Carbon\Carbon;
use App\Helpers\Enum\TiposEntidad;
use App\Helpers\Enum\SexosEntidad;
use Illuminate\Database\Eloquent\Model;

class Docente extends Model {

  public static function listarIdsDisponiblesXDatosClasesGeneradas($clasesGeneradas, $tipoDocente = NULL) {
    $idsDisponiblesSel = [];
    $auxCont = 1;

    foreach ($clasesGeneradas as $claseGenerada) {
      if (isset($claseGenerada["fechaInicio"]) && isset($claseGenerada["fechaFin"])) {
        $fechaInicioOri = clone $claseGenerada["fechaInicio"];
        $fechaFinOri = clone $claseGenerada["fechaFin"];
        $fechaInicioCop = clone $fechaInicioOri;
        $fechaFinCop = clone $fechaFinOri;
        
        $idsNoDisponibles = Clase::listarIdsEntidadesXRangoFecha($fechaInicioOri->subHour(), $fechaFinOri->addHour(), TRUE);
        $idsDisponibles = Horario::listarIdsEntidadesXRangoFecha($fechaInicioCop->dayOfWeek, $fechaInicioCop->format("H:i:s"), $fechaFinCop->format("H:i:s"), $tipoDocente);
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
    if (count($datos["idsClases"]) > 0) {
      $idsDisponiblesSel = [];
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
        $idsNoDisponibles = Clase::listarIdsEntidadesXRangoFecha($fechaInicio->subHour(), $fechaFin->addHour(), TRUE);
        $idsDisponibles = Horario::listarIdsEntidadesXRangoFecha($auxFechaInicio->dayOfWeek, $auxFechaInicio->format("H:i:s"), $auxFechaFin->format("H:i:s"), $datos["tipoDocente"]);
        $idsDisponiblesSel = ($auxCont == 1 ? array_diff($idsDisponibles->toArray(), $idsNoDisponibles->toArray()) : array_intersect($idsDisponiblesSel, array_diff($idsDisponibles->toArray(), $idsNoDisponibles->toArray())));
        $auxCont++;
      }
    } else {
      $fechaInicio = Carbon::createFromFormat("d/m/Y H:i:s", $datos["fecha"] . " 00:00:00")->addSeconds($datos["horaInicio"]);
      $fechaFin = clone $fechaInicio;
      $fechaFin->addSeconds($datos["duracion"]);

      $auxFechaInicio = clone $fechaInicio;
      $auxFechaFin = clone $fechaFin;
      $idsNoDisponibles = Clase::listarIdsEntidadesXRangoFecha($fechaInicio->subHour(), $fechaFin->addHour(), TRUE);
      $idsDisponibles = Horario::listarIdsEntidadesXRangoFecha($auxFechaInicio->dayOfWeek, $auxFechaInicio->format("H:i:s"), $auxFechaFin->format("H:i:s"), $datos["tipoDocente"]);
      $idsDisponiblesSel = array_diff($idsDisponibles->toArray(), $idsNoDisponibles->toArray());
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
            })->where("entidad.tipo", $datos["tipoDocente"]);
  }

}
