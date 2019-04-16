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

  public static function listarAlumnosVigentes($id) {
    $idsAlumnos = Clase::listarXProfesor($id)
                    ->whereIn(Clase::nombreTabla() . ".estado", [EstadosClase::Programada, EstadosClase::PendienteConfirmar])
                    ->lists(Clase::nombreTabla() . ".idAlumno")->toArray();
    if (count($idsAlumnos) > 0)
      return Alumno::listar()
                      ->whereIn("entidad.id", array_unique($idsAlumnos))
                      ->get();
    return null;
  }

  public static function obtenerXId($id, $simple = FALSE) {
    $nombreTabla = Profesor::nombreTabla();
    $profesor = Profesor::select($nombreTabla . ".*", "entidad.*")
                    ->leftJoin(Entidad::nombreTabla() . " as entidad", $nombreTabla . ".idEntidad", "=", "entidad.id")
                    ->where("entidad.id", $id)
                    ->where("entidad.eliminado", 0)->firstOrFail();
    if (!$simple) {
      $profesor->horario = Horario::obtenerFormatoJson($id);
      $profesor->direccionUbicacion = Ubigeo::obtenerTextoUbigeo($profesor->codigoUbigeo);
      $profesor->cursos = EntidadCurso::obtenerXEntidad($id, FALSE);
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
