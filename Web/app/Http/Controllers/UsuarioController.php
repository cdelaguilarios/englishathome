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
use App\Http\Requests\UsuarioRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UsuarioController extends Controller {

  protected $data = array();

  public function __construct() {
    $this->data["seccion"] = "usuarios";
    $this->data["roles"] = RolesUsuario::listar();
    $this->data["estados"] = EstadosUsuario::listar(TRUE);
  }

  public function index() {
    return view("usuario.lista", $this->data);
  }

  public function listar() {
    return Datatables::of(Usuario::Listar())->make(TRUE);
  }

  public function create() {
    return view("usuario.crear", $this->data);
  }

  public function store(UsuarioRequest $req) {
    try {
      $idUsuario = Usuario::Registrar($req);
      Mensajes::agregarMensajeExitoso("Registro exitoso.");
      return redirect(route("usuarios.editar", ["id" => $idUsuario]));
    } catch (\Exception $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("Ocurrió un problema durante el registro de datos. Por favor inténtelo nuevamente.");
      return redirect(route("usuarios.nuevo"));
    }
  }

  public function edit($id) {
    try {
      if (!(Auth::user()->rol == RolesUsuario::Principal || $id == Auth::user()->idEntidad)) {
        Mensajes::agregarMensajeAdvertencia("No tiene permisos suficientes para ingresar a la sección seleccionada.");
        return redirect()->guest(route("/"));
      }
      $this->data["usuario"] = Usuario::ObtenerXId($id);
    } catch (ModelNotFoundException $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("No se encontraron datos del usuario seleccionado.");
      return redirect(route("usuarios"));
    }
    return view("usuario.editar", $this->data);
  }

  public function update($id, UsuarioRequest $req) {
    try {
      if (!(Auth::user()->rol == RolesUsuario::Principal || $id == Auth::user()->idEntidad)) {
        Mensajes::agregarMensajeAdvertencia("No tiene permisos suficientes para realizar la acción solicitada.");
        return redirect()->guest(route("/"));
      }

      $edicionAutorizada = true;
      $datos = $req->all();
      if ($datos["rol"] != RolesUsuario::Principal && Usuario::UsuarioUnicoPrincipal($id)) {
        Mensajes::agregarMensajeAdvertencia("El usuario que usted desea modificar es el único 'Usuario principal' y no puede ser modificado a otro tipo diferente.");
        $edicionAutorizada = false;
      }
      if ($datos["estado"] == EstadosUsuario::Inactivo && Usuario::UsuarioUnicoPrincipal($id)) {
        Mensajes::agregarMensajeAdvertencia("El usuario que usted desea modificar es el único 'Usuario principal' y su cuenta no se puede desactivar.");
        $edicionAutorizada = false;
      }
      if ($edicionAutorizada) {
        Usuario::Actualizar($id, $req);
        Mensajes::agregarMensajeExitoso("Actualización exitosa.");
      }
    } catch (\Exception $e) {
      Log::error($e->getMessage());
      Mensajes::agregarMensajeError("Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente.");
    }
    return redirect(route("usuarios.editar", ["id" => $id]));
  }

  public function destroy($id) {
    try {
      if (Usuario::UsuarioUnicoPrincipal($id)) {
        return response()->json(["mensaje" => "El usuario que usted desea eliminar es el único 'Usuario principal' y sus datos no pueden ser borrados."], 400);
      }
      Usuario::Eliminar($id);
    } catch (ModelNotFoundException $e) {
      Log::error($e);
      return response()->json(["mensaje" => "No se pudo eliminar el registro de datos del usuario seleccionado."], 400);
    }
    return response()->json(["mensaje" => "Eliminación exitosa", "id" => $id], 200);
  }

}
