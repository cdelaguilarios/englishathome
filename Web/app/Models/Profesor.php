<?php

namespace App\Models;

use DB;
use Auth;
use Carbon\Carbon;
use App\Helpers\Enum\EstadosClase;
use App\Helpers\Enum\TiposEntidad;
use App\Helpers\Enum\EstadosProfesor;
use App\Helpers\Enum\MensajesHistorial;
use Illuminate\Database\Eloquent\Model;

class Profesor extends Model {

  public $timestamps = false;
  protected $primaryKey = "idEntidad";
  protected $table = "profesor";
  protected $fillable = ["ultimosTrabajos", "experienciaOtrosIdiomas", "descripcionPropia", "ensayo", "cv", "certificadoInternacional", "imagenDocumentoIdentidad", "audio", "comentarioPerfil"];

  public static function nombreTabla() {
    $modeloProfesor = new Profesor();
    $nombreTabla = $modeloProfesor->getTable();
    unset($modeloProfesor);
    return $nombreTabla;
  }

  public static function listar($datos = NULL) {
    $nombreTabla = Profesor::nombreTabla();
    $profesores = Profesor::select($nombreTabla . ".*", "entidad.*", DB::raw('CONCAT(entidad.nombre, " ", entidad.apellido) AS nombreCompleto'))
                    ->leftJoin(Entidad::nombreTabla() . " as entidad", $nombreTabla . ".idEntidad", "=", "entidad.id")
                    ->leftJoin(EntidadCurso::nombreTabla() . " as entidadCurso", $nombreTabla . ".idEntidad", "=", "entidadCurso.idEntidad")
                    ->where("entidad.eliminado", 0)->groupBy("entidad.id")->distinct();
    if (isset($datos["estado"])) {
      $profesores->where("entidad.estado", $datos["estado"]);
    }
    return $profesores;
  }

  public static function listarBusqueda($terminoBus = NULL) {
    $profesores = Profesor::listar()->select("entidad.id", DB::raw('CONCAT(entidad.nombre, " ", entidad.apellido) AS nombreCompleto'));
    if (isset($terminoBus)) {
      $profesores->whereRaw('CONCAT(entidad.nombre, " ", entidad.apellido) like ?', ["%{$terminoBus}%"]);
    }
    return $profesores->lists("nombreCompleto", "entidad.id");
  }

  public static function obtenerXId($id, $simple = FALSE) {
    $nombreTabla = Profesor::nombreTabla();
    $profesor = Profesor::select($nombreTabla . ".*", "entidad.*")
                    ->leftJoin(Entidad::nombreTabla() . " as entidad", $nombreTabla . ".idEntidad", "=", "entidad.id")
                    ->where("entidad.id", $id)
                    ->where("entidad.eliminado", 0)->firstOrFail();
    if (!$simple) {
      $profesor->horario = Horario::obtenerJsonXIdEntidad($id);
      $profesor->direccionUbicacion = Ubigeo::obtenerTextoUbigeo($profesor->codigoUbigeo);
      $profesor->cursos = EntidadCurso::obtenerXIdEntidad($id, FALSE);
      $idProfesorAnterior = Profesor::listar()->select("entidad.id")->where("entidad.id", "<", $id)->where("entidad.estado", $profesor->estado)->orderBy("entidad.id", "DESC")->first();
      $idProfesorSiguiente = Profesor::listar()->select("entidad.id")->where("entidad.id", ">", $id)->where("entidad.estado", $profesor->estado)->first();
      $profesor->idProfesorAnterior = (isset($idProfesorAnterior) ? $idProfesorAnterior->id : NULL);
      $profesor->idProfesorSiguiente = (isset($idProfesorSiguiente) ? $idProfesorSiguiente->id : NULL);
    }
    return $profesor;
  }

  public static function registrar($req) {
    $datos = $req->all();
    if (isset($datos["fechaNacimiento"])) {
      $datos["fechaNacimiento"] = Carbon::createFromFormat("d/m/Y H:i:s", $datos["fechaNacimiento"] . " 00:00:00")->toDateTimeString();
    }

    $idEntidad = Entidad::registrar($datos, TiposEntidad::Profesor, EstadosProfesor::Registrado);
    Entidad::registrarActualizarImagenPerfil($idEntidad, $req->file("imagenPerfil"));
    EntidadCurso::registrarActualizar($idEntidad, $datos["idCursos"]);
    Horario::registrarActualizar($idEntidad, $datos["horario"]);
    $datos["cv"] = Archivo::procesarArchivosSubidos("", $datos, 1, "nombreDocumentoCv", "nombreOriginalDocumentoCv", "nombreDocumentoCvEliminado");
    $datos["certificadoInternacional"] = Archivo::procesarArchivosSubidos("", $datos, 1, "nombreDocumentoCertificadoInternacional", "nombreOriginalDocumentoCertificadoInternacional", "nombreDocumentoCertificadoInternacionalEliminado");
    $datos["imagenDocumentoIdentidad"] = Archivo::procesarArchivosSubidos("", $datos, 1, "nombreImagenDocumentoIdentidad", "nombreOriginalImagenDocumentoIdentidad", "nombreImagenDocumentoIdentidadEliminado");

    $profesor = new Profesor($datos);
    $profesor->idEntidad = $idEntidad;
    $profesor->save();

    Docente::registrarActualizarAudio($idEntidad, $req->file("audio"));
    Historial::registrar([
        "idEntidades" => [$idEntidad, Auth::user()->idEntidad],
        "titulo" => MensajesHistorial::TituloProfesorRegistroXUsuario,
        "mensaje" => ""
    ]);
    return $idEntidad;
  }

  public static function actualizar($id, $req) {
    $datos = $req->all();
    if (isset($datos["fechaNacimiento"])) {
      $datos["fechaNacimiento"] = Carbon::createFromFormat("d/m/Y H:i:s", $datos["fechaNacimiento"] . " 00:00:00")->toDateTimeString();
    }

    Entidad::actualizar($id, $datos, TiposEntidad::Profesor, $datos["estado"]);
    Entidad::registrarActualizarImagenPerfil($id, $req->file("imagenPerfil"));
    EntidadCurso::registrarActualizar($id, $datos["idCursos"]);
    Horario::registrarActualizar($id, $datos["horario"]);
    Docente::registrarActualizarAudio($id, $req->file("audio"));
    unset($datos["audio"]);

    $profesor = Profesor::obtenerXId($id, TRUE);
    $datos["cv"] = Archivo::procesarArchivosSubidos($profesor->cv, $datos, 1, "nombreDocumentoCv", "nombreOriginalDocumentoCv", "nombreDocumentoCvEliminado");
    $datos["certificadoInternacional"] = Archivo::procesarArchivosSubidos($profesor->certificadoInternacional, $datos, 1, "nombreDocumentoCertificadoInternacional", "nombreOriginalDocumentoCertificadoInternacional", "nombreDocumentoCertificadoInternacionalEliminado");
    $datos["imagenDocumentoIdentidad"] = Archivo::procesarArchivosSubidos($profesor->imagenDocumentoIdentidad, $datos, 1, "nombreImagenDocumentoIdentidad", "nombreOriginalImagenDocumentoIdentidad", "nombreImagenDocumentoIdentidadEliminado");
    $profesor->update($datos);

    if (Usuario::verificarExistencia($id)) {
      $usuario = Usuario::obtenerXId($id);
      $usuario->email = $datos["correoElectronico"];
      $usuario->update();
    }
  }

  public static function actualizarEstado($id, $estado) {
    Profesor::obtenerXId($id, TRUE);
    Entidad::actualizarEstado($id, $estado);
  }

  public static function actualizarHorario($id, $horario) {
    Profesor::obtenerXId($id, TRUE);
    Horario::registrarActualizar($id, $horario);
  }

  public static function actualizarComentariosPerfil($id, $datos) {
    $profesor = Profesor::ObtenerXId($id, TRUE);
    $profesor->comentarioPerfil = $datos["comentarioPerfil"];
    $profesor->save();
  }

  public static function eliminar($id) {
    Profesor::obtenerXId($id, TRUE);
    Entidad::eliminar($id);
  }

  public static function verificarExistencia($id) {
    try {
      Profesor::obtenerXId($id, TRUE);
    } catch (\Exception $ex) {
      return FALSE;
    }
    return TRUE;
  }

  public static function listarClasesBase($id, $idAlumno = NULL, $soloVigentes = FALSE, $soloConfirmadasORealizadas = FALSE) {
    $nombreTabla = Clase::nombreTabla();
    $clases = Clase::listarBase()->where($nombreTabla . ".idProfesor", $id);
    if (!is_null($idAlumno))
      $clases->where($nombreTabla . ".idAlumno", $idAlumno);
    if($soloVigentes)
      $clases->whereIn($nombreTabla . ".estado", [EstadosClase::Programada, EstadosClase::PendienteConfirmar]);
    else if ($soloConfirmadasORealizadas)
      $clases->whereIn($nombreTabla . ".estado", [EstadosClase::ConfirmadaProfesorAlumno, EstadosClase::Realizada]);
    return $clases;
  }

  public static function listarAlumnos($id, $soloVigentes = TRUE) {
    $idsAlumnos = Profesor::listarClasesBase($id, NULL, $soloVigentes)->lists(Clase::nombreTabla() . ".idAlumno")->toArray();
    return (count($idsAlumnos) > 0 ? Alumno::listar()->whereIn("entidad.id", array_unique($idsAlumnos))->get() : null);
  }

  public static function obtenerAlumno($id, $idAlumno, $soloVigente = TRUE) {
    Profesor::listarClasesBase($id, $idAlumno, $soloVigente)->firstOrFail();
    $preClases = Profesor::listarClasesBase($id, $idAlumno);
    $clases = $preClases->get();
    $alumno = Alumno::obtenerXId($idAlumno, TRUE);
    $alumno->horario = Horario::obtenerJsonXIdEntidad($idAlumno);
    $alumno->totalClases = $clases->count();
    $alumno->duracionTotalClases = $clases->sum("duracion");
    $alumno->duracionTotalClasesRealizadas = $preClases->whereIn(Clase::nombreTabla() . ".estado", [EstadosClase::ConfirmadaProfesorAlumno, EstadosClase::Realizada])->get()->sum("duracion");
    $alumno->porcentajeAvance = ($alumno->duracionTotalClasesRealizadas * 100/$alumno->duracionTotalClases);
    return $alumno;
  }

  public static function listarClasesAlumno($id, $idAlumno, $soloVigentes = FALSE, $soloConfirmadasORealizadas = TRUE) {
    $nombreTabla = Clase::nombreTabla();
    return Profesor::listarClasesBase($id, $idAlumno, $soloVigentes, $soloConfirmadasORealizadas)
                    ->select($nombreTabla . ".id", $nombreTabla . ".numeroPeriodo", $nombreTabla . ".duracion", $nombreTabla . ".estado", $nombreTabla . ".fechaInicio", $nombreTabla . ".fechaFin", $nombreTabla . ".fechaConfirmacion", $nombreTabla . ".fechaCancelacion", $nombreTabla . ".comentarioProfesor", $nombreTabla . ".comentarioParaProfesor");
  }

  public static function registrarAvanceClase($id, $idAlumno, $datos) {
    Profesor::listarClasesAlumno($id, $idAlumno)->where(Clase::nombreTabla() . ".id", $datos["idClase"])->firstOrFail();

    $datos["tipo"] = 2;
    $datos["idAlumno"] = $idAlumno;
    Clase::actualizarComentarios($datos);
  }

  public static function obtenerProximaClase($id, $idAlumno) {
    return Profesor::listarClasesBase($id, $idAlumno, TRUE)
                    ->orderBy(Clase::nombreTabla() . ".fechaInicio", "ASC")
                    ->first();
  }

  public static function confirmarClase($id, $idAlumno, $datos) {
    $alumno = Profesor::obtenerAlumno($id, $idAlumno);
    $clase = Profesor::obtenerProximaClase($id, $idAlumno);
    $duracionAct = (int) $clase->duracion;
    $duracionSel = (int) $datos["duracion"];
    $duracionMax = (int) $alumno->duracionTotalClases - $alumno->duracionTotalClasesRealizadas;

    $fechaActual = Carbon::now()->toDateTimeString();
    $cambioDuracion = ($duracionAct != $duracionSel && $duracionSel <= $duracionMax);

    $clase->estado = EstadosClase::ConfirmadaProfesorAlumno;
    $clase->fechaConfirmacion = $fechaActual;
    $clase->fechaUltimaActualizacion = $fechaActual;
    if ($cambioDuracion)
      $clase->duracion = $duracionSel;
    $clase->save();

    Profesor::registrarAvanceClase($id, $idAlumno, $datos);

    //TODO: Considerar pagos sin clases
    //Actualizar clases en base a variación de la duración de clases
    $nombreTabla = Clase::nombreTabla();
    if ($cambioDuracion) {
      $variaciónDuracion = ($duracionSel - $duracionAct);
      if ($variaciónDuracion > 0) {
        //Se aumento las horas de la clase confirmada, se debe quitar el tiempo de más de la última o últimas clases
        $clasesRestantes = Profesor::listarClasesBase($id, $idAlumno, TRUE)->orderBy($nombreTabla . ".fechaInicio", "DESC")->select($nombreTabla . ".id", $nombreTabla . ".duracion")->get();
        foreach ($clasesRestantes as $claseRestante) {
          if ($claseRestante->duracion <= $variaciónDuracion) {
            $variaciónDuracion -= $claseRestante->duracion;
            Clase::eliminar($idAlumno, $claseRestante->id);
            if ($variaciónDuracion <= 0)
              break;
          } else {
            $claseResAct = Clase::ObtenerXId($idAlumno, $claseRestante->id);
            $claseResAct->duracion -= abs($variaciónDuracion);
            $claseResAct->fechaUltimaActualizacion = $fechaActual;
            $claseResAct->save();
            break;
          }
        }
      } else {
        //Se resto horas a la clase confirmada, se debe agregar el tiempo sobrante a la última clase
        $ultimaClase = Profesor::listarClasesBase($id, $idAlumno, TRUE)->orderBy(Clase::nombreTabla() . ".fechaInicio", "DESC")->select($nombreTabla . ".id")->first();
        if (!is_null($ultimaClase)) {
          $claseResAct = Clase::ObtenerXId($idAlumno, $ultimaClase->id);
          $claseResAct->duracion += abs($variaciónDuracion);
          $claseResAct->fechaUltimaActualizacion = $fechaActual;
          $claseResAct->save();
        }
      }
    }
    //TODO: Reajustar clases en base a pago y clases confirmadas y realizadas

    Historial::registrar([
        "idEntidades" => [$id, $idAlumno],
        "titulo" => "[" . TiposEntidad::Profesor . "] confirmó una clase del alumno(a) [" . TiposEntidad::Alumno . "]",
        "mensaje" => ""
    ]);
  }

  //REPORTE
  public static function listarCampos() {
    return [
        "ultimosTrabajos" => ["titulo" => "Últimos trabajos"],
        "experienciaOtrosIdiomas" => ["titulo" => "Experencia en otros idiomas"],
        "descripcionPropia" => ["titulo" => "Descripción propia"],
        "ensayo" => ["titulo" => "Ensayo"],
        "cv" => ["titulo" => "CV"],
        "certificadoInternacional" => ["titulo" => "Certificado internacional"],
        "imagenDocumentoIdentidad" => ["titulo" => "Imagen documento de identidad"],
        "audio" => ["titulo" => "Audio"]
    ];
  }

}
