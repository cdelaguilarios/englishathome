<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class Profesor extends Model {

    public $timestamps = false;
    protected $table = 'profesor';
    protected $fillable = [];

    public static function NombreTabla() {
        $modeloProfesor = new Profesor();
        $nombreTabla = $modeloProfesor->getTable();
        unset($modeloProfesor);
        return $nombreTabla;
    }

    protected static function Listar() {
        $nombreTabla = Profesor::NombreTabla();
        return Profesor::select($nombreTabla . '.*', 'entidad.*', DB::raw('CONCAT(entidad.nombre, " ", entidad.apellido) AS nombreCompleto'))
                        ->leftJoin(Entidad::nombreTabla() . ' as entidad', $nombreTabla . '.idEntidad', '=', 'entidad.id')
                        ->leftJoin(EntidadCurso::NombreTabla() . ' as entidadCurso', $nombreTabla . '.idEntidad', '=', 'entidadCurso.idEntidad')
                        ->where('entidad.eliminado', 0);
    }

    protected static function ListarSimple() {
        return Profesor::Listar()->lists('nombreCompleto', 'entidad.id');
    }

    protected static function ObtenerXId($id) {
        $nombreTabla = Profesor::NombreTabla();
        return Profesor::select($nombreTabla . '.*', 'entidad.*')
                        ->leftJoin(Entidad::nombreTabla() . ' as entidad', $nombreTabla . '.idEntidad', '=', 'entidad.id')
                        ->where('entidad.id', $id)
                        ->where('entidad.eliminado', 0)->firstOrFail();
    }

}
