<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntidadHistorial extends Model {

  public $timestamps = false;
  protected $table = "entidadHistorial";
  protected $fillable = ["idEntidad", "idHistorial"];

  public static function nombreTabla() {
    $modeloEntidadHistorial = new EntidadHistorial();
    $nombreTabla = $modeloEntidadHistorial->getTable();
    unset($modeloEntidadHistorial);
    return $nombreTabla;
  }

}
