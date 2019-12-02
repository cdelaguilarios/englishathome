<?php

namespace App\Http\Controllers;

use Log;
use App\Models\Ubigeo;
use App\Http\Controllers\Controller;

class UbigeoController extends Controller/* - */ {

  public function listarDepartamentos()/* - */ {
    try {
      $departamentos = Ubigeo::listarDepartamentos();
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "No se pudo obtener la lista de departamentos."], 400);
    }
    return response()->json(["elementosUbigeo" => $departamentos], 200);
  }

  public function listarProvincias($codigoDepartamento)/* - */ {
    try {
      $provincias = Ubigeo::listarProvinciasXCodigoDepartamento($codigoDepartamento);
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "No se pudo obtener la lista de provincias del departamento seleccionado."], 400);
    }
    return response()->json(["elementosUbigeo" => $provincias], 200);
  }

  public function listarDistritos($codigoProvincia)/* - */ {
    try {
      $distritos = Ubigeo::listarDistritosXCodigoProvincia($codigoProvincia);
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "No se pudo obtener la lista de distritos de la provincia seleccionada."], 400);
    }
    return response()->json(["elementosUbigeo" => $distritos], 200);
  }

}
