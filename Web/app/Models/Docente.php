<?php

namespace App\Models;

use Carbon\Carbon;
use App\Helpers\Enum\TiposEntidad;
use App\Helpers\Enum\GenerosEntidad;
use Illuminate\Database\Eloquent\Model;

class Docente extends Model {

    protected static function listarDisponiblesXDatosPago($idAlumno, $datos) {
        $idsDisponiblesSel = [];
        $auxCont = 1;

        foreach (Clase::generarXDatosPago($idAlumno, $datos) as $claseGenerada) {
            if (isset($claseGenerada["fechaInicio"]) && isset($claseGenerada["fechaInicio"])) {
                $idsNoDisponibles = Clase::listarIdsEntidadesXRangoFecha($claseGenerada["fechaInicio"], $claseGenerada["fechaFin"], TRUE);
                $idsDisponibles = Horario::listarIdsEntidadesXRangoFecha($claseGenerada["fechaInicio"]->dayOfWeek, $claseGenerada["fechaInicio"]->format("H:i:s"), $claseGenerada["fechaFin"]->format("H:i:s"), $datos["tipoDocente"]);
                $idsDisponiblesSel = (count($idsDisponiblesSel) == 0 && $auxCont == 1 ? array_diff($idsDisponibles->toArray(), $idsNoDisponibles->toArray()) : array_intersect($idsDisponiblesSel, array_diff($idsDisponibles->toArray(), $idsNoDisponibles->toArray())));
                $auxCont++;
            }
        }
        $generoDocentePago = $datos["generoDocente"];
        $idCursoDocentePago = $datos["idCursoDocente"];
        $docentes = ($datos["tipoDocente"] == TiposEntidad::Profesor ? Profesor::listar() : Postulante::Listar());

        return $docentes->where(function ($q) use ($generoDocentePago) {
                    $q->whereNull("entidad.genero")->orWhereIn("entidad.genero", ($generoDocentePago != "" ? [$generoDocentePago] : array_keys(GenerosEntidad::listar())));
                })->where(function ($q) use ($idCursoDocentePago) {
                    $q->whereNull("entidadCurso.idCurso")->orWhereIn("entidadCurso.idCurso", ($idCursoDocentePago != "" ? [$idCursoDocentePago] : array_keys(Curso::listarSimple()->toArray())));
                })->where("entidad.tipo", $datos["tipoDocente"])->whereIn("entidad.id", $idsDisponiblesSel);
    }

    protected static function listarDisponiblesXDatosClase($datos) {
        $fechaInicio = Carbon::createFromFormat("d/m/Y H:i:s", $datos["fecha"] . " 00:00:00")->addSeconds($datos["horaInicio"]);       
        $fechaFin = clone $fechaInicio;
        $fechaFin->addSeconds($datos["duracion"]);
        
        
        $idsNoDisponibles = Clase::listarIdsEntidadesXRangoFecha($fechaInicio, $fechaFin, TRUE);
        $idsDisponibles = Horario::listarIdsEntidadesXRangoFecha($fechaInicio->dayOfWeek, $fechaInicio->format("H:i:s"), $fechaFin->format("H:i:s"), $datos["tipoDocente"]);
        $idsDisponiblesSel = array_diff($idsDisponibles->toArray(), $idsNoDisponibles->toArray());

        $generoDocenteClase = $datos["generoDocente"];
        $idCursoDocenteClase = $datos["idCursoDocente"];
        $docentes = ($datos["tipoDocente"] == TiposEntidad::Profesor ? Profesor::listar() : Postulante::Listar());

        return $docentes->where(function ($q) use ($generoDocenteClase) {
                    $q->whereNull("entidad.genero")->orWhereIn("entidad.genero", ($generoDocenteClase != "" ? [$generoDocenteClase] : array_keys(GenerosEntidad::listar())));
                })->where(function ($q) use ($idCursoDocenteClase) {
                    $q->whereNull("entidadCurso.idCurso")->orWhereIn("entidadCurso.idCurso", ($idCursoDocenteClase != "" ? [$idCursoDocenteClase] : array_keys(Curso::listarSimple()->toArray())));
                })->where("entidad.tipo", $datos["tipoDocente"])->whereIn("entidad.id", $idsDisponiblesSel);
    }

    protected static function verificarExistencia($idEntidad) {
        if (!(isset($idEntidad) && $idEntidad != "" && is_numeric($idEntidad))) {
            return FALSE;
        }
        $numProfesores = Entidad::where("id", $idEntidad)->where("tipo", TiposEntidad::Profesor)->count();
        $numPostulantes = Entidad::where("id", $idEntidad)->where("tipo", TiposEntidad::Postulante)->count();
        return ($numProfesores > 0 || $numPostulantes > 0);
    }

}
