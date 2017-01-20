<?php

namespace App\Models;

use Auth;
use App\Helpers\Enum\TiposEntidad;
use App\Helpers\Enum\RolesUsuario;
use App\Helpers\Enum\EstadosUsuario;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class Usuario extends Model implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract {

  use Authenticatable,
      Authorizable,
      CanResetPassword;

  public $timestamps = FALSE;
  protected $table = "usuario";
  protected $primaryKey = "idEntidad";
  protected $fillable = ["email", "rol"];
  protected $hidden = ["password", "remember_token"];

  public static function nombreTabla() {
    $modeloUsuario = new Usuario();
    $nombreTabla = $modeloUsuario->getTable();
    unset($modeloUsuario);
    return $nombreTabla;
  }

  public static function listar($datos = NULL) {
    $nombreTabla = Usuario::nombreTabla();
    $usuarios = Usuario::select($nombreTabla . ".*", "entidad.*")
            ->leftJoin(Entidad::nombreTabla() . " as entidad", $nombreTabla . ".idEntidad", "=", "entidad.id")
            ->where("entidad.eliminado", 0);
    if (isset($datos["estado"])) {
      $usuarios->where("entidad.estado", $datos["estado"]);
    }
    return $usuarios;
  }

  public static function obtenerXId($id) {
    return Usuario::listar()->where("entidad.id", $id)->firstOrFail();
  }

  public static function registrar($req) {
    $datos = $req->all();
    $datos["correoElectronico"] = $datos["email"];

    $idEntidad = Entidad::registrar($datos, TiposEntidad::Usuario, ((isset($datos["estado"])) ? $datos["estado"] : EstadosUsuario::Activo));
    Entidad::registrarActualizarImagenPerfil($idEntidad, $req->file("imagenPerfil"));

    $usuario = new Usuario($datos);
    $usuario->password = bcrypt($datos["password"]);
    $usuario->idEntidad = $idEntidad;
    $usuario->save();
    return $idEntidad;
  }

  public static function actualizar($id, $req) {
    $datos = $req->all();
    $datos["correoElectronico"] = $datos["email"];

    Entidad::Actualizar($id, $datos, TiposEntidad::Usuario, $datos["estado"]);
    Entidad::registrarActualizarImagenPerfil($id, $req->file("imagenPerfil"));

    $usuario = Usuario::obtenerXId($id);
    if (isset($datos["password"]) && !is_null($datos["password"]) && $datos["password"] != "") {
      $usuario->password = bcrypt($datos["password"]);
    }
    $rol = Auth::user()->rol;
    if ($rol != RolesUsuario::Principal) {
      $datos["rol"] = $usuario->rol;
    }
    $usuario->update($datos);
  }

  public static function actualizarEstado($id, $estado) {
    Usuario::obtenerXId($id);
    Entidad::actualizarEstado($id, $estado);
  }

  public static function usuarioUnicoPrincipal($id) {
    $datosUsuario = Usuario::where("rol", RolesUsuario::Principal)->count();
    if ($datosUsuario > 1) {
      return FALSE;
    }
    $usuario = Usuario::obtenerXId($id);
    return ($usuario->rol == RolesUsuario::Principal);
  }

  public static function usuarioEliminado($id) {
    $entidad = Entidad::ObtenerXId($id);
    return ($entidad->eliminado == 0);
  }

  public static function usuarioActivo($id) {
    $entidad = Entidad::ObtenerXId($id);
    return ($entidad->estado == EstadosUsuario::Activo);
  }

  public static function eliminar($id) {
    $usuario = Usuario::obtenerXId($id);
    Entidad::eliminar($id);

    $correoElectronicoEliminacion = mb_substr($usuario->email . "_e_" . rand(1, 9999999), 0, 255);
    while (Usuario::where("email", $correoElectronicoEliminacion)->count() > 0 || Entidad::where("correoElectronico", $correoElectronicoEliminacion)->count() > 0) {
      $correoElectronicoEliminacion = mb_substr($usuario->email . "_e_" . rand(1, 9999999), 0, 255);
    }
    $usuario->email = $correoElectronicoEliminacion;
    $usuario->save();
  }

}
