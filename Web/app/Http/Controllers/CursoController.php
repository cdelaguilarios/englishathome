<?php

namespace App\Http\Controllers;

use Log;
use Mensajes;
use Datatables;
use App\Models\Curso;
use App\Http\Controllers\Controller;
use App\Http\Requests\Curso\FormularioRequest;

class CursoController extends Controller {

  protected $data = array();

  public function __construct() {
    $this->data["seccion"] = "cursos";
  }

  public function index() {
    return view("curso.lista", $this->data);
  }

  public function listar() {
    return Datatables::of(Curso::listar())->make(true);
  }

  public function datos($id) {
    return response()->json(Curso::obtenerXId($id), 200);
  }

  public function crear() {
    return view("curso.crear", $this->data);
  }

  public function registrar(FormularioRequest $req) {
    try {
      Curso::registrar($req);
      Mensajes::agregarMensajeExitoso("Registro exitoso.");
      return redirect(route("cursos"));
    } catch (\Exception $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("Ocurrió un problema durante el registro de datos. Por favor inténtelo nuevamente.");
      return redirect(route("cursos.crear"));
    }
  }

  public function editar($id) {
    try {
      $this->data["curso"] = Curso::obtenerXId($id);
    } catch (ModelNotFoundException $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("No se encontraron datos del curso seleccionado.");
      return redirect(route("cursos"));
    }
    return view("curso.editar", $this->data);
  }

  public function actualizar($id, FormularioRequest $req) {
    try {
      Curso::actualizar($id, $req);
      Mensajes::agregarMensajeExitoso("Actualización exitosa.");
    } catch (\Exception $e) {
      Log::error($e->getMessage());
      Mensajes::agregarMensajeError("Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente.");
    }
    return redirect(route("cursos.editar", ["id" => $id]));
  }

  public function eliminar($id) {
    try {
      Curso::eliminar($id);
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "No se pudo eliminar el registro de datos del curso seleccionado."], 400);
    }
    return response()->json(["mensaje" => "Eliminación exitosa.", "id" => $id], 200);
  }

}
