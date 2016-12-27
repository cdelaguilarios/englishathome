<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curso extends Model {

  public $timestamps = false;
  protected $table = "curso";

  protected static function obtenerXId($id) {
    return Curso::where("eliminado", 0)->where("id", $id)->firstOrFail();
  }
  
  protected static function listarSimple() {
    return Curso::where("eliminado", 0)->lists("nombre", "id");
  }

}
