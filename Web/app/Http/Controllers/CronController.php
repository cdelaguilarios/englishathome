<?php

namespace App\Http\Controllers;

use Log;
use App\Models\Alumno;
use App\Models\Historial;

class CronController extends Controller {

  public function enviarCorreos() {
    $cabecerasRespuesta = ["Content-Type" => "application/json; charset=UTF-8", "charset" => "utf-8"];
    try {
      //TODO: cambiar
      //Historial::enviarCorreos();
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "Ocurrió un problema durante el envío de correos."], 400, $cabecerasRespuesta, JSON_UNESCAPED_UNICODE);
    }
    return response()->json(["mensaje" => "Envío de correos exitosos."], 200, $cabecerasRespuesta, JSON_UNESCAPED_UNICODE);
  }

  public function sincronizarEstados() {
    $cabecerasRespuesta = ["Content-Type" => "application/json; charset=UTF-8", "charset" => "utf-8"];
    try {
      Alumno::sincronizarEstados();
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "Ocurrió un problema durante la sincronización de estados."], 400, $cabecerasRespuesta, JSON_UNESCAPED_UNICODE);
    }
    return response()->json(["mensaje" => "Sincronización exitosa."], 200, $cabecerasRespuesta, JSON_UNESCAPED_UNICODE);
  }

}
