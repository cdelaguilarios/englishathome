<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class Postulante extends Model {

    public $timestamps = false;
    protected $table = 'postulante';
    protected $fillable = [];

    public static function NombreTabla() {
        $modeloPostulante = new Postulante();
        $nombreTabla = $modeloPostulante->getTable();
        unset($modeloPostulante);
        return $nombreTabla;
    }

    protected static function Listar() {
        $nombreTabla = Postulante::NombreTabla();
        return Postulante::select($nombreTabla . '.*', 'entidad.*', DB::raw('CONCAT(entidad.nombre, " ", entidad.apellido) AS nombreCompleto'))
                        ->leftJoin(Entidad::nombreTabla() . ' as entidad', $nombreTabla . '.idEntidad', '=', 'entidad.id')
                        ->leftJoin(EntidadCurso::NombreTabla() . ' as entidadCurso', $nombreTabla . '.idEntidad', '=', 'entidadCurso.idEntidad')
                        ->where('entidad.eliminado', 0);
    }

    protected static function ListarSimple() {
        return Postulante::Listar()->lists('nombreCompleto', 'entidad.id');
    }

    protected static function ObtenerXId($id) {
        $nombreTabla = Postulante::NombreTabla();
        return Postulante::select($nombreTabla . '.*', 'entidad.*')
                        ->leftJoin(Entidad::nombreTabla() . ' as entidad', $nombreTabla . '.idEntidad', '=', 'entidad.id')
                        ->where('entidad.id', $id)
                        ->where('entidad.eliminado', 0)->firstOrFail();
    }

}
