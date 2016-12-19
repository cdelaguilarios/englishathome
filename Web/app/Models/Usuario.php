<?php

namespace App\Models;

use Auth;
use Carbon\Carbon;
use App\Helpers\Util;
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

  public static function NombreTabla() {
    $modeloUsuario = new Usuario();
    $nombreTabla = $modeloUsuario->getTable();
    unset($modeloUsuario);
    return $nombreTabla;
  }

  protected static function Listar() {
    $nombreTabla = Usuario::NombreTabla();
    return Usuario::select($nombreTabla . ".*", "entidad.*")
                    ->leftJoin(Entidad::nombreTabla() . " as entidad", $nombreTabla . ".idEntidad", "=", "entidad.id")
                    ->where("entidad.eliminado", 0);
  }

  protected static function ObtenerXId($id) {
    $nombreTabla = Usuario::NombreTabla();
    return Usuario::select($nombreTabla . ".*", "entidad.*")
                    ->leftJoin(Entidad::nombreTabla() . " as entidad", $nombreTabla . ".idEntidad", "=", "entidad.id")
                    ->where("entidad.id", $id)
                    ->where("entidad.eliminado", 0)->firstOrFail();
  }

  protected static function Registrar($req) {
    $datos = $req->all();
    $datos["correoElectronico"] = $datos["email"];

    $entidad = new Entidad($datos);
    $entidad->tipo = TiposEntidad::Usuario;
    $entidad->save();

    $usuario = new Usuario($datos);
    $usuario->password = bcrypt($datos["password"]);
    $usuario->idEntidad = $entidad["id"];
    $usuario->save();

    $imagenPerfil = $req->file("imagenPerfil");
    if (isset($imagenPerfil) && !is_null($imagenPerfil) && $imagenPerfil != "") {
      $entidad->rutaImagenPerfil = Util::GuardarImagen($entidad["id"] . "_iu_", $imagenPerfil);
      $entidad->save();
    }
    return $entidad["id"];
  }

  protected static function Actualizar($id, $req) {
    $datos = $req->all();
    $datos["correoElectronico"] = $datos["email"];

    $entidad = Entidad::ObtenerXId($id);
    $usuario = Usuario::ObtenerXId($id);

    $imagenPerfil = $req->file("imagenPerfil");
    if (isset($imagenPerfil) && !is_null($imagenPerfil) && $imagenPerfil != "") {
      $entidad->rutaImagenPerfil = Util::GuardarImagen($id . "_iu_", $imagenPerfil);
    }


    if (isset($datos["password"]) && !is_null($datos["password"]) && $datos["password"] != "") {
      $usuario->password = bcrypt($datos["password"]);
    }
    $rol = Auth::user()->rol;
    if ($rol != RolesUsuario::Principal) {
      $datos["rol"] = $usuario->rol;
    }

    $entidad->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
    $entidad->update($datos);
    $usuario->update($datos);
  }

  protected static function Eliminar($id) {
    $entidad = Entidad::ObtenerXId($id);
    $usuario = Usuario::ObtenerXId($id);

    $correoElectronicoEliminacion = mb_substr($usuario->email . "_e_" . rand(1, 9999999), 0, 255);
    while (Usuario::where("email", $correoElectronicoEliminacion)->count() > 0 || Entidad::where("correoElectronico", $correoElectronicoEliminacion)->count() > 0) {
      $correoElectronicoEliminacion = mb_substr($usuario->email . "_e_" . rand(1, 9999999), 0, 255);
    }

    $entidad->correoElectronico = $correoElectronicoEliminacion;
    $entidad->eliminado = 1;
    $entidad->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
    $entidad->save();

    $usuario->email = $correoElectronicoEliminacion;
    $usuario->save();
  }

  protected static function UsuarioUnicoPrincipal($id) {
    $datosUsuario = Usuario::where("rol", RolesUsuario::Principal)->count();
    if ($datosUsuario > 1) {
      return FALSE;
    }
    $usuario = Usuario::ObtenerXId($id);
    return ($usuario->rol == RolesUsuario::Principal);
  }

  protected static function UsuarioEliminado($id) {
    $entidad = Entidad::ObtenerXId($id);
    return ($entidad->eliminado == 0);
  }

  protected static function UsuarioActivo($id) {
    $entidad = Entidad::ObtenerXId($id);
    return ($entidad->estado == EstadosUsuario::Activo);
  }

}
