<?php

namespace App\Models;

use DB;
use App\Helpers\Enum\TiposEntidad;
use Illuminate\Database\Eloquent\Model;

class Reporte extends Model {

  public $timestamps = false;
  protected $table = "reporte";
  protected $fillable = ["titulo", "descripcion"];

  public static function nombreTabla() {
    $modeloReporte = new Reporte();
    $nombreTabla = $modeloReporte->getTable();
    unset($modeloReporte);
    return $nombreTabla;
  }

  public static function listar() {
    return Reporte::where("eliminado", 0);
  }

  public static function obtenerXId($id) {
    return Reporte::listar()->where("id", $id)->firstOrFail();
  }

  public static function registrar($entidad, $req) {
    $clase = "\App\Models\\" . $entidad;

    $datos = $req->all();
    $reporte = new Reporte($datos);
    $reporte->datos = Reporte::obtenerDatos($entidad, $req);
    $reporte->consulta = $clase::obtenerConsultaBdReporte();
    $reporte->fechaRegistro = Carbon::now()->toDateTimeString();
    $reporte->save();
    return $reporte->id;
  }

  public static function actualizar($id, $req) {
    $datos = $req->all();
    $reporte = Reporte::obtenerXId($id);
    $reporte->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
    $reporte->update($datos);
  }

  public static function eliminar($id) {
    $reporte = Reporte::obtenerXId($id);
    $reporte->eliminado = 1;
    $reporte->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
    $reporte->save();
  }

  public static function listarCampos($entidad) {
    $clase = "\App\Models\\" . $entidad;
    $nombreTabla = $clase::nombreTabla();
    $campos = $clase::listarCampos();
    $datosColumnas = DB::table('INFORMATION_SCHEMA.COLUMNS')
                    ->where('table_name', $nombreTabla)
                    ->whereIn('COLUMN_NAME', array_keys($campos))
                    ->select('COLUMN_NAME', 'DATA_TYPE')->get();
    $camposEntidad = [];
    $listaTiposBaseEntidad = TiposEntidad::listarTiposBase();
    if (array_key_exists($entidad, $listaTiposBaseEntidad)) {
      $camposEntidad = Reporte::listarCampos("Entidad");
    }
    foreach ($datosColumnas as $datosColumna) {
      if (!array_key_exists("tipo", $campos[$datosColumna->COLUMN_NAME])) {
        $campos[$datosColumna->COLUMN_NAME]["tipo"] = $datosColumna->DATA_TYPE;
      }
    }
    return $camposEntidad + $campos;
  }

  public static function listarEntidadesRelacionadas($entidad) {
    $clase = "\App\Models\\" . $entidad;
    $tiposEntidadesRelacionadas = $clase::listarEntidadesRelacionadas();
    $tipos = TiposEntidad::listar();

    $tiposSel = [];
    foreach ($tiposEntidadesRelacionadas as $tipoEntidadRelacionada) {
      $tiposSel[$tipoEntidadRelacionada] = $tipos[$tipoEntidadRelacionada];
    }
    return $tiposSel;
  }

  public static function validarEntidadRelacionada($entidad, $id) {
    $clase = "\App\Models\\" . $entidad;
    return $clase::verificarExistencia($id);
  }

  //Util
  public static function generarConsultaBd($campos, $consultaTablas, $filtros) {
    $consultaBD = "SELECT ";
    foreach ($campos as $campo) {
      $consultaBD .= ($consultaBD != "SELECT " ? "," : "") . $campo;
    }
    $consultaBD .= $consultaTablas . " WHERE ";
    for ($i = 0; $i < count($filtros); $i++) {
      $consultaBD .= ($i != 0 ? " AND " : "") . $filtros[$i]["campo"] . $filtros[$i]["operador"] . $filtros[$i]["valor"];
    }
    return $consultaBD;
  }

  private static function obtenerDatos($entidad, $req) {
    return "";
  }

}
