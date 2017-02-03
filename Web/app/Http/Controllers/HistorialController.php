<?php

namespace App\Http\Controllers;

use Auth;
use Mensajes;
use App\Models\Historial;
use App\Http\Controllers\Controller;
use App\Http\Requests\Historial\ListaRequest;
use App\Http\Requests\Historial\FormularioRequest;

class HistorialController extends Controller {

  protected $data = array();

  public function __construct() {
    try {
      if (Usuario::usuarioUnicoPrincipal($id)) {
        return response()->json(["mensaje" => "El usuario que usted desea eliminar es el único 'Usuario principal' y sus datos no pueden ser borrados."], 400);
      }
      Usuario::eliminar($id);
    } catch (ModelNotFoundException $e) {
      Log::error($e);
      return response()->json(["mensaje" => "No se pudo eliminar el registro de datos del usuario seleccionado."], 400);
    }
    return response()->json(["mensaje" => "Eliminación exitosa", "id" => $id], 200);
  }

  public function obtener($id, ListaRequest $req) {
    $datos = $req->all();
    $datosHistorial = Historial::obtener($datos["numeroCarga"], $id);
    return response()->json($datosHistorial, 200);
  }

  public function registrar($id, FormularioRequest $req) {
    try {
      $datos = $req->all();
      $datos["idEntidades"] = [$id, (Auth::guest() ? NULL : Auth::user()->idEntidad)];
      Historial::registrar($datos);
      Mensajes::agregarMensajeExitoso("Registro exitoso.");
    } catch (\Exception $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("Ocurrió un problema durante el registro de datos. Por favor inténtelo nuevamente.");
    }
    return back();
  }

}
