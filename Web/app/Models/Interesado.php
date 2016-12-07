<?php

namespace App\Models;

use Carbon\Carbon;
use App\Helpers\Enum\TiposEntidad;
use App\Helpers\Enum\EstadosInteresado;
use Illuminate\Database\Eloquent\Model;

class Interesado extends Model {

    public $timestamps = false;
    protected $table = 'interesado';
    protected $fillable = ['consulta', 'cursoInteres'];

    public static function NombreTabla() {
        $modeloInteresado = new Interesado();
        $nombreTabla = $modeloInteresado->getTable();
        unset($modeloInteresado);
        return $nombreTabla;
    }

    protected static function Listar() {
        $nombreTabla = Interesado::NombreTabla();
        return Interesado::select($nombreTabla . '.*', 'entidad.*')
                        ->leftJoin(Entidad::nombreTabla() . ' as entidad', $nombreTabla . '.idEntidad', '=', 'entidad.id')
                        ->where('entidad.eliminado', 0);
    }

    protected static function ObtenerXId($id) {
        $nombreTabla = Interesado::NombreTabla();
        return Interesado::select($nombreTabla . '.*', 'entidad.*')
                        ->leftJoin(Entidad::nombreTabla() . ' as entidad', $nombreTabla . '.idEntidad', '=', 'entidad.id')
                        ->where('entidad.id', $id)
                        ->where('entidad.eliminado', 0)->firstOrFail();
    }

    protected static function Registrar($req) {
        $datos = $req->all();
        $datos["estado"] = EstadosInteresado::Pendiente;

        $entidad = new Entidad($datos);
        $entidad->tipo = TiposEntidad::Interesado;
        $entidad->save();

        $interesado = new Interesado($datos);
        $interesado->idEntidad = $entidad['id'];
        $interesado->save();
        return $entidad["id"];
    }

    protected static function Actualizar($id, $req) {
        $datos = $req->all();

        $interesado = Interesado::ObtenerXId($id);
        $entidad = Entidad::ObtenerXId($id);

        $entidad->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
        $entidad->update($datos);
        $interesado->update($datos);
    }

    protected static function Eliminar($id) {
        $entidad = Entidad::ObtenerXId($id);
        $entidad->eliminado = 1;
        $entidad->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
        $entidad->save();
    }

}
