<?php

namespace App\Models;

use DB;
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

  public static function listar($datos = NULL, $soloUsuariosDelSistema = TRUE) {
    $usuarios = Usuario::leftJoin(Entidad::nombreTabla() . " as entidad", Usuario::nombreTabla() . ".idEntidad", "=", "entidad.id")->where("entidad.eliminado", 0)->groupBy("entidad.id")->distinct();
    if (isset($datos["estado"])) {
      $usuarios->where("entidad.estado", $datos["estado"]);
    }
    if($soloUsuariosDelSistema){
      $usuarios->whereIn("rol", array_keys(RolesUsuario::listarDelSistema()));
    }
    return $usuarios;
  }

  public static function listarBusqueda($terminoBus = NULL) {
    $alumnos = Usuario::listar()->select("entidad.id", DB::raw('CONCAT(entidad.nombre, " ", entidad.apellido) AS nombreCompleto'));
    if (isset($terminoBus)) {
      $alumnos->whereRaw('CONCAT(entidad.nombre, " ", entidad.apellido) like ?', ["%{$terminoBus}%"]);
    }
    return $alumnos->lists("nombreCompleto", "entidad.id");
  }

  public static function obtenerXId($id, $soloUsuariosDelSistema = TRUE) {
    return Usuario::listar(NULL, $soloUsuariosDelSistema)->where("entidad.id", $id)->firstOrFail();
  }

  public static function obtenerActual() {
    if (is_null(session("usuarioActual"))) {
      session(["usuarioActual" => Usuario::obtenerXId(Auth::user()->idEntidad, FALSE)]);
    }
    return session("usuarioActual");
  }

  public static function registrar($req) {
    $datos = $req->all();
    $datos["correoElectronico"] = $datos["email"];

    $idEntidad = Entidad::registrar($datos, TiposEntidad::Usuario, $datos["estado"]);
    Entidad::registrarActualizarImagenPerfil($idEntidad, $req->file("imagenPerfil"));

    $usuario = new Usuario($datos);
    $usuario->idEntidad = $idEntidad;
    $usuario->password = bcrypt($datos["password"]);
    $usuario->save();
    return $idEntidad;
  }

  public static function actualizar($id, $req) {
    $datos = $req->all();
    $datos["correoElectronico"] = $datos["email"];

    Entidad::actualizar($id, $datos, TiposEntidad::Usuario, $datos["estado"]);
    Entidad::registrarActualizarImagenPerfil($id, $req->file("imagenPerfil"));

    $usuario = Usuario::obtenerXId($id);
    if (isset($datos["password"]) && $datos["password"] != "") {
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
    $datosUsuario = Usuario::listar()->where(Usuario::nombreTabla() . ".rol", RolesUsuario::Principal)->count();
    if ($datosUsuario == 1) {
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

  public static function verificarExistencia($id) {
    try {
      Usuario::obtenerXId($id, FALSE);
    } catch (\Exception $ex) {
      return FALSE;
    }
    return TRUE;
  }

}
