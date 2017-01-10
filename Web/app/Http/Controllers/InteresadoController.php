<?php

namespace App\Http\Controllers;

use Log;
use Mensajes;
use Datatables;
use App\Models\Interesado;
use App\Http\Controllers\Controller;
use App\Http\Requests\Interesado\BusquedaRequest;
use App\Http\Requests\Interesado\FormularioRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\Interesado\ActualizarEstadoRequest;
use App\Http\Requests\Interesado\FormularioCotizacionRequest;

class InteresadoController extends Controller {

  protected $data = array();

  public function __construct() {
    $this->data["seccion"] = "interesados";
  }

  public function index() {
    return view("interesado.lista", $this->data);
  }

  public function listar(BusquedaRequest $req) {
    $datos = $req->all();
    return Datatables::of(Interesado::listar($datos))->filterColumn("entidad.nombre", function($q, $k) {
              $q->whereRaw('CONCAT(entidad.nombre, " ", entidad.apellido) like ?', ["%{$k}%"]);
            })->make(true);
  }

  public function crear() {
    return view("interesado.crear", $this->data);
  }

  public function registrar(FormularioRequest $req) {
    try {
      $datos = $req->all();
      Interesado::registrar($datos);
      Mensajes::agregarMensajeExitoso("Registro exitoso.");
      return redirect(route("interesados"));
    } catch (\Exception $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("Ocurrió un problema durante el registro de datos. Por favor inténtelo nuevamente.");
      return redirect(route("interesados.crear"));
    }
  }

  public function registrarExterno(FormularioRequest $req) {
    try {
      $datos = $req->all();
      $id = Interesado::registrar($datos);
    } catch (ModelNotFoundException $e) {
      Log::error($e);
      return response()->json(["mensaje" => "Ocurrió un problema durante el registro de datos. Por favor inténtelo nuevamente."], 400);
    }
    return response()->json(["mensaje" => "Registro exitoso.", "id" => $id], 200);
  }

  public function editar($id) {
    try {
      $this->data["interesado"] = Interesado::obtenerXId($id);
    } catch (ModelNotFoundException $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("No se encontraron datos de la persona interesada seleccionada.");
      return redirect("interesados");
    }
    return view("interesado.editar", $this->data);
  }

  public function actualizar($id, FormularioRequest $req) {
    try {
      $datos = $req->all();
      Interesado::actualizar($id, $datos);
      Mensajes::agregarMensajeExitoso("Actualización exitosa.");
    } catch (\Exception $e) {
      Log::error($e->getMessage());
      Mensajes::agregarMensajeError("Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente.");
    }
    return redirect(route("interesados.editar", ["id" => $id]));
  }

  public function actualizarEstado($id, ActualizarEstadoRequest $request) {
    try {
      $datos = $request->all();
      Interesado::actualizarEstado($id, $datos["estado"]);
    } catch (ModelNotFoundException $e) {
      Log::error($e);
      return response()->json(["mensaje" => "Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente."], 400);
    }
    return response()->json(["mensaje" => "Actualización exitosa."], 200);
  }

  public function cotizar($id) {
    try {
      $this->data["interesado"] = Interesado::obtenerXId($id);
    } catch (ModelNotFoundException $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("No se encontraron datos de la persona interesada seleccionada.");
      return redirect("interesados");
    }
    return view("interesado.cotizacion", $this->data);
  }

  public function enviarCotizacion($id, FormularioCotizacionRequest $req) {
    try {
      $datos = $req->all();
      Interesado::enviarCotizacion($id, $datos);
      Mensajes::agregarMensajeExitoso("Cotización enviada.");
    } catch (\Exception $e) {
      Log::error($e->getMessage());
      Mensajes::agregarMensajeError("Ocurrió un problema durante el envio de la cotización. Por favor inténtelo nuevamente.");
    }
    return redirect(route("interesados.cotizar", ["id" => $id]));
  }

  public function eliminar($id) {
    try {
      Interesado::eliminar($id);
    } catch (ModelNotFoundException $e) {
      Log::error($e);
      return response()->json(["mensaje" => "No se pudo eliminar el registro de datos de la persona interesada seleccionada."], 400);
    }
    return response()->json(["mensaje" => "Eliminación exitosa", "id" => $id], 200);
  }

}
