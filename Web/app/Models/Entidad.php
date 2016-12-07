<?php

namespace App\Models;

use Carbon\Carbon;
use App\Helpers\Util;
use Illuminate\Database\Eloquent\Model;

class Entidad extends Model {

    public $timestamps = false;
    protected $table = "entidad";
    protected $fillable = ["nombre", "apellido", "fechaNacimiento", "genero", "telefono", "idTipoDocumento", "numeroDocumento", "correoElectronico", "rutaImagenPerfil", "direccion", "referenciaDireccion", "codigoUbigeo", "geoLatitud", "geoLongitud"];

    public static function nombreTabla() {
        $modeloEntidad = new Entidad();
        $nombreTabla = $modeloEntidad->getTable();
        unset($modeloEntidad);
        return $nombreTabla;
    }

    public static function ObtenerXId($id) {
        return Entidad::findOrFail($id);
    }

    public static function registrar($datos, $tipo, $estado) {
        $entidad = new Entidad($datos);
        $entidad->tipo = $tipo;
        $entidad->estado = $estado;
        $entidad->save();
        return $entidad["id"];
    }

    public static function Actualizar($id, $datos, $tipo, $estado) {
        $entidad = Entidad::ObtenerXId($id);
        $entidad->tipo = $tipo;
        $entidad->estado = $estado;
        $entidad->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
        $entidad->update($datos);
    }

    public static function registrarActualizarImagenPerfil($id, $imagenPerfil) {
        if (isset($imagenPerfil) && !is_null($imagenPerfil) && $imagenPerfil != "") {
            $entidad = Entidad::ObtenerXId($id);
            $entidad->rutaImagenPerfil = Util::GuardarImagen($entidad["id"] . "_ia_", $imagenPerfil);
            $entidad->save();
        }
    }

    public static function eliminar($id) {
        $entidad = Entidad::ObtenerXId($id);
        $entidad->eliminado = 1;
        $entidad->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
        $entidad->save();
    }

}
