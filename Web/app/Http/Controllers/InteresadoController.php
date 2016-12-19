<?php

namespace App\Http\Controllers;

use Log;
use Mensajes;
use Datatables;
use App\Models\Interesado;
use App\Http\Controllers\Controller;
use App\Http\Requests\InteresadoRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class InteresadoController extends Controller {

  protected $data = array();

  public function __construct() {
    $this->data["seccion"] = "interesados";
  }

  public function index() {
    return view("interesado.lista", $this->data);
  }

  public function listar() {
    return Datatables::of(Interesado::Listar())->make(true);
  }

  public function create() {
    return view("interesado.crear", $this->data);
  }

  public function store(InteresadoRequest $req) {
    try {
      $idInteresado = Interesado::Registrar($req);
      Mensajes::agregarMensajeExitoso("Registro exitoso.");
      return redirect(route("interesados.editar", ["id" => $idInteresado]));
    } catch (\Exception $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("Ocurrió un problema durante el registro de datos. Por favor inténtelo nuevamente.");
      return redirect(route("interesados.nuevo"));
    }
  }

  public function edit($id) {
    try {
      $this->data["interesado"] = Interesado::ObtenerXId($id);
    } catch (ModelNotFoundException $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("No se encontraron datos de la persona interesada seleccionada.");
      return redirect("interesados");
    }
    return view("interesado.editar", $this->data);
  }

  public function update($id, InteresadoRequest $req) {
    try {
      Interesado::Actualizar($id, $req);
      Mensajes::agregarMensajeExitoso("Actualización exitosa.");
    } catch (\Exception $e) {
      Log::error($e->getMessage());
      Mensajes::agregarMensajeError("Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente.");
    }
    return redirect(route("interesados.editar", ["id" => $id]));
  }

  public function destroy($id) {
    try {
      Interesado::Eliminar($id);
    } catch (ModelNotFoundException $e) {
      Log::error($e);
      return response()->json(["mensaje" => "No se pudo eliminar el registro de datos de la persona interesada seleccionada."], 400);
    }
    return response()->json(["mensaje" => "Eliminación exitosa", "id" => $id], 200);
  }

}
