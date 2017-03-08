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
        $idsNoDisponibles = Clase::listarIdsEntidadesXRangoFecha($claseGenerada["fechaInicio"]->subHour(), $claseGenerada["fechaFin"]->addHour(), TRUE);
        $idsDisponibles = Horario::listarIdsEntidadesXRangoFecha($claseGenerada["fechaInicio"]->dayOfWeek, $claseGenerada["fechaInicio"]->format("H:i:s"), $claseGenerada["fechaFin"]->format("H:i:s"), $tipoDocente);
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
    $fechaInicio = Carbon::createFromFormat("d/m/Y H:i:s", $datos["fecha"] . " 00:00:00")->addSeconds($datos["horaInicio"]);
    $fechaFin = clone $fechaInicio;
    $fechaFin->addSeconds($datos["duracion"]);

    $idsNoDisponibles = Clase::listarIdsEntidadesXRangoFecha($fechaInicio->subHour(), $fechaFin->addHour(), TRUE);
    $idsDisponibles = Horario::listarIdsEntidadesXRangoFecha($fechaInicio->dayOfWeek, $fechaInicio->format("H:i:s"), $fechaFin->format("H:i:s"), $datos["tipoDocente"]);
    $idsDisponiblesSel = array_diff($idsDisponibles->toArray(), $idsNoDisponibles->toArray());
    return Docente::listarXFiltrosBusqueda($datos)->whereIn("entidad.id", $idsDisponiblesSel);
  }

  public static function listarDisponiblesXFecha($datos) {
    $fechaInicio = Carbon::createFromFormat("d/m/Y H:i:s", $datos["fechaInicio"]);
    $fechaFin = Carbon::createFromFormat("d/m/Y H:i:s", $datos["fechaFin"]);
    if ($fechaInicio >= $fechaFin) {
      return Profesor::whereNull("idEntidad");
    }

    $idsNoDisponibles = Clase::listarIdsEntidadesXRangoFecha($fechaInicio->subHour(), $fechaFin->addHour(), TRUE);
    return Docente::listarXFiltrosBusqueda($datos)->whereNotIn("entidad.id", $idsNoDisponibles);
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
