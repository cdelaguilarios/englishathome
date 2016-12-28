<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RelacionEntidad extends Model {

  public $timestamps = false;
  protected $table = "relacionEntidad";
  protected $fillable = ["idEntidadA", "idEntidadB", "tipo"];

  public static function NombreTabla() {
    $modeloRelacionEntidad = new RelacionEntidad();
    $nombreTabla = $modeloRelacionEntidad->getTable();
    unset($modeloRelacionEntidad);
    return $nombreTabla;
  }

  protected static function registrar($idEntidadA, $idEntidadB, $tipo) {
    $relacionEntidad = new RelacionEntidad([
        "idEntidadA" => $idEntidadA,
        "idEntidadB" => $idEntidadB,
        "tipo" => $tipo
    ]);
    $relacionEntidad->save();
  }

}
