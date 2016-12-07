<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class Ubigeo extends Model {

    protected static function listarDepartamentos() {
        return DB::table('departamento')->orderBy('departamento')->lists('departamento', 'codigo');
    }

    protected static function listarProvinciasXCodigoDepartamento($codigoDepartamento) {
        return DB::table('provincia')->where('codigoDepartamento', $codigoDepartamento)->orderBy('provincia')->lists('provincia', 'codigo');
    }

    protected static function listarDistritosXCodigoProvincia($codigoProvincia) {
        return DB::table('distrito')->where('codigoProvincia', $codigoProvincia)->orderBy('distrito')->lists('distrito', 'codigo');
    }

    protected static function obtenerTextoUbigeo($codigoUbigeo) {
        $texto = "";
        if (strlen($codigoUbigeo) == 6) {
            $codigoDepartamento = substr($codigoUbigeo, 0, 2);
            $codigoProvincia = substr($codigoUbigeo, 0, 4);
            $codigoDistrito = substr($codigoUbigeo, 0, 6);

            try {
                $distritoSel = DB::table('distrito')->where('codigo', $codigoDistrito)->first();
                $provinciaSel = DB::table('provincia')->where('codigo', $codigoProvincia)->first();
                $departamentoSel = DB::table('departamento')->where('codigo', $codigoDepartamento)->first();

                $texto = ucwords(strtolower($distritoSel->distrito . ", " . $provinciaSel->provincia . ", " . str_replace("DEPARTAMENTO ", "", $departamentoSel->departamento)));
            } catch (ErrorException $e) {
                
            }
        }
        return $texto;
    }

}
