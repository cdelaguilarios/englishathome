<?php

namespace App\Http\Controllers;

use App\Models\Historial;
use App\Http\Controllers\Controller;
use App\Http\Requests\Historial\ListaRequest;
use App\Http\Requests\Historial\FormularioRequest;

class HistorialController extends Controller {

  protected $data = array();

  public function __construct() {    
  }

  public function historial($id, ListaRequest $req) {
    $datos = $req->all();
    $datosHistorial = Historial::obtener($datos["numeroCarga"], $id);
    return response()->json($datosHistorial, 200);
  }
  
  public function registrar($id, FormularioRequest $req) {
    try {
      Historial::registrar($id, $req->all());
      Mensajes::agregarMensajeExitoso("Registro exitoso.");
    } catch (\Exception $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("Ocurrió un problema durante el registro de datos. Por favor inténtelo nuevamente.");
    }
    return redirect(route("alumnos.perfil", ["id" => $id]));
  }

}
