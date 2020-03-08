<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntidadCorreo extends Model/* - */ {

  public $timestamps = false;
  protected $table = "entidadCorreo";
  protected $fillable = [
      "idEntidad",
      "idCorreo"
  ];

  public static function nombreTabla()/* - */ {
    $modeloEntidadCorreo = new EntidadCorreo();
    $nombreTabla = $modeloEntidadCorreo->getTable();
    unset($modeloEntidadCorreo);
    return $nombreTabla;
  }

}
