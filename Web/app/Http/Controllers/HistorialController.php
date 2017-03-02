<?php

namespace App\Http\Controllers;

use Auth;
use Mensajes;
use Carbon\Carbon;
use App\Models\Historial;
use App\Http\Controllers\Controller;
use App\Http\Requests\Historial\ListaRequest;
use App\Http\Requests\Historial\FormularioRequest;

class HistorialController extends Controller {

  protected $data = array();

  public function __construct() {
    
  }

  public function obtener($idEntidad, ListaRequest $req) {
    $datos = $req->all();
    $datosHistorial = Historial::obtenerPerfil($datos["numeroCarga"], $idEntidad);
    return response()->json($datosHistorial, 200);
  }

  public function registrar($idEntidad, FormularioRequest $req) {
    try {
      $datos = $req->all();
      $datos["fechaNotificacion"] = Carbon::createFromFormat("d/m/Y H:i:s", $datos["fechaNotificacion"] . " 00:00:00");
      $datos["idEntidades"] = [$idEntidad, (Auth::guest() ? NULL : Auth::user()->idEntidad)];
      Historial::registrar($datos);
      Mensajes::agregarMensajeExitoso("Registro exitoso.");
    } catch (\Exception $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("Ocurrió un problema durante el registro de datos. Por favor inténtelo nuevamente.");
    }
    return back()->with("historial", "1");
  }

}
