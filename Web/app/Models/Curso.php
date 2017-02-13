<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curso extends Model {

  public $timestamps = false;
  protected $table = "curso";

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

  public static function listarSimple() {
    return Curso::where("eliminado", 0)->lists("nombre", "id");
  }

  public static function obtenerXId($id) {
    return Curso::where("eliminado", 0)->where("id", $id)->firstOrFail();
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

    Entidad::actualizar($id, $datos, TiposEntidad::Usuario, $datos["estado"]);
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
