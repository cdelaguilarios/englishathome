<?php

namespace App\Models;

use DB;
use Carbon\Carbon;
use App\Helpers\Enum\TiposEntidad;
use App\Helpers\Enum\SexosEntidad;
use App\Helpers\Enum\EstadosPostulante;
use Illuminate\Database\Eloquent\Model;

class Docente extends Model {

  public static function listarDisponibles($datos = NULL) {
    $nombreTabla = Postulante::nombreTabla();
    $postulantes = Postulante::select($nombreTabla . ".*", "entidad.*", DB::raw('CONCAT(entidad.nombre, " ", entidad.apellido) AS nombreCompleto'))
                    ->leftJoin(Entidad::nombreTabla() . " as entidad", $nombreTabla . ".idEntidad", "=", "entidad.id")
                    ->leftJoin(EntidadCurso::nombreTabla() . " as entidadCurso", $nombreTabla . ".idEntidad", "=", "entidadCurso.idEntidad")
                    ->where("entidad.eliminado", 0)->groupBy("entidad.id")->distinct();

    if (isset($datos["estado"])) {
      $postulantes->where("entidad.estado", $datos["estado"]);
    }
    return $postulantes;
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
            })->where("entidad.tipo", $datos["tipoDocente"])->where("entidad.estado", '!=', EstadosPostulante::ProfesorRegistrado);
  }

  public static function procesarDocumentosPersonales($documentosPersonales, $datos) {
    if (!is_null($datos["nombresDocumentosPersonalesEliminados"])) {
      $nombresDocumentosPersonalesEliminados = explode(",", $datos["nombresDocumentosPersonalesEliminados"]);
      for ($i = 0; $i < count($nombresDocumentosPersonalesEliminados); $i++) {
        if (trim($nombresDocumentosPersonalesEliminados[$i]) == "") {
          continue;
        }
        try {
          Archivo::eliminar($nombresDocumentosPersonalesEliminados[$i]);
          $documentosPersonalesSel = explode(",", $documentosPersonales);
          for ($j = 0; $j < count($documentosPersonalesSel); $j++) {
            if (strpos($documentosPersonalesSel[$j], $nombresDocumentosPersonalesEliminados[$i] . ":") !== false) {
              $documentosPersonales = str_replace($documentosPersonalesSel[$j] . ",", "", $documentosPersonales);
              break;
            }
          }
        } catch (\Exception $e) {
          Log::error($e);
        }
      }
    }
    if (!is_null($datos["nombresDocumentosPersonales"]) && !is_null($datos["nombresOriginalesDocumentosPersonales"])) {
      $nombresDocumentosPersonales = explode(",", $datos["nombresDocumentosPersonales"]);
      $nombresOriginalesDocumentosPersonales = explode(",", $datos["nombresOriginalesDocumentosPersonales"]);
      for ($i = 0; $i < count($nombresDocumentosPersonales); $i++) {
        if (count(explode(",", $documentosPersonales)) == 4) {
          break;
        }
        if (trim($nombresDocumentosPersonales[$i]) == "") {
          continue;
        }
        $documentosPersonales .= $nombresDocumentosPersonales[$i] . ":" . (array_key_exists($i, $nombresOriginalesDocumentosPersonales) && $nombresOriginalesDocumentosPersonales[$i] != "" ? $nombresOriginalesDocumentosPersonales[$i] : $nombresDocumentosPersonales[$i]) . ",";
      }
    }
    return $documentosPersonales;
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
