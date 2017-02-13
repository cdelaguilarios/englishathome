<?php

namespace App\Http\Controllers;

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

  public function listar(BusquedaRequest $req) {
    return Datatables::of(Curso::listar($req->all()))->make(true);
  }

  public function datos($id) {
    $datosCurso = Curso::obtenerXId($id);
    return response()->json($datosCurso, 200);
  }

  public function crear() {
    return view("curso.crear", $this->data);
  }

  public function registrar(FormularioRequest $req) {
    try {
      $idCurso = Curso::registrar($req->all());
      Mensajes::agregarMensajeExitoso("Registro exitoso.");
      return redirect(route("curos.listar", ["id" => $idCurso]));
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
      Curso::actualizar($id, $req->all());
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
    } catch (ModelNotFoundException $e) {
      Log::error($e);
      return response()->json(["mensaje" => "No se pudo eliminar el registro de datos del curso seleccionado."], 400);
    }
    return response()->json(["mensaje" => "Eliminación exitosa", "id" => $id], 200);
  }

}
