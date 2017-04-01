<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class Ubigeo extends Model {

  public static function listarDepartamentos() {
    return DB::table("departamento")->orderBy("departamento")->lists("departamento", "codigo");
  }

  public static function listarProvinciasXCodigoDepartamento($codigoDepartamento) {
    return DB::table("provincia")->where("codigoDepartamento", $codigoDepartamento)->orderBy("provincia")->lists("provincia", "codigo");
  }

  public static function listarDistritosXCodigoProvincia($codigoProvincia) {
    return DB::table("distrito")->where("codigoProvincia", $codigoProvincia)->orderBy("distrito")->lists("distrito", "codigo");
  }

  public static function obtenerTextoUbigeo($codigoUbigeo) {
    $texto = "";
    if (strlen($codigoUbigeo) == 6) {
      $codigoDepartamento = substr($codigoUbigeo, 0, 2);
      $codigoProvincia = substr($codigoUbigeo, 0, 4);
      $codigoDistrito = substr($codigoUbigeo, 0, 6);

      try {
        $distritoSel = DB::table("distrito")->where("codigo", $codigoDistrito)->get();
        $provinciaSel = DB::table("provincia")->where("codigo", $codigoProvincia)->get();
        $departamentoSel = DB::table("departamento")->where("codigo", $codigoDepartamento)->get();
        $texto = ucwords(strtolower($distritoSel[0]->distrito . ", " . $provinciaSel[0]->provincia . ", " . str_replace("DEPARTAMENTO ", "", $departamentoSel[0]->departamento)));
      } catch (\Exception $e) {
        
      }
    }
    return $texto;
  }

}
