<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RelacionEntidad extends Model {

  public $timestamps = false;
  protected $table = "relacionEntidad";
  protected $fillable = ["idEntidadA", "idEntidadB", "tipo"];

  public static function nombreTabla() {
    $modeloRelacionEntidad = new RelacionEntidad();
    $nombreTabla = $modeloRelacionEntidad->getTable();
    unset($modeloRelacionEntidad);
    return $nombreTabla;
  }

  public static function registrar($idEntidadA, $idEntidadB, $tipo) {
    $relacionEntidad = new RelacionEntidad([
        "idEntidadA" => $idEntidadA,
        "idEntidadB" => $idEntidadB,
        "tipo" => $tipo
    ]);
    $relacionEntidad->save();
  }

  public static function obtenerXIdEntidadA($idInteresado) {
    return RelacionEntidad::where("idEntidadA", $idInteresado)->get();
  }
  
  public static function obtenerXIdEntidadB($idInteresado) {
    return RelacionEntidad::where("idEntidadB", $idInteresado)->get();
  }

}
