<?php

namespace App\Http\Controllers;

use Log;
use Mensajes;
use App\Models\Entidad;
use App\Helpers\Enum\TiposEntidad;
use App\Http\Requests\Entidad\ActualizarImagenRequest;

class EntidadController extends Controller {

  public function actualizarImagenPerfil($id, ActualizarImagenRequest $req) {
    try {
      $datosEntidad = Entidad::ObtenerXId($id);
    } catch (\Exception $e) {
      Log::error($e->getMessage());
      Mensajes::agregarMensajeError("La entidad seleccionada no existe.");
      return redirect(route("/"));
    }

    try {
      Entidad::registrarActualizarImagenPerfil($id, $req->file("imagenPerfil"));
      Mensajes::agregarMensajeExitoso("Actualización exitosa.");
    } catch (\Exception $e) {
      Log::error($e->getMessage());
      Mensajes::agregarMensajeError("Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente.");
    }
    return redirect(route(TiposEntidad::listar()[$datosEntidad->tipo][2], ["id" => $id]));
  }

}
