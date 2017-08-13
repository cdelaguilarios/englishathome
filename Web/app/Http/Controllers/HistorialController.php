<?php

namespace App\Http\Controllers;

use Log;
use Auth;
use Input;
use Mensajes;
use Carbon\Carbon;
use App\Models\Entidad;
use App\Models\Historial;
use App\Http\Controllers\Controller;
use App\Http\Requests\Historial\ListaRequest;
use App\Http\Requests\Historial\FormularioRequest;
use App\Http\Requests\Historial\BusquedaEntidadRequest;
use App\Http\Requests\Historial\FormularioCorreoRequest;

class HistorialController extends Controller {

  protected $data = array();

  public function __construct() {
    
  }

  // <editor-fold desc="Historial">
  public function obtener($idEntidad, ListaRequest $req) {
    $datos = $req->all();
    $observador = Input::get("observador");
    $idNotificacion = Input::get("id");
    $datosHistorial = Historial::obtenerPerfil($datos["numeroCarga"], $idEntidad, (isset($observador) && ((int) $observador) == 1), FALSE, FALSE, $idNotificacion);
    return response()->json($datosHistorial, 200);
  }

  public function registrar($idEntidad, FormularioRequest $req) {
    try {
      $datos = $req->all();
      $datos["fechaNotificacion"] = (isset($datos["fechaNotificacion"]) ? Carbon::createFromFormat("d/m/Y H:i:s", $datos["fechaNotificacion"]) : NULL);
      $datos["idEntidades"] = [$idEntidad, (Auth::guest() ? NULL : Auth::user()->idEntidad)];
      $datos["idEntidadDestinataria"] = (((int) $datos["enviarCorreoEntidad"] == 1) ? $idEntidad : NULL);
      Historial::registrar($datos);
      $datos["enviarCorreo"] = (((int) $datos["enviarCorreoEntidad"] == 1) ? 1 : $datos["enviarCorreo"] );
      Historial::registrar($datos);
      Mensajes::agregarMensajeExitoso("Registro exitoso.");
    } catch (\Exception $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("Ocurrió un problema durante el registro de datos. Por favor inténtelo nuevamente.");
    }
    return back()->with("historial", "1");
  }

  // </editor-fold>
  // <editor-fold desc="Correos masivos">

  public function correos() {
    $this->data["seccion"] = "correos";
    return view("correos.index", $this->data);
  }

  public function listarEntidades(BusquedaEntidadRequest $req) {
    return response()->json(Entidad::buscar($req->all()), 200);
  }

  public function registrarCorreos(FormularioCorreoRequest $req) {
    try {
      $correosAdicionalesExcluidos = Historial::registrarCorreos($req->all());
      Mensajes::agregarMensajeExitoso("Registro exitoso. Los correos se enviaran progresivamente.");
      if ($correosAdicionalesExcluidos != "") {
        Mensajes::agregarMensajeAdvertencia("Los siguientes correos adicionales han sido excluidos: " . $correosAdicionalesExcluidos . ".");
      }
    } catch (\Exception $e) {
      Log::error($e->getMessage());
      Mensajes::agregarMensajeError("Ocurrió un problema durante el registro de datos de los correos. Por favor inténtelo nuevamente.");
    }
    return redirect(route("correos"));
  }

  // </editor-fold>
  // <editor-fold desc="Notificaciones">

  public function listarNuevasNotificaciones() {
    $datosHistorial = Historial::obtenerPerfil(1, Auth::id(), TRUE, TRUE, TRUE);
    return response()->json($datosHistorial, 200);
  }

  public function revisarNuevasNotificaciones() {
    $idsNuevasNotificaciones = Input::get("idsNuevasNotificaciones");
    Historial::revisarNotificaciones(Auth::id(), $idsNuevasNotificaciones);
  }

  public function listarNotificaciones() {
    $idNotificacion = Input::get("id");
    Historial::revisarNotificaciones(Auth::id(), (isset($idNotificacion) ? $idNotificacion : []));
    $this->data["seccion"] = "notificaciones";
    return view("notificacion.lista", $this->data);
  }

  // </editor-fold>
}
