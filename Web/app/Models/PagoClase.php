<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoClase extends Model {

    public $timestamps = false;
    protected $table = 'pagoClase';
    protected $fillable = ['idPago', 'idClase'];

    public static function NombreTabla() {
        $modeloPagoClase = new PagoClase();
        $nombreTabla = $modeloPagoClase->getTable();
        unset($modeloPagoClase);
        return $nombreTabla;
    }
    
    public static function registrar($idPago, $idClase) {        
        $pagoClase = new PagoClase(["idPago" => $idPago, "idClase" => $idClase]);
        $pagoClase->save();
    }

}
