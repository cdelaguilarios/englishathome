<?php

namespace App\Http\Controllers;

use Log;
use Mensajes;
use Datatables;
use App\Models\Clase;
use App\Models\Profesor;
use App\Models\PagoProfesor;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfesorRequest;
use App\Http\Requests\Profesor\Pago as PagoReq;

class ProfesorController extends Controller {

  protected $data = array();

  public function __construct() {
    $this->data["seccion"] = "profesores";
  }

  // <editor-fold desc="Profesor">
  public function index() {
    return view("profesor.lista", $this->data);
  }

  public function listar() {
    return Datatables::of(Profesor::listar())->make(true);
  }

  public function create() {
    return view("profesor.crear", $this->data);
  }

  public function store(ProfesorRequest $req) {
    try {
      $idProfesor = Profesor::registrar($req);
      Mensajes::agregarMensajeExitoso("Registro exitoso.");
      return redirect(route("profesores.perfil", ["id" => $idProfesor]));
    } catch (\Exception $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("Ocurrió un problema durante el registro de datos. Por favor inténtelo nuevamente.");
      return redirect(route("profesores.nuevo"));
    }
  }

  public function show($id) {
    try {
      $this->data["profesor"] = Profesor::ObtenerXId($id);
    } catch (ModelNotFoundException $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("No se encontraron datos del profesor seleccionado.");
      return redirect("profesores");
    }
    return view("profesor.perfil", $this->data);
  }

  public function edit($id) {
    try {
      $this->data["profesor"] = Profesor::obtenerXId($id);
    } catch (ModelNotFoundException $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("No se encontraron datos del profesor seleccionado.");
      return redirect("profesores");
    }
    return view("profesor.editar", $this->data);
  }

  public function update($id, ProfesorRequest $req) {
    try {
      Profesor::actualizar($id, $req);
      Mensajes::agregarMensajeExitoso("Actualización exitosa.");
    } catch (\Exception $e) {
      Log::error($e->getMessage());
      Mensajes::agregarMensajeError("Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente.");
    }
    return redirect(route("profesores.editar", ["id" => $id]));
  }

  public function destroy($id) {
    try {
      Profesor::eliminar($id);
    } catch (ModelNotFoundException $e) {
      Log::error($e);
      return response()->json(["mensaje" => "No se pudo eliminar el registro de datos del profesor seleccionado."], 400);
    }
    return response()->json(["mensaje" => "Eliminación exitosa", "id" => $id], 200);
  }

  // </editor-fold>
  // // <editor-fold desc="Pagos">
  public function listarPagos($id) {
    return Datatables::of(PagoProfesor::listar($id))->make(true);
  }

  public function registrarPago($id, PagoReq\PagoRequest  $request) {
    try {
      PagoProfesor::registrar($id, $request);
      Mensajes::agregarMensajeExitoso("Registro exitoso.");
    } catch (\Exception $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("Ocurrió un problema durante el registro de datos. Por favor inténtelo nuevamente.");
    }
    return redirect(route("profesores.perfil", ["id" => $id]));
  }

  public function actualizarEstadoPago($id, PagoReq\ActualizarEstadoRequest $request) {
    try {
      $datos = $request->all();
      PagoProfesor::actualizarEstado($id, $datos);
    } catch (ModelNotFoundException $e) {
      Log::error($e);
      return response()->json(["mensaje" => "Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente."], 400);
    }
    return response()->json(["mensaje" => "Actualización exitosa."], 200);
  }

  public function datosPago($id, $idPago) {
    return response()->json(PagoProfesor::obtenerXId($id, $idPago), 200);
  }

  public function eliminarPago($id, $idPago) {
    try {
      PagoProfesor::eliminar($id, $idPago);
    } catch (ModelNotFoundException $e) {
      Log::error($e);
      return response()->json(["mensaje" => "No se pudo eliminar el registro de datos del pago seleccionado."], 400);
    }
    return response()->json(["mensaje" => "Eliminación exitosa", "id" => $idPago], 200);
  }

  // </editor-fold>
  // <editor-fold desc="Clases">
  public function listarClases($id) {
    return Datatables::of(Clase::listarXProfesor($id))->make(true);
  }

  public function registrarPagoXClases($id, PagoRequest $request) {
    PagoProfesor::registrar($id, $request);
    Mensajes::agregarMensajeExitoso("Registro exitoso.");
    try {
      
    } catch (\Exception $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("Ocurrió un problema durante el registro de datos. Por favor inténtelo nuevamente.");
    }
    return redirect(route("profesores.perfil", ["id" => $id]));
  }

  // </editor-fold>
}
