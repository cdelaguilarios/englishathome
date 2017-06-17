<?php

namespace App\Http\Controllers;

use Log;
use Auth;
use Mensajes;
use Datatables;
use App\Models\Usuario;
use App\Helpers\Enum\RolesUsuario;
use App\Helpers\Enum\EstadosUsuario;
use App\Http\Controllers\Controller;
use App\Http\Requests\Usuario\BusquedaRequest;
use App\Http\Requests\Usuario\FormularioRequest;
use App\Http\Requests\Usuario\ActualizarEstadoRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UsuarioController extends Controller {

  protected $data = array();

  public function __construct() {
    $this->data["seccion"] = "usuarios";
  }

  public function index() {
    return view("usuario.lista", $this->data);
  }

  public function listar(BusquedaRequest $req) {
    $datos = $req->all();
    return Datatables::of(Usuario::listar($datos))->filterColumn("entidad.nombre", function($q, $k) {
              $q->whereRaw('CONCAT(entidad.nombre, " ", entidad.apellido) like ?', ["%{$k}%"]);
            })->filterColumn("entidad.fechaRegistro", function($q, $k) {
              $q->whereRaw("DATE_FORMAT(entidad.fechaRegistro, '%d/%m/%Y %H:%i:%s') like ?", ["%{$k}%"]);
            })->make(true);
  }

  public function crear() {
    return view("usuario.crear", $this->data);
  }

  public function registrar(FormularioRequest $req) {
    try {
      Usuario::registrar($req);
      Mensajes::agregarMensajeExitoso("Registro exitoso.");
      return redirect(route("usuarios"));
    } catch (\Exception $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("Ocurrió un problema durante el registro de datos. Por favor inténtelo nuevamente.");
      return redirect(route("usuarios.crear"));
    }
  }

  public function editar($id) {
    try {
      if (!(Auth::user()->rol == RolesUsuario::Principal || $id == Auth::user()->idEntidad)) {
        Mensajes::agregarMensajeAdvertencia("No tiene permisos suficientes para ingresar a la sección seleccionada.");
        return redirect()->guest(route("/"));
      }
      $this->data["usuario"] = Usuario::obtenerXId($id);
    } catch (ModelNotFoundException $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("No se encontraron datos del usuario seleccionado. Es posible que haya sido eliminado.");
      return redirect(route("usuarios"));
    }
    return view("usuario.editar", $this->data);
  }

  public function actualizar($id, FormularioRequest $req) {
    try {
      if (!(Auth::user()->rol == RolesUsuario::Principal || $id == Auth::user()->idEntidad)) {
        Mensajes::agregarMensajeAdvertencia("No tiene permisos suficientes para realizar la acción solicitada.");
        return redirect()->guest(route("/"));
      }
      $actualizacionAutorizada = true;
      $datos = $req->all();
      if ($datos["rol"] != RolesUsuario::Principal && Usuario::usuarioUnicoPrincipal($id)) {
        Mensajes::agregarMensajeAdvertencia("El usuario que usted desea modificar es el único 'Usuario principal' y no puede ser modificado a otro tipo diferente.");
        $actualizacionAutorizada = false;
      }
      if ($actualizacionAutorizada && $datos["estado"] == EstadosUsuario::Inactivo && Usuario::usuarioUnicoPrincipal($id)) {
        Mensajes::agregarMensajeAdvertencia("El usuario que usted desea modificar es el único 'Usuario principal' y su cuenta no se puede desactivar.");
        $actualizacionAutorizada = false;
      }
      if ($actualizacionAutorizada) {
        Usuario::actualizar($id, $req);
        Mensajes::agregarMensajeExitoso("Actualización exitosa.");
      }
    } catch (\Exception $e) {
      Log::error($e->getMessage());
      Mensajes::agregarMensajeError("Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente.");
    }
    return redirect(route("usuarios.editar", ["id" => $id]));
  }

  public function actualizarEstado($id, ActualizarEstadoRequest $request) {
    try {
      $datos = $request->all();
      if ($datos["estado"] == EstadosUsuario::Inactivo && Usuario::usuarioUnicoPrincipal($id)) {
        return response()->json(["mensaje" => "El usuario que usted desea modificar es el único 'Usuario principal' y su cuenta no se puede desactivar."], 401);
      }
      Usuario::actualizarEstado($id, $datos["estado"]);
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente."], 400);
    }
    return response()->json(["mensaje" => "Actualización exitosa."], 200);
  }

  public function eliminar($id) {
    try {
      if (Usuario::usuarioUnicoPrincipal($id)) {
        return response()->json(["mensaje" => "El usuario que usted desea eliminar es el único 'Usuario principal' y sus datos no pueden ser borrados."], 401);
      }
      Usuario::eliminar($id);
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "No se pudo eliminar el registro de datos del usuario seleccionado."], 400);
    }
    return response()->json(["mensaje" => "Eliminación exitosa", "id" => $id], 200);
  }

}
