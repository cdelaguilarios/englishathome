<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NivelIngles extends Model {

    public $timestamps = false;
    protected $table = 'nivelingles';

    protected static function listarSimple() {
        return NivelIngles::where('eliminado', 0)->lists('nombre', 'id');
    }

}
