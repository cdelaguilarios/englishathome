<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class Curso extends Model {

  public $timestamps = false;
  protected $table = "curso";
  protected $fillable = ["nombre", "descripcion", "modulos", "metodologia", "incluye", "inversion", "inversionCuotas", "notasAdicionales", "activo"];

  public static function listar() {
    return Curso::where("eliminado", 0);
  }

  public static function listarSimple($soloActivos = TRUE) {
    $cursos = Curso::listar();
    if ($soloActivos) {
      $cursos->where("activo", 1);
    }
    return $cursos->lists("nombre", "id");
  }

  public static function obtenerXId($id) {
    return Curso::listar()->where("id", $id)->firstOrFail();
  }

  public static function registrar($req) {
    $datos = $req->all();
    $curso = new Curso($datos);
    $curso->fechaRegistro = Carbon::now()->toDateTimeString();
    $curso->save();
    $imagen = $req->file("imagen");
    if (isset($imagen) && $imagen != "") {
      $curso->imagen = Archivo::registrar($curso->id . "_ic_", $imagen);
      $curso->save();
    }
    Cache::forget("datosExtrasVistas");
    return $curso->id;
  }

  public static function actualizar($id, $req) {
    $datos = $req->all();
    $curso = Curso::obtenerXId($id);
    $curso->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
    $curso->update($datos);
    $imagen = $req->file("imagen");
    if (isset($imagen) && $imagen != "") {
      if (isset($curso->imagen) && $curso->imagen != "") {
        Archivo::eliminar($curso->imagen);
      }
      $curso->imagen = Archivo::registrar($curso->id . "_ic_", $imagen);
      $curso->save();
    }
    Cache::forget("datosExtrasVistas");
  }

  public static function eliminar($id) {
    $curso = Curso::obtenerXId($id);
    $curso->eliminado = 1;
    $curso->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
    $curso->save();
  }

}
