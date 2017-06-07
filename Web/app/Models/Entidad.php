<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Entidad extends Model {

  public $timestamps = false;
  protected $table = "entidad";
  protected $fillable = ["nombre", "apellido", "fechaNacimiento", "sexo", "telefono", "idTipoDocumento", "numeroDocumento", "correoElectronico", "imagenPerfil", "direccion", "numeroDepartamento", "referenciaDireccion", "codigoUbigeo", "geoLatitud", "geoLongitud", "comentarioAdministrador"];

  public static function nombreTabla() {
    $modeloEntidad = new Entidad();
    $nombreTabla = $modeloEntidad->getTable();
    unset($modeloEntidad);
    return $nombreTabla;
  }

  public static function listar($tipo, $idsExcluir = []) {
    return Entidad::where("eliminado", 0)->whereNotIn("id", $idsExcluir)->where("tipo", $tipo)->get();
  }

  public static function ObtenerXId($id) {
    return Entidad::where("eliminado", 0)->where("id", $id)->firstOrFail();
  }

  public static function buscar($datos) {
    $texto = $datos["texto"];
    $pagina = $datos["pagina"];
    $entidadesXPorPagina = 6;
    $entidades = Entidad::where("eliminado", 0)->where(function ($q) use($texto) {
      $q->where("nombre", 'like', "%" . $texto . "%")
              ->orWhere("apellido", 'like', "%" . $texto . "%")
              ->orWhere("correoElectronico", 'like', "%" . $texto . "%");
    });
    $total = $entidades->count();
    return ["incomplete_results" => TRUE, "entidades" => $entidades->skip(($pagina - 1) * $entidadesXPorPagina)->take($entidadesXPorPagina)->get(), "total" => $total];
  }

  public static function registrar($datos, $tipo, $estado) {
    $entidad = new Entidad($datos);
    $entidad->tipo = $tipo;
    $entidad->estado = $estado;
    $entidad->save();
    return $entidad->id;
  }

  public static function actualizar($id, $datos, $tipo, $estado) {
    $entidad = Entidad::ObtenerXId($id);
    $entidad->tipo = $tipo;
    if (isset($estado)) {
      $entidad->estado = $estado;
    }
    $entidad->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
    $entidad->update($datos);
  }

  public static function actualizarEstado($id, $estado) {
    $entidad = Entidad::ObtenerXId($id);
    $entidad->estado = $estado;
    $entidad->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
    $entidad->save();
  }

  public static function registrarActualizarImagenPerfil($id, $imagenPerfil) {
    if (isset($imagenPerfil) && !is_null($imagenPerfil)) {
      $entidad = Entidad::ObtenerXId($id);
      if (isset($entidad->imagenPerfil) && $entidad->imagenPerfil != "") {
        Archivo::eliminar($entidad->imagenPerfil);
      }
      $entidad->imagenPerfil = Archivo::registrar($entidad->id . "_ip_", $imagenPerfil, TRUE);
      $entidad->save();
    }
  }

  public static function eliminar($id) {
    $entidad = Entidad::ObtenerXId($id);
    $entidad->eliminado = 1;
    $entidad->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
    $entidad->save();
  }

  public static function verificarExistencia($id) {
    try {
      Entidad::obtenerXId($id, TRUE);
    } catch (\Exception $ex) {
      return FALSE;
    }
    return TRUE;
  }

}
