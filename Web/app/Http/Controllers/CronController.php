<?php

namespace App\Http\Controllers;

use Log;
use Mail;
use App\Models\Alumno;
use App\Models\Historial;

class CronController extends Controller {

  public function test() {
    $nombreCompletoDestinatario = "usuario administrador";
    $mensaje = '<p>Correo de prueba</p>';
    Mail::send("notificacion.plantillaCorreo", ["nombreCompletoDestinatario" => $nombreCompletoDestinatario, "mensaje" => $mensaje], function ($m) {
      $m->to("cdelaguilarios@gmail.com", "Administrador - English at home")->subject("English at home - Notificación Test");
    });
  }

  public function enviarCorreos() {
    try {
      Historial::enviarCorreosAdministracion();
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "Ocurrió un problema durante el envío de correos."], 400);
    }
    return response()->json(["mensaje" => "Envío de correos exitosos."], 200);
  }

  public function sincronizarEstados() {
    try {
      Alumno::sincronizarEstados();
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "Ocurrió un problema durante la sincronización de estados."], 400);
    }
    return response()->json(["mensaje" => "Sincronización exitosa."], 200);
  }

}
