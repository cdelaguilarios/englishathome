<?php

namespace App\Models;

use DB;
use Log;
use Auth;
use Carbon\Carbon;
use App\Helpers\Enum\TiposEntidad;
use App\Helpers\Enum\EstadosProfesor;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Enum\EstadosPostulante;
use App\Helpers\Enum\MensajesNotificacion;
use App\Helpers\Enum\TiposRelacionEntidad;

class Postulante extends Model {

  public $timestamps = false;
  protected $primaryKey = "idEntidad";
  protected $table = "postulante";
  protected $fillable = ["ultimosTrabajos", "experienciaOtrosIdiomas", "descripcionPropia", "ensayo", "cv", "certificadoInternacional", "imagenDocumentoIdentidad", "audio"];

  public static function nombreTabla() {
    $modeloPostulante = new Postulante();
    $nombreTabla = $modeloPostulante->getTable();
    unset($modeloPostulante);
    return $nombreTabla;
  }

  public static function listar($datos = NULL) {
    $nombreTabla = Postulante::nombreTabla();
    $postulantes = Postulante::leftJoin(Entidad::nombreTabla() . " as entidad", $nombreTabla . ".idEntidad", "=", "entidad.id")
            ->leftJoin(EntidadCurso::nombreTabla() . " as entidadCurso", $nombreTabla . ".idEntidad", "=", "entidadCurso.idEntidad")
            ->leftJoin("distrito AS distritoPostulante", function ($q) {
              $q->on("distritoPostulante.codigo", "=", "entidad.codigoUbigeo");
            })
            ->where("entidad.eliminado", 0)
            ->groupBy("entidad.id")
            ->distinct();
    if (isset($datos["estado"])) {
      $postulantes->where("entidad.estado", $datos["estado"]);
    }
    return $postulantes->select(DB::raw(
                            $nombreTabla . '.*,
                            entidad.*,
                            distritoPostulante.distrito AS distrito,   
                            CONCAT(entidad.nombre, " ", entidad.apellido) AS nombreCompleto'
                    )
    );
  }

  public static function listarBusqueda($terminoBus = NULL) {
    $alumnos = Postulante::listar()->select("entidad.id", DB::raw('CONCAT(entidad.nombre, " ", entidad.apellido) AS nombreCompleto'));
    if (isset($terminoBus)) {
      $alumnos->whereRaw('CONCAT(entidad.nombre, " ", entidad.apellido) like ?', ["%{$terminoBus}%"]);
    }
    return $alumnos->lists("nombreCompleto", "entidad.id");
  }

  public static function obtenerXId($id, $simple = FALSE) {
    $postulante = Postulante::listar()->where("entidad.id", $id)->firstOrFail();

    if (!$simple) {
      $postulante->horario = Horario::obtenerJsonXIdEntidad($id);
      $postulante->direccionUbicacion = Ubigeo::obtenerTextoUbigeo($postulante->codigoUbigeo);
      $postulante->cursos = EntidadCurso::obtenerXIdEntidad($id, FALSE);

      $datosIdsAntSig = Entidad::ObtenerIdsAnteriorSiguienteXEntidad(TiposEntidad::Postulante, $postulante);
      $postulante->idPostulanteAnterior = $datosIdsAntSig["idEntidadAnterior"];
      $postulante->idPostulanteSiguiente = $datosIdsAntSig["idEntidadSiguiente"];
    }
    return $postulante;
  }

  public static function registrar($req) {
    $datos = $req->all();
    if (isset($datos["fechaNacimiento"])) {
      $datos["fechaNacimiento"] = Carbon::createFromFormat("d/m/Y H:i:s", $datos["fechaNacimiento"] . " 00:00:00")->toDateTimeString();
    }

    $idEntidad = Entidad::registrar($datos, TiposEntidad::Postulante, (Auth::guest() ? EstadosPostulante::RegistradoExterno : EstadosPostulante::Registrado));
    Entidad::registrarActualizarImagenPerfil($idEntidad, $req->file("imagenPerfil"));
    if (!(Auth::guest())) {
      EntidadCurso::registrarActualizar($idEntidad, $datos["idCursos"]);
    }
    Horario::registrarActualizar($idEntidad, $datos["horario"]);

    $datos["cv"] = Archivo::procesarArchivosSubidos("", $datos, 1, "DocumentoPersonalCv");
    $datos["certificadoInternacional"] = Archivo::procesarArchivosSubidos("", $datos, 1, "DocumentoPersonalCertificadoInternacional");
    $datos["imagenDocumentoIdentidad"] = Archivo::procesarArchivosSubidos("", $datos, 1, "DocumentoPersonalImagenDocumentoIdentidad");

    $postulante = new Postulante($datos);
    $postulante->idEntidad = $idEntidad;
    $postulante->save();

    Docente::registrarActualizarAudio($idEntidad, $req->file("audio"));
    Notificacion::registrarActualizar([
        "idEntidades" => [$idEntidad, (Auth::guest() ? NULL : Auth::user()->idEntidad)],
        "titulo" => (Auth::guest() ? MensajesNotificacion::TituloPostulanteRegistro : MensajesNotificacion::TituloPostulanteRegistroXUsuario),
        "enviarCorreo" => (Auth::guest() ? 1 : 0),
        "mostrarEnPerfil" => 1
    ]);
    return $idEntidad;
  }

  public static function actualizar($id, $req) {
    $datos = $req->all();
    if (isset($datos["fechaNacimiento"])) {
      $datos["fechaNacimiento"] = Carbon::createFromFormat("d/m/Y H:i:s", $datos["fechaNacimiento"] . " 00:00:00")->toDateTimeString();
    }

    Entidad::actualizar($id, $datos, TiposEntidad::Postulante, $datos["estado"]);
    Entidad::registrarActualizarImagenPerfil($id, $req->file("imagenPerfil"));
    EntidadCurso::registrarActualizar($id, $datos["idCursos"]);
    Horario::registrarActualizar($id, $datos["horario"]);
    Docente::registrarActualizarAudio($id, $req->file("audio"));
    unset($datos["audio"]);

    $postulante = Postulante::obtenerXId($id, TRUE);
    $datos["cv"] = Archivo::procesarArchivosSubidos($postulante->cv, $datos, 1, "DocumentoPersonalCv");
    $datos["certificadoInternacional"] = Archivo::procesarArchivosSubidos($postulante->certificadoInternacional, $datos, 1, "DocumentoPersonalCertificadoInternacional");
    $datos["imagenDocumentoIdentidad"] = Archivo::procesarArchivosSubidos($postulante->imagenDocumentoIdentidad, $datos, 1, "DocumentoPersonalImagenDocumentoIdentidad");
    $postulante->update($datos);
  }

  public static function actualizarEstado($id, $estado) {
    Postulante::obtenerXId($id, TRUE);
    Entidad::actualizarEstado($id, $estado);
  }

  public static function actualizarHorario($id, $horario) {
    Postulante::obtenerXId($id, TRUE);
    Horario::registrarActualizar($id, $horario);
  }

  public static function obtenerIdProfesor($id) {
    Postulante::obtenerXId($id, TRUE);
    $relacionEntidad = RelacionEntidad::obtenerXIdEntidadB($id);
    return ((count($relacionEntidad) > 0) ? $relacionEntidad[0]->idEntidadA : 0);
  }

  public static function registrarProfesor($id, $idProfesor = NULL) {
    $datos = Postulante::obtenerXId($id, TRUE)->toArray();
    $idProfesorRel = Postulante::obtenerIdProfesor($id);
    if (!($datos["estado"] != EstadosPostulante::ProfesorRegistrado && $idProfesorRel == 0)) {
      return ($idProfesorRel != 0 ? $idProfesorRel : NULL);
    }
    if (is_null($idProfesor)) {
      $idEntidad = Entidad::registrar($datos, TiposEntidad::Profesor, EstadosProfesor::Registrado);
      $entidadCursos = EntidadCurso::obtenerXIdEntidad($id, FALSE);
      if (!is_null($entidadCursos)) {
        $idsCursos = [];
        foreach ($entidadCursos as $entidadCurso) {
          array_push($idsCursos, $entidadCurso->idCurso);
        }
        EntidadCurso::registrarActualizar($idEntidad, $idsCursos);
      }
      Horario::copiarHorario($id, $idEntidad);

      $profesor = new Profesor();
      $profesor->idEntidad = $idEntidad;
      $profesor->ultimosTrabajos = $datos["ultimosTrabajos"];
      $profesor->experienciaOtrosIdiomas = $datos["experienciaOtrosIdiomas"];
      $profesor->descripcionPropia = $datos["descripcionPropia"];
      $profesor->ensayo = $datos["ensayo"];
      $profesor->cv = $datos["cv"];
      $profesor->certificadoInternacional = $datos["certificadoInternacional"];
      $profesor->imagenDocumentoIdentidad = $datos["imagenDocumentoIdentidad"];
      $profesor->audio = $datos["audio"];
      $profesor->save();
      $idProfesor = $idEntidad;

      Notificacion::registrarActualizar([
          "idEntidades" => [$idEntidad, Auth::user()->idEntidad],
          "titulo" => MensajesNotificacion::TituloProfesorRegistroXUsuario,
          "mostrarEnPerfil" => 1
      ]);
    }
    RelacionEntidad::registrar($idProfesor, $id, TiposRelacionEntidad::ProfesorPostulante);
    Postulante::actualizarEstado($id, EstadosPostulante::ProfesorRegistrado);
    Notificacion::registrarActualizar([
        "idEntidades" => [$id, Auth::user()->idEntidad],
        "titulo" => MensajesNotificacion::TituloPostulanteRegistroProfesorXUsuario,
        "mostrarEnPerfil" => 1
    ]);
    return $idProfesor;
  }

  public static function eliminar($id) {
    Postulante::obtenerXId($id, TRUE);
    Entidad::eliminar($id);
  }

  public static function verificarExistencia($id) {
    try {
      Postulante::obtenerXId($id, TRUE);
    } catch (\Exception $e) {
      Log::error($e);
      return FALSE;
    }
    return TRUE;
  }

  public static function verificarExistenciaXCorreoElectronico($correoElectronico) {
    try {
      Postulante::listar()->where("entidad.correoElectronico", $correoElectronico)->firstOrFail();
    } catch (\Exception $ex) {
      return FALSE;
    }
    return TRUE;
  }

}
