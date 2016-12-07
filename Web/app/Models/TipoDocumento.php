<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model {

    public $timestamps = false;
    protected $table = 'tipoDocumento';

    protected static function listarSimple() {
        return TipoDocumento::where('eliminado', 0)->lists('nombre', 'id');
    }

}
