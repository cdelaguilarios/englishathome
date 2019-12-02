<?php

namespace App\Http\Controllers;

use Log;
use Mensajes;
use App\Models\Entidad;
use App\Helpers\Enum\TiposEntidad;
use App\Http\Requests\Entidad\ActualizarImagenRequest;
use App\Http\Requests\Entidad\ActualizarCredencialesAccesoRequest;
use App\Http\Requests\Entidad\ActualizarComentariosAdministradorRequest;

class EntidadController extends Controller/* - */ {

  public function actualizarComentariosAdministrador($id, ActualizarComentariosAdministradorRequest $req)/* - */ {
    try {
      Entidad::actualizarComentariosAdministrador($id, $req->all());
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente."], 400);
    }
    return response()->json(["mensaje" => "Actualización exitosa."], 200);
  }
  
  public function actualizarCredencialesAcceso($id, ActualizarCredencialesAccesoRequest $req)/* - */ {
    try {
      $datosEntidad = Entidad::ObtenerXId($id);
    } catch (\Exception $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("La entidad seleccionada no existe.");
      return redirect(route("/"));
    }

    try {
      Entidad::actualizarCredencialesAcceso($id, $req->all());
      Mensajes::agregarMensajeExitoso("Actualización exitosa.");
    } catch (\Exception $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente.");
    }
    return redirect(route(TiposEntidad::listarTiposBase()[$datosEntidad->tipo][3], ["id" => $id]));
  }

  public function actualizarImagenPerfil($id, ActualizarImagenRequest $req)/* - */ {
    try {
      $datosEntidad = Entidad::ObtenerXId($id);
    } catch (\Exception $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("La entidad seleccionada no existe.");
      return redirect(route("/"));
    }

    try {
      Entidad::registrarActualizarImagenPerfil($id, $req->file("imagenPerfil"));
      Mensajes::agregarMensajeExitoso("Actualización exitosa.");
    } catch (\Exception $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente.");
    }
    return redirect(route(TiposEntidad::listarTiposBase()[$datosEntidad->tipo][3], ["id" => $id]));
  }

}
