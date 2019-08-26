<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NivelIngles extends Model {

  public $timestamps = false;
  protected $table = "nivelIngles";

  public static function nombreTabla()/* - */ {
    $modeloNivelIngles = new NivelIngles();
    $nombreTabla = $modeloNivelIngles->getTable();
    unset($modeloNivelIngles);
    return $nombreTabla;
  }

  public static function listarSimple() {
    return NivelIngles::where("eliminado", 0)->lists("nombre", "id");
  }

}
