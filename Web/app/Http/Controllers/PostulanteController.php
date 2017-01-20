<?php

namespace App\Http\Controllers;

use Log;
use Mensajes;
use Datatables;
use App\Models\Postulante;
use App\Http\Controllers\Controller;
use App\Http\Requests\Postulante\BusquedaRequest;
use App\Http\Requests\Postulante\FormularioRequest;
use App\Http\Requests\Postulante\ActualizarEstadoRequest;
use App\Http\Requests\Postulante\ActualizarHorarioRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PostulanteController extends Controller {

  protected $data = array();

  public function __construct() {
    $this->data["seccion"] = "postulantes";
  }

  public function index() {
    return view("postulante.lista", $this->data);
  }

  public function listar(BusquedaRequest $req) {
    return Datatables::of(Postulante::listar($req->all()))->filterColumn("entidad.nombre", function($q, $k) {
              $q->whereRaw('CONCAT(entidad.nombre, " ", entidad.apellido) like ?', ["%{$k}%"]);
            })->make(true);
  }

  public function crear() {
    return view("postulante.crear", $this->data);
  }

  public function registrar(FormularioRequest $req) {
    try {
      Postulante::registrar($req);
      Mensajes::agregarMensajeExitoso("Registro exitoso.");
      return redirect(route("postulantes"));
    } catch (\Exception $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("Ocurrió un problema durante el registro de datos. Por favor inténtelo nuevamente.");
      return redirect(route("postulantes.crear"));
    }
  }

  public function editar($id) {
    try {
      $this->data["postulante"] = Postulante::obtenerXId($id);
    } catch (ModelNotFoundException $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("No se encontraron datos del postulante seleccionado.");
      return redirect(route("postulantes"));
    }
    return view("postulante.editar", $this->data);
  }

  public function actualizar($id, FormularioRequest $req) {
    try {
      Postulante::actualizar($id, $req);
      Mensajes::agregarMensajeExitoso("Actualización exitosa.");
    } catch (\Exception $e) {
      Log::error($e->getMessage());
      Mensajes::agregarMensajeError("Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente.");
    }
    return redirect(route("postulantes.editar", ["id" => $id]));
  }

  public function actualizarEstado($id, ActualizarEstadoRequest $req) {
    try {
      $datos = $req->all();
      Postulante::actualizarEstado($id, $datos["estado"]);
    } catch (ModelNotFoundException $e) {
      Log::error($e);
      return response()->json(["mensaje" => "Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente."], 400);
    }
    return response()->json(["mensaje" => "Actualización exitosa."], 200);
  }

  public function actualizarHorario($id, ActualizarHorarioRequest $req) {
    try {
      $datos = $req->all();
      Postulante::actualizarHorario($id, $datos["horario"]);
    } catch (ModelNotFoundException $e) {
      Log::error($e);
      return response()->json(["mensaje" => "Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente."], 400);
    }
    return response()->json(["mensaje" => "Actualización exitosa."], 200);
  }

  public function eliminar($id) {
    try {
      Postulante::eliminar($id);
    } catch (ModelNotFoundException $e) {
      Log::error($e);
      return response()->json(["mensaje" => "No se pudo eliminar el registro de datos del postulante seleccionado."], 400);
    }
    return response()->json(["mensaje" => "Eliminación exitosa", "id" => $id], 200);
  }

}
