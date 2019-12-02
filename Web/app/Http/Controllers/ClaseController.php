<?php

namespace App\Http\Controllers;

use Log;
use Auth;
use Mensajes;
use Datatables;
use App\Models\Clase;
use App\Models\Profesor;
use App\Helpers\Enum\RolesUsuario;
use App\Http\Controllers\Controller;
use App\Http\Requests\Clase\BusquedaPropiasRequest;
use App\Http\Requests\Clase\ActualizarEstadoRequest;
use App\Http\Requests\Clase\ActualizarComentarioRequest;
use App\Http\Requests\Clase\ConfirmarProfesorAlumnoRequest;

class ClaseController extends Controller {

  protected $data = array();

  public function __construct() {
    $this->data["seccion"] = "clases";
  }
  
  public function actualizarEstado($id, ActualizarEstadoRequest $req)/* - */ {
    try {
      $datos = $req->all();
      Clase::actualizarEstadoNUEVO($id, $datos["estado"]);
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente."], 400);
    }
    return response()->json(["mensaje" => "Actualización exitosa."], 200);
  }

  public function actualizarComentarios(ActualizarComentarioRequest $req)/* - */ {
    try {
      $datos = $req->all();
      Clase::actualizarComentarios($datos["id"], $datos);
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente."], 400);
    }
    return response()->json(["mensaje" => "Actualización exitosa."], 200);
  }

  // <editor-fold desc="TODO: ELIMINAR">
  public function propias() {
    if (Auth::user()->rol == RolesUsuario::Profesor) {
      $this->data["alumnos"] = Profesor::listarAlumnosVigentes(Auth::user()->idEntidad);
    }
    return view("clase.listaPropias", $this->data);
  }
  
  public function confirmarProfesorAlumno(ConfirmarProfesorAlumnoRequest $req) {
    try {
      Clase::confirmarProfesorAlumno($req->all());
      Mensajes::agregarMensajeExitoso("Confirmación exitosa.");
    } catch (\Exception $e) {
      Log::error($e->getMessage());
      Mensajes::agregarMensajeError("Ocurrió un problema durante la confirmación de la clase. Por favor inténtelo nuevamente.");
    }
    return redirect(route("clases.propias"));
  }

  public function listarPropias(BusquedaPropiasRequest $req) {
    return Datatables::of(Clase::listarPropias($req->all()))
                    ->filterColumn("fechaInicio", function($q, $k) {
                      $q->whereRaw('fechaInicio like ?', ["%{$k}%"])
                      ->orWhereRaw('duracion like ?', ["%{$k}%"])
                      ->orWhereRaw('CONCAT(' . (Auth::user()->rol == RolesUsuario::Alumno ? 'entidadProfesor.nombre, " ", entidadProfesor.apellido' : 'entidadAlumno.nombre, " ", entidadAlumno.apellido') . ') like ?', ["%{$k}%"]);
                    })
                    ->filterColumn("comentarioEntidad", function($q, $k) {
                      $q->whereRaw(Clase::nombreTabla() . (Auth::user()->rol == RolesUsuario::Alumno ? ".comentarioAlumno" : ".comentarioProfesor") . ' like ?', ["%{$k}%"]);
                    })
                    ->filterColumn("comentarioAdministrador", function($q, $k) {
                      $q->whereRaw(Clase::nombreTabla() . (Auth::user()->rol == RolesUsuario::Alumno ? ".comentarioParaAlumno" : ".comentarioParaProfesor") . ' like ?', ["%{$k}%"]);
                    })->make(true);
  }
  // </editor-fold>

}
