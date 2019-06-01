<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntidadNivelIngles extends Model {

  public $timestamps = false;
  protected $table = "entidadNivelIngles";
  protected $fillable = ["idEntidad", "idNivelIngles"];

  public static function nombreTabla()/* - */ {
    $modeloEntidadNivelIngles = new EntidadNivelIngles();
    $nombreTabla = $modeloEntidadNivelIngles->getTable();
    unset($modeloEntidadNivelIngles);
    return $nombreTabla;
  }

  public static function obtenerXIdEntidad($idEntidad)/* - */ {
    return EntidadNivelIngles::where("idEntidad", $idEntidad)->first();
  }

  public static function registrarActualizar($idEntidad, $idNivelIngles) {
    EntidadNivelIngles::where("idEntidad", $idEntidad)->delete();
    $entidadNivelIngles = new EntidadNivelIngles(["idEntidad" => $idEntidad, "idNivelIngles" => $idNivelIngles]);
    $entidadNivelIngles->save();
  }

}
