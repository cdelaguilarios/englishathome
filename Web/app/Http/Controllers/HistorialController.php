<?php

namespace App\Http\Controllers;

use Log;
use Auth;
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

  public function obtener($idEntidad, ListaRequest $req) {
    $datos = $req->all();
    $datosHistorial = Historial::obtenerPerfil($datos["numeroCarga"], $idEntidad);
    return response()->json($datosHistorial, 200);
  }

  public function registrar($idEntidad, FormularioRequest $req) {
    try {
      $datos = $req->all();
      $datos["fechaNotificacion"] = (isset($datos["fechaNotificacion"]) ? Carbon::createFromFormat("d/m/Y H:i:s", $datos["fechaNotificacion"] . " 00:00:00") : NULL);
      $datos["idEntidades"] = [$idEntidad, (Auth::guest() ? NULL : Auth::user()->idEntidad)];
      Historial::registrar($datos);
      Mensajes::agregarMensajeExitoso("Registro exitoso.");
    } catch (\Exception $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("Ocurrió un problema durante el registro de datos. Por favor inténtelo nuevamente.");
    }
    return back()->with("historial", "1");
  }

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

}
