<?php

namespace App\Models;

use DB;
use Auth;
use Carbon\Carbon;
use App\Helpers\Enum\TiposEntidad;
use App\Helpers\Enum\EstadosProfesor;
use App\Helpers\Enum\MensajesHistorial;
use Illuminate\Database\Eloquent\Model;

class Profesor extends Model {

  public $timestamps = false;
  protected $table = "profesor";
  protected $fillable = [];

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

  public static function listarBusqueda() {
    return Profesor::listar()->select("entidad.id", DB::raw('CONCAT(entidad.nombre, " ", entidad.apellido) AS nombreCompleto'))->lists("nombreCompleto", "entidad.id");
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

    $profesor = new Profesor();
    $profesor->idEntidad = $idEntidad;
    $profesor->save();

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
  }

  public static function actualizarEstado($id, $estado) {
    Profesor::obtenerXId($id, TRUE);
    Entidad::actualizarEstado($id, $estado);
  }

  public static function actualizarHorario($id, $horario) {
    Profesor::obtenerXId($id, TRUE);
    Horario::registrarActualizar($id, $horario);
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

}
