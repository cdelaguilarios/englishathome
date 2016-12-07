<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoProfesor extends Model {

    public $timestamps = false;
    protected $table = 'pagoProfesor';
    protected $fillable = ['idProfesor', 'idPago'];

    public static function NombreTabla() {
        $modeloPagoProfesor= new PagoProfesor();
        $nombreTabla = $modeloPagoProfesor->getTable();
        unset($modeloPagoProfesor);
        return $nombreTabla;
    }

}
