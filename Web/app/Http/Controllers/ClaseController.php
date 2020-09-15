<?php

namespace App\Http\Controllers;

use Log;
use App\Models\Clase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Clase\ActualizarEstadoRequest;
use App\Http\Requests\Clase\ActualizarComentarioRequest;

class ClaseController extends Controller {

  protected $data = array();

  public function __construct() {
    $this->data["seccion"] = "clases";
  }
  
  public function actualizarEstado($id, ActualizarEstadoRequest $req) {
    try {
      $datos = $req->all();
      Clase::actualizarEstado($id, $datos["estado"]);
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente."], 400);
    }
    return response()->json(["mensaje" => "Actualización exitosa."], 200);
  }

  public function actualizarComentarios(ActualizarComentarioRequest $req) {
    try {
      $datos = $req->all();
      Clase::actualizarComentarios($datos["id"], $datos);
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente."], 400);
    }
    return response()->json(["mensaje" => "Actualización exitosa."], 200);
  }

}
