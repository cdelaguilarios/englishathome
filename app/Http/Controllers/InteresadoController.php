<?php

namespace App\Http\Controllers;

use Log;
use Input;
use Mensajes;
use Datatables;
use App\Models\Interesado;
use App\Http\Controllers\Controller;
use App\Helpers\Enum\OrigenesInteresado;
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
    return Datatables::of(Interesado::listar($req->all()))
                    ->filterColumn("entidad.nombre", function($q, $k) {
                      $q->whereRaw('CONCAT(entidad.nombre, " ", entidad.apellido) like ?', ["%{$k}%"]);
                    })
                    ->filterColumn("consulta", function($q, $k) {
                      $q->whereRaw('consulta like ?', ["%{$k}%"])
                      ->orWhereRaw('cursoInteres like ?', ["%{$k}%"])
                      ->orWhereRaw('origen like ?', ["%{$k}%"]);
                    })
                    ->filterColumn("entidad.correoElectronico", function($q, $k) {
                      $q->whereRaw('entidad.correoElectronico like ?', ["%{$k}%"])
                      ->orWhereRaw('entidad.telefono like ?', ["%{$k}%"]);
                    })
                    ->filterColumn("entidad.fechaRegistro", function($q, $k) {
                      $q->whereRaw("DATE_FORMAT(entidad.fechaRegistro, '%d/%m/%Y %H:%i:%s') like ?", ["%{$k}%"]);
                    })->make(true);
  }

  public function buscar() {
    $termino = Input::get("termino");

    $interesadosPro = [];
    $interesados = Interesado::listarBusqueda($termino["term"]);
    foreach ($interesados as $id => $nombreCompleto) {
      $interesadosPro[] = ['id' => $id, 'text' => $nombreCompleto];
    }
    return \Response::json(["results" => $interesadosPro]);
  }

  public function crear() {
    return view("interesado.crear", $this->data);
  }

  public function registrar(FormularioRequest $req) {
    try {
      Interesado::registrar($req->all());
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
      //TODO: Evaluar la posibilidad de validar el registro externo a través de un token o algo parecido
      $datos = $req->all();
      $datos["origen"] = OrigenesInteresado::Web;
      $id = Interesado::registrar($datos);
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "Ocurrió un problema durante el registro de datos. Por favor inténtelo nuevamente."], 500);
    }
    return response()->json(["mensaje" => "Registro exitoso.", "id" => $id], 200);
  }

  public function editar($id) {
    try {
      $this->data["interesado"] = Interesado::obtenerXId($id);
    } catch (ModelNotFoundException $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("No se encontraron datos de la persona interesada seleccionada. Es posible que haya sido eliminado.");
      return redirect("interesados");
    }
    return view("interesado.editar", $this->data);
  }

  public function actualizar($id, FormularioRequest $req) {
    try {
      $datos = $req->all();
      Interesado::actualizar($id, $datos);

      if ($datos["registrarComoAlumno"] == 1) {
        Interesado::registrarAlumno($id);
        Mensajes::agregarMensajeExitoso("El interesado seleccionado ha sido registrado como nuevo alumno.");
        return redirect(route("interesados"));
      } else {
        Mensajes::agregarMensajeExitoso("Actualización exitosa.");
      }
    } catch (\Exception $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente.");
    }
    return redirect(route("interesados.editar", ["id" => $id]));
  }

  public function actualizarEstado($id, ActualizarEstadoRequest $req) {
    try {
      $datos = $req->all();
      Interesado::actualizarEstado($id, $datos["estado"]);
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente."], 500);
    }
    return response()->json(["mensaje" => "Actualización exitosa."], 200);
  }

  public function cotizar($id) {
    try {
      $this->data["interesado"] = Interesado::obtenerXId($id);
    } catch (ModelNotFoundException $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("No se encontraron datos de la persona interesada seleccionada. Es posible que haya sido eliminado.");
      return redirect("interesados");
    }
    return view("interesado.cotizar", $this->data);
  }

  public function enviarCotizacion($id, FormularioCotizacionRequest $req) {
    try {
      Interesado::enviarCotizacion($id, $req->all());
      Mensajes::agregarMensajeExitoso("Cotización enviada.");
    } catch (\Exception $e) {
      Log::error($e->getMessage());
      Mensajes::agregarMensajeError("Ocurrió un problema durante el envío de la cotización. Por favor inténtelo nuevamente.");
    }
    return redirect(route("interesados.cotizar", ["id" => $id]));
  }

  public function perfilAlumno($id) {
    try {
      $idAlumno = Interesado::obtenerIdAlumno($id);
      return redirect($idAlumno > 0 ? route("alumnos.perfil", ["id" => $idAlumno]) : route("interesados"));
    } catch (\Exception $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("No se encontraron datos de la persona interesada o del alumno seleccionado.");
      return redirect("interesados");
    }
  }

  public function eliminar($id) {
    try {
      Interesado::eliminar($id);
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "No se pudo eliminar el registro de datos de la persona interesada seleccionada."], 400);
    }
    return response()->json(["mensaje" => "Eliminación exitosa.", "id" => $id], 200);
  }

}
