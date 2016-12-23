<?php

namespace App\Models;

use Carbon\Carbon;
use App\Helpers\Enum\TiposEntidad;
use App\Helpers\Enum\EstadosInteresado;
use Illuminate\Database\Eloquent\Model;

class Interesado extends Model {

  public $timestamps = false;
  protected $table = "interesado";
  protected $fillable = ["consulta", "cursoInteres"];

  public static function nombreTabla() {
    $modeloInteresado = new Interesado();
    $nombreTabla = $modeloInteresado->getTable();
    unset($modeloInteresado);
    return $nombreTabla;
  }

  protected static function listar($datos = NULL) {
    $nombreTabla = Interesado::nombreTabla();
    $interesados = Interesado::select($nombreTabla . ".*", "entidad.*")
            ->leftJoin(Entidad::nombreTabla() . " as entidad", $nombreTabla . ".idEntidad", "=", "entidad.id")
            ->where("entidad.eliminado", 0);
    
    if (isset($datos["estado"])) {
      $interesados->where("entidad.estado", $datos["estado"]);
    }
    return $interesados;
  }

  protected static function obtenerXId($id) {
    return Interesado::listar()->where("entidad.id", $id)->firstOrFail();
  }

  protected static function registrar($datos) {
    $entidad = new Entidad($datos);
    $entidad->tipo = TiposEntidad::Interesado;
    $entidad->estado = EstadosInteresado::PendienteInformacion;
    $entidad->save();

    $interesado = new Interesado($datos);
    $interesado->idEntidad = $entidad->id;
    $interesado->save();
    return $entidad->id;
  }

  protected static function actualizar($id, $datos) {
    $entidad = Entidad::ObtenerXId($id);
    $entidad->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
    $entidad->update($datos);

    $interesado = Interesado::obtenerXId($id);
    $interesado->update($datos);
  }

  protected static function eliminar($id) {
    Interesado::obtenerXId($id);
    Entidad::eliminar($id);
  }

}
