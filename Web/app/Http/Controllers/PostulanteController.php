<?php

namespace App\Http\Controllers;

use Log;
use Input;
use Mensajes;
use Datatables;
use App\Models\Postulante;
use App\Http\Controllers\Controller;
use App\Http\Requests\ActualizarHorarioRequest;
use App\Http\Requests\Postulante\BusquedaRequest;
use App\Http\Requests\Postulante\FormularioRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\Postulante\ActualizarEstadoRequest;

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
        })->filterColumn("entidad.fechaRegistro", function($q, $k) {
          $q->whereRaw("DATE_FORMAT(entidad.fechaRegistro, '%d/%m/%Y %H:%i:%s') like ?", ["%{$k}%"]);
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

  public function crearExterno() {
    Auth::logout();
    $nuevoRegistro = Input::get("nr");
    $this->data["nuevoRegistro"] = (isset($nuevoRegistro));
    $this->data["vistaExterna"] = TRUE;
    return view("postulante.crear", $this->data);
  }

  public function registrarExterno(FormularioRequest $req) {
    try {
      Postulante::registrar($req);
    } catch (\Exception $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("Ocurrió un problema durante el registro de datos. Por favor inténtelo nuevamente.");
    }
    return redirect(route("postulantes.crear.externo", ["nr" => 1]));
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
      $datos = $req->all();
      Postulante::actualizar($id, $req);
      if ($datos["registrarComoProfesor"] == 1) {
        Postulante::registrarProfesor($id);
        Mensajes::agregarMensajeExitoso("El postulante seleccionado ha sido registrado como nuevo profesor.");
        return redirect(route("interesados"));
      } else {
        Mensajes::agregarMensajeExitoso("Actualización exitosa.");
      }
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
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente."], 400);
    }
    return response()->json(["mensaje" => "Actualización exitosa."], 200);
  }

  public function actualizarHorario($id, ActualizarHorarioRequest $req) {
    try {
      $datos = $req->all();
      Postulante::actualizarHorario($id, $datos["horario"]);
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente."], 400);
    }
    return response()->json(["mensaje" => "Actualización exitosa."], 200);
  }

  public function perfilProfesor($id) {
    try {
      $idProfesor = Postulante::obtenerIdProfesor($id);
      return redirect($idProfesor > 0 ? route("profesores.perfil", ["id" => $idProfesor]) : route("postulantes"));
    } catch (\Exception $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("No se encontraron datos del postulante o del profesor seleccionado.");
      return redirect("postulantes");
    }
  }

  public function eliminar($id) {
    try {
      Postulante::eliminar($id);
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "No se pudo eliminar el registro de datos del postulante seleccionado."], 400);
    }
    return response()->json(["mensaje" => "Eliminación exitosa", "id" => $id], 200);
  }

}
