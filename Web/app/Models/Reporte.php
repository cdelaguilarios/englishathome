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

  public static function registrar($entidad, $req) {
    $clase = "\App\Models\\" . $entidad;

    $datos = $req->all();
    $reporte = new Curso($datos);
    $reporte->datos = Reporte::obtenerDatos($entidad, $req);
    $reporte->consulta = $clase::obtenerConsultaBdReporte();
    $reporte->fechaRegistro = Carbon::now()->toDateTimeString();
    $reporte->save();
    return $reporte->id;
  }

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
