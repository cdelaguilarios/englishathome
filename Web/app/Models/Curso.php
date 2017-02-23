<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Curso extends Model {

  public $timestamps = false;
  protected $table = "curso";
  protected $fillable = ["nombre", "descripcion", "metodologia", "incluye", "inversion", "inversionCuotas", "notasAdicionales", "activo"];

  public static function listar() {
    return Curso::where("eliminado", 0);
  }

  public static function listarSimple() {
    return Curso::listar()->where("activo", 1)->lists("nombre", "id");
  }

  public static function obtenerXId($id) {
    return Curso::listar()->where("id", $id)->firstOrFail();
  }

  public static function registrar($datos) {
    $curso = new Curso($datos);
    $curso->save();
    return $curso->id;
  }

  public static function actualizar($id, $datos) {
    $curso = Curso::obtenerXId($id);
    $curso->update($datos);
  }

  public static function eliminar($id) {
    $curso = Curso::obtenerXId($id);
    $curso->eliminado = 1;
    $curso->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
    $curso->save();
  }

}
