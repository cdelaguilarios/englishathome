<?php

namespace App\Http\Controllers;

use Log;
use Mensajes;
use Datatables;
use App\Models\Clase;
use App\Models\Profesor;
use App\Models\PagoProfesor;
use App\Http\Controllers\Controller;
use App\Http\Requests\Util as UtilRequest;
use App\Http\Requests\ActualizarHorarioRequest;
use App\Http\Requests\Profesor\BusquedaRequest;
use App\Http\Requests\Profesor\FormularioRequest;
use App\Http\Requests\Profesor\Pago as PagoRequest;
use App\Http\Requests\Profesor\ActualizarEstadoRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProfesorController extends Controller {

  protected $data = array();

  public function __construct() {
    $this->data["seccion"] = "profesores";
  }

  // <editor-fold desc="Profesor">
  public function index() {
    return view("profesor.lista", $this->data);
  }

  public function listar(BusquedaRequest $req) {
    return Datatables::of(Profesor::listar($req->all()))->filterColumn("entidad.nombre", function($q, $k) {
              $q->whereRaw('CONCAT(entidad.nombre, " ", entidad.apellido) like ?', ["%{$k}%"]);
            })->filterColumn("entidad.fechaRegistro", function($q, $k) {
              $q->whereRaw("DATE_FORMAT(entidad.fechaRegistro, '%d/%m/%Y %H:%i:%s') like ?", ["%{$k}%"]);
            })->make(true);
  }

  public function crear() {
    return view("profesor.crear", $this->data);
  }

  public function registrar(FormularioRequest $req) {
    try {
      $idProfesor = Profesor::registrar($req);
      Mensajes::agregarMensajeExitoso("Registro exitoso.");
      return redirect(route("profesores.perfil", ["id" => $idProfesor]));
    } catch (\Exception $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("Ocurrió un problema durante el registro de datos. Por favor inténtelo nuevamente.");
      return redirect(route("profesores.crear"));
    }
  }

  public function perfil($id) {
    try {
      $this->data["profesor"] = Profesor::obtenerXId($id);
    } catch (ModelNotFoundException $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("No se encontraron datos del profesor seleccionado.");
      return redirect(route("profesores"));
    }
    return view("profesor.perfil", $this->data);
  }

  public function editar($id) {
    try {
      $this->data["profesor"] = Profesor::obtenerXId($id);
    } catch (ModelNotFoundException $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("No se encontraron datos del profesor seleccionado.");
      return redirect(route("profesores"));
    }
    return view("profesor.editar", $this->data);
  }

  public function actualizar($id, FormularioRequest $req) {
    try {
      Profesor::actualizar($id, $req);
      Mensajes::agregarMensajeExitoso("Actualización exitosa.");
    } catch (\Exception $e) {
      Log::error($e->getMessage());
      Mensajes::agregarMensajeError("Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente.");
    }
    return redirect(route("profesores.editar", ["id" => $id]));
  }

  public function actualizarEstado($id, ActualizarEstadoRequest $req) {
    try {
      $datos = $req->all();
      Profesor::actualizarEstado($id, $datos["estado"]);
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente."], 400);
    }
    return response()->json(["mensaje" => "Actualización exitosa."], 200);
  }

  public function actualizarHorario($id, ActualizarHorarioRequest $req) {
    try {
      $datos = $req->all();
      Profesor::actualizarHorario($id, $datos["horario"]);
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente."], 400);
    }
    return response()->json(["mensaje" => "Actualización exitosa."], 200);
  }

  public function eliminar($id) {
    try {
      Profesor::eliminar($id);
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "No se pudo eliminar el registro de datos del profesor seleccionado."], 400);
    }
    return response()->json(["mensaje" => "Eliminación exitosa.", "id" => $id], 200);
  }

  // </editor-fold>
  // // <editor-fold desc="Pagos">
  public function listarPagos($id) {
    return Datatables::of(PagoProfesor::listar($id))->filterColumn("pago.fechaRegistro", function($q, $k) {
              $q->whereRaw("DATE_FORMAT(pago.fechaRegistro, '%d/%m/%Y %H:%i:%s') like ?", ["%{$k}%"]);
            })->make(true);
  }

  public function actualizarEstadoPago($id, PagoRequest\ActualizarEstadoRequest $req) {
    try {
      PagoProfesor::actualizarEstado($id, $req->all());
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente."], 400);
    }
    return response()->json(["mensaje" => "Actualización exitosa."], 200);
  }

  public function registrarPago($id, PagoRequest\FormularioRequest $req) {
    try {
      PagoProfesor::registrar($id, $req);
      Mensajes::agregarMensajeExitoso("Registro exitoso.");
    } catch (\Exception $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("Ocurrió un problema durante el registro de datos. Por favor inténtelo nuevamente.");
    }
    return redirect(route("profesores.perfil", ["id" => $id, "sec" => "pago"]));
  }

  public function datosPago($id, $idPago) {
    return response()->json(PagoProfesor::obtenerXId($id, $idPago), 200);
  }

  public function eliminarPago($id, $idPago) {
    try {
      PagoProfesor::eliminar($id, $idPago);
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "No se pudo eliminar el registro de datos del pago seleccionado."], 400);
    }
    return response()->json(["mensaje" => "Eliminación exitosa", "id" => $idPago], 200);
  }

  // </editor-fold>
  // <editor-fold desc="Clases">
  public function listarClases($id, UtilRequest\BusquedaRequest $req) {
    return Datatables::of(Clase::listarXProfesor($id, $req->all()))->filterColumn("nombreAlumno", function($q, $k) {
              $q->whereRaw('CONCAT(entidadAlumno.nombre, " ", entidadAlumno.apellido) like ?', ["%{$k}%"]);
            })->filterColumn("fechaInicio", function($q, $k) {
              $q->whereRaw("DATE_FORMAT(" . Clase::nombreTabla() . ".fechaInicio, '%d/%m/%Y %H:%i:%s') like ?", ["%{$k}%"]);
            })->make(true);
  }

  public function registrarPagoXClases($id, PagoRequest\FormularioRequest $req) {
    try {
      PagoProfesor::registrar($id, $req);
      Mensajes::agregarMensajeExitoso("Registro exitoso.");
    } catch (\Exception $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("Ocurrió un problema durante el registro de datos. Por favor inténtelo nuevamente.");
    }
    return redirect(route("profesores.perfil", ["id" => $id, "sec" => "clase"]));
  }

  // </editor-fold>
}
