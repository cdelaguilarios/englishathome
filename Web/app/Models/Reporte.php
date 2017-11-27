<?php

namespace App\Models;

use DB;
use Carbon\Carbon;
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

  public static function registrar($req) {
    $datos = $req->all();
    $reporte = new Reporte($datos);
    $reporte->datos = Reporte::obtenerDatos($datos);
    $reporte->fechaRegistro = Carbon::now()->toDateTimeString();
    $reporte->save();
    return $reporte->id;
  }

  public static function actualizar($id, $req) {
    $datos = $req->all();
    $reporte = Reporte::obtenerXId($id);
    $reporte->datos = Reporte::obtenerDatos($datos);
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

  private static function obtenerDatos($datos) {
    $datosEntidad = json_decode($datos["entidad"], false, 512, JSON_UNESCAPED_UNICODE);

    $datosProcesados = [
        "entidad" => $datosEntidad->nombre
    ];

    $camposSeleccionadosPro = [];
    $listaCamposEntidad = Reporte::listarCampos($datosEntidad->nombre);
    foreach ($datosEntidad->camposSel as $campo) {
      $campoSeleccionado = Reporte::obtenerDatosCampo($datos, $datosEntidad->nombre, $campo, $listaCamposEntidad[$campo]);
      if (!is_null($campoSeleccionado)) {
        $camposSeleccionadosPro[] = $campoSeleccionado;
      }
    }
    $datosProcesados["camposSeleccionados"] = $camposSeleccionadosPro;

    if (!is_null($datos["entidadesRelacionadas"])) {
      $entidadesRelacionadasPro = [];
      $entidadesRelacionadas = json_decode($datos["entidadesRelacionadas"], false, 512, JSON_UNESCAPED_UNICODE);
      if (!is_array($entidadesRelacionadas)) {
        $entidadesRelacionadas = [];
      }
      foreach ($entidadesRelacionadas as $datosEntidadRelacionada) {
        $entidadRelacionadaPro = [
            "entidad" => $datosEntidadRelacionada->nombre
        ];

        $camposSeleccionadosEntidadRelPro = [];
        $listaCamposEntidadRelacionada = Reporte::listarCampos($datosEntidadRelacionada->nombre);
        foreach ($datosEntidadRelacionada->camposSel as $campo) {
          $campoSeleccionado = Reporte::obtenerDatosCampo($datos, $datosEntidadRelacionada->nombre, $campo, $listaCamposEntidadRelacionada[$campo]);
          if (!is_null($campoSeleccionado)) {
            $camposSeleccionadosEntidadRelPro[] = $campoSeleccionado;
          }
        }
        $campoSeleccionado = Reporte::obtenerDatosCampo($datos, $datosEntidadRelacionada->nombre, "busqueda", ["titulo" => "Búsqueda", "tipo" => "busqueda"]);
        if (!is_null($campoSeleccionado)) {
          $camposSeleccionadosEntidadRelPro[] = $campoSeleccionado;
        }

        $entidadRelacionadaPro["camposSeleccionados"] = $camposSeleccionadosEntidadRelPro;
        $entidadesRelacionadasPro[] = $entidadRelacionadaPro;
      }
      if (count($entidadesRelacionadasPro) > 0) {
        $datosProcesados["entiadesRelacionadas"] = $entidadesRelacionadasPro;
      }
    }
    return json_encode($datosProcesados, JSON_UNESCAPED_UNICODE);
  }

  private static function obtenerDatosCampo($datos, $entidad, $campo, $datosCampo) {
    $campoSeleccionado = [
        "nombre" => $campo,
        "titulo" => $datosCampo["titulo"],
        "tipo" => $datosCampo["tipo"]
    ];

    $filtro = NULL;
    $nomTipoFiltro = strtolower("sel-tipo-filtro-" . $entidad . "-" . $campo);
    $nomFiltro = strtolower("inp-filtro-" . $entidad . "-" . $campo);
    $datTipoFiltroValido = (isset($datos[$nomTipoFiltro]) && !empty($datos[$nomTipoFiltro]));
    $datFiltroValido = (isset($datos[$nomFiltro]) && !empty($datos[$nomFiltro]));


    if ((!$datTipoFiltroValido && !in_array(strtolower($datosCampo["tipo"]), ["tinyint", "sexo", "tipodocumento"])) ||
            (!$datFiltroValido && !in_array(strtolower($datosCampo["tipo"]), ["datetime", "timestamp", "tinyint"]))) {
      return $campoSeleccionado;
    }

    switch (strtolower($datosCampo["tipo"])) {
      case "varchar":
      case "text":
      case "char":
      case "int":
      case "float":
        $filtro = [
            "tipo" => $datos[$nomTipoFiltro],
            "valores" => [$datos[$nomFiltro]]
        ];
        break;
      case "datetime":
      case "timestamp":
        $nomFechaIniFiltro = strtolower("inp-filtro-fecha-inicio-" . $entidad . "-" . $campo);
        $nomFechaFinFiltro = strtolower("inp-filtro-fecha-fin-" . $entidad . "-" . $campo);
        $datFiltroFechaIniValido = (isset($datos[$nomFechaIniFiltro]) && !empty($datos[$nomFechaIniFiltro]));
        $datFiltroFechaFinValido = (isset($datos[$nomFechaFinFiltro]) && !empty($datos[$nomFechaFinFiltro]));

        if ($datFiltroFechaIniValido) {
          $filtro = [
              "tipo" => $datos[$nomTipoFiltro],
              "valores" => [$datos[$nomFechaIniFiltro]]
          ];
        }
        if ($datFiltroFechaIniValido && $datFiltroFechaFinValido && $datos[$nomTipoFiltro] == "BETWEEN") {
          $filtro["valores"][] = $datos[$nomFechaFinFiltro];
        }
        break;
      case "tinyint":
      case "sexo":
      case "tipodocumento":
        $filtro = [
            "tipo" => "=",
            "valores" => [$datos[$nomFiltro]]
        ];
        break;
      case "busqueda":
        $filtro = [
            "tipo" => "=",
        ];
        $ids = "";
        $nombres = "";
        foreach ($datos[$nomFiltro] as $idEntidad) {
          $clase = "\App\Models\\" . $entidad;
          $datosEntidadRel = $clase::obtenerXId($idEntidad);

          $ids .= ($ids !== "" ? "," : "") . $idEntidad;
          $nombres .= ($nombres !== "" ? "," : "") . $datosEntidadRel["nombre"] . " " . $datosEntidadRel ["apellido"];
        }
        $filtro["valores"] = [$ids, $nombres];
        break;
    }
    if (!is_null($filtro)) {
      $campoSeleccionado["filtro"] = $filtro;
    }
    return $campoSeleccionado;
  }

}
