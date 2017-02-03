<?php

namespace App\Http\Controllers;

use Log;
use App\Models\Historial;

class CronController extends Controller {

  public function enviarCorreos() {
    try {
      Historial::enviarCorreosAdministracion();
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "Ocurrió un problema durante el envío de correos."], 400);
    }
    return response()->json(["mensaje" => "Envío de correos exitosos."], 200);
  }

  public function actualizarEstados() {
    
  }

}
