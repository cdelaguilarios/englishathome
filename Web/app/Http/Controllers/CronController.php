<?php

namespace App\Http\Controllers;

use Log;
use App\Models\Correo;

class CronController extends Controller {

  public function enviarCorreos() {
    $cabecerasRespuesta = ["Content-Type" => "application/json; charset=UTF-8", "charset" => "utf-8"];
    try {
      Correo::enviar();
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "Ocurrió un problema durante el envío de correos"], 400, $cabecerasRespuesta, JSON_UNESCAPED_UNICODE);
    }
    return response()->json(["mensaje" => "Envío de correos exitoso"], 200, $cabecerasRespuesta, JSON_UNESCAPED_UNICODE);
  }

}
