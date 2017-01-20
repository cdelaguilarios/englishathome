<?php

namespace App\Models;

use DB;
use Auth;
use Carbon\Carbon;
use App\Helpers\Enum\TiposEntidad;
use App\Helpers\Enum\MensajesHistorial;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Enum\EstadosPostulante;

class Postulante extends Model {

  public $timestamps = false;
  protected $table = "postulante";
  protected $fillable = [];

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
                    ->leftJoin(EntidadCurso::NombreTabla() . " as entidadCurso", $nombreTabla . ".idEntidad", "=", "entidadCurso.idEntidad")
                    ->where("entidad.eliminado", 0)->distinct();
    
    if (isset($datos["estado"])) {
      $postulantes->where("entidad.estado", $datos["estado"]);
    }
    return $postulantes;
  }
  
  public static function ObtenerXId($id, $simple = FALSE) {
    $nombreTabla = Postulante::nombreTabla();
    $postulante = Postulante::select($nombreTabla . ".*", "entidad.*")
                    ->leftJoin(Entidad::nombreTabla() . " as entidad", $nombreTabla . ".idEntidad", "=", "entidad.id")
                    ->where("entidad.id", $id)
                    ->where("entidad.eliminado", 0)->firstOrFail();
    if (!$simple) {
      $postulante->horario = Horario::obtenerFormatoJson($id);
      $postulante->direccionUbicacion = Ubigeo::obtenerTextoUbigeo($postulante->codigoUbigeo);
      $postulante->cursos = EntidadCurso::obtenerXEntidad($id, FALSE);
    }
    return $postulante;
  }
  
  public static function registrar($req) {
    $datos = $req->all();
    $datos["fechaNacimiento"] = Carbon::createFromFormat("d/m/Y H:i:s", $datos["fechaNacimiento"] . " 00:00:00")->toDateTimeString();

    $idEntidad = Entidad::registrar($datos, TiposEntidad::Postulante, EstadosPostulante::Registrado);
    Entidad::registrarActualizarImagenPerfil($idEntidad, $req->file("imagenPerfil"));
    EntidadCurso::registrarActualizar($idEntidad, $datos["idCursos"]);
    Horario::registrarActualizar($idEntidad, $datos["horario"]);

    $postulante = new Postulante();
    $postulante->idEntidad = $idEntidad;
    $postulante->save();

    Historial::Registrar([$idEntidad, Auth::user()->idEntidad], MensajesHistorial::TituloPostulanteRegistroXUsuario, "");
    return $idEntidad;
  }

  public static function actualizar($id, $req) {
    $datos = $req->all();
    $datos["fechaNacimiento"] = Carbon::createFromFormat("d/m/Y H:i:s", $datos["fechaNacimiento"] . " 00:00:00")->toDateTimeString();

    Entidad::Actualizar($id, $datos, TiposEntidad::Postulante, $datos["estado"]);
    Entidad::registrarActualizarImagenPerfil($id, $req->file("imagenPerfil"));
    EntidadCurso::registrarActualizar($id, $datos["idCursos"]);
    Horario::registrarActualizar($id, $datos["horario"]);

    Postulante::obtenerXId($id, TRUE);
  }

  public static function actualizarEstado($id, $estado) {
    Postulante::obtenerXId($id, TRUE);
    Entidad::actualizarEstado($id, $estado);
  }

  public static function eliminar($id) {
    Postulante::obtenerXId($id, TRUE);
    Entidad::eliminar($id);
  }

}
