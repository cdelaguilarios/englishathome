<?php

namespace App\Models;

use Crypt;
use Illuminate\Database\Eloquent\Model;

class VariableSistema extends Model {

  public $timestamps = false;
  protected $table = "variableSistema";
  protected $fillable = ["llave", "valor", "titulo", "recomendacionesAdicionales", "tipo"];

  public static function listar() {
    return VariableSistema::get();
  }

  public static function obtenerXId($id) {
    return VariableSistema::where("id", $id)->firstOrFail();
  }
  
  public static function obtenerXLlave($llave) {
    $variable = VariableSistema::where("llave", $llave)->first();
    return (isset($variable) ? Crypt::decrypt($variable->valor) : "");
  }

  public static function actualizar($datos) {
    $variablesSistema = VariableSistema::listar();

    foreach ($variablesSistema as $variableSistema) {
      if ($datos[$variableSistema->llave] != NULL && $datos[$variableSistema->llave] != "") {
        $variableSistema->update(["valor" => Crypt::encrypt($datos[$variableSistema->llave])]);
      }
    }
  }

}
