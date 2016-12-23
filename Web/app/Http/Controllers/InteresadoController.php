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
    return Datatables::of(Interesado::listar($datos))->make(true);
  }

  public function create() {
    return view("interesado.crear", $this->data);
  }

  public function store(FormularioRequest $req) {
    try {
      $datos = $req->all();
      $id = Interesado::registrar($datos);
      Mensajes::agregarMensajeExitoso("Registro exitoso.");
      return redirect(route("interesados.editar", ["id" => $id]));
    } catch (\Exception $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("Ocurrió un problema durante el registro de datos. Por favor inténtelo nuevamente.");
      return redirect(route("interesados.nuevo"));
    }
  }

  public function registroExterno(FormularioRequest $req) {
    try {
      $datos = $req->all();
      $id = Interesado::registrar($datos);
    } catch (ModelNotFoundException $e) {
      Log::error($e);
      return response()->json(["mensaje" => "Ocurrió un problema durante el registro de datos. Por favor inténtelo nuevamente."], 400);
    }
    return response()->json(["mensaje" => "Registro exitoso.", "id" => $id], 200);
  }

  public function edit($id) {
    try {
      $this->data["interesado"] = Interesado::obtenerXId($id);
    } catch (ModelNotFoundException $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("No se encontraron datos de la persona interesada seleccionada.");
      return redirect("interesados");
    }
    return view("interesado.editar", $this->data);
  }

  public function update($id, FormularioRequest $req) {
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

  public function destroy($id) {
    try {
      Interesado::eliminar($id);
    } catch (ModelNotFoundException $e) {
      Log::error($e);
      return response()->json(["mensaje" => "No se pudo eliminar el registro de datos de la persona interesada seleccionada."], 400);
    }
    return response()->json(["mensaje" => "Eliminación exitosa", "id" => $id], 200);
  }

}
