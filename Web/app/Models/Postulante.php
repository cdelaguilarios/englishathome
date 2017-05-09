<?php

namespace App\Models;

use DB;
use Auth;
use Carbon\Carbon;
use App\Helpers\Enum\TiposEntidad;
use App\Helpers\Enum\EstadosProfesor;
use App\Helpers\Enum\MensajesHistorial;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Enum\EstadosPostulante;
use App\Helpers\Enum\TiposRelacionEntidad;

class Postulante extends Model {

  public $timestamps = false;
  protected $primaryKey = "idEntidad";
  protected $table = "postulante";
  protected $fillable = ["ultimosTrabajos", "experienciaOtrosIdiomas", "descripcionPropia", "ensayo"];

  public static function nombreTabla() {
    $modeloPostulante = new Postulante();
    $nombreTabla = $modeloPostulante->getTable();
    unset($modeloPostulante);
    return $nombreTabla;
  }

  public static function listar($datos = NULL) {
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

  public static function listarBusqueda() {
    return Postulante::listar()->lists("nombreCompleto", "entidad.id");
  }

  public static function obtenerXId($id, $simple = FALSE) {
    $postulante = Postulante::listar()->where("entidad.id", $id)->firstOrFail();
    if (!$simple) {
      $postulante->horario = Horario::obtenerFormatoJson($id);
      $postulante->direccionUbicacion = Ubigeo::obtenerTextoUbigeo($postulante->codigoUbigeo);
      $postulante->cursos = EntidadCurso::obtenerXEntidad($id, FALSE);
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

    $postulante = new Postulante($datos);
    $postulante->idEntidad = $idEntidad;
    $postulante->save();

    Historial::registrar([
        "idEntidades" => [$idEntidad, (Auth::guest() ? NULL : Auth::user()->idEntidad)],
        "titulo" => (Auth::guest() ? MensajesHistorial::TituloPostulanteRegistro : MensajesHistorial::TituloPostulanteRegistroXUsuario),
        "enviarCorreo" => (Auth::guest() ? 1 : 0),
        "mensaje" => ""
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
    $postulante = Postulante::obtenerXId($id, TRUE);
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
      $entidadCursos = EntidadCurso::obtenerXEntidad($id, FALSE);
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
      $profesor->save();
      $idProfesor = $idEntidad;

      Historial::registrar([
          "idEntidades" => [$idEntidad, Auth::user()->idEntidad],
          "titulo" => MensajesHistorial::TituloProfesorRegistroXUsuario,
          "mensaje" => ""
      ]);
    }
    RelacionEntidad::registrar($idProfesor, $id, TiposRelacionEntidad::ProfesorPostulante);
    Postulante::actualizarEstado($id, EstadosPostulante::ProfesorRegistrado);
    Historial::registrar([
        "idEntidades" => [$id, Auth::user()->idEntidad],
        "titulo" => MensajesHistorial::TituloPostulanteRegistroProfesorXUsuario,
        "mensaje" => ""
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
    } catch (\Exception $ex) {
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
