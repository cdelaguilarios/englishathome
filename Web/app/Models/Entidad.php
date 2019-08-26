<?php

namespace App\Models;

use Carbon\Carbon;
use App\Helpers\Enum\TiposEntidad;
use App\Helpers\Enum\RolesUsuario;
use Illuminate\Database\Eloquent\Model;

class Entidad extends Model {

  public $timestamps = false;
  protected $table = "entidad";
  protected $fillable = ["nombre", "apellido", "fechaNacimiento", "sexo", "telefono", "idTipoDocumento", "numeroDocumento", "correoElectronico", "imagenPerfil", "direccion", "numeroDepartamento", "referenciaDireccion", "codigoUbigeo", "geoLatitud", "geoLongitud", "comentarioAdministrador"];

  public static function nombreTabla()/* - */ {
    $modeloEntidad = new Entidad();
    $nombreTabla = $modeloEntidad->getTable();
    unset($modeloEntidad);
    return $nombreTabla;
  }

  public static function listar($tipo, $estado = NULL, $idsExcluir = []) {
    $entidades = Entidad::where("eliminado", 0)->whereNotIn("id", $idsExcluir)->where("tipo", $tipo);
    if (isset($estado) && $estado != "") {
      $entidades->where("estado", $estado);
    }
    return $entidades->get();
  }

  public static function ObtenerXId($id)/* - */ {
    return Entidad::where("eliminado", 0)->where("id", $id)->firstOrFail();
  }

  public static function buscar($datos) {
    $texto = $datos["texto"];
    $pagina = $datos["pagina"];
    $entidadesXPorPagina = 6;
    $entidades = Entidad::where("eliminado", 0)->where(function ($q) use($texto) {
      $q->where("nombre", 'like', "%" . $texto . "%")
              ->orWhere("apellido", 'like', "%" . $texto . "%")
              ->orWhere("correoElectronico", 'like', "%" . $texto . "%");
    });
    $total = $entidades->count();
    return ["incomplete_results" => TRUE, "entidades" => $entidades->skip(($pagina - 1) * $entidadesXPorPagina)->take($entidadesXPorPagina)->get(), "total" => $total];
  }

  public static function registrar($datos, $tipo, $estado) {
    $entidad = new Entidad($datos);
    $entidad->tipo = $tipo;
    $entidad->estado = $estado;
    $entidad->fechaRegistro = Carbon::now()->toDateTimeString();
    $entidad->save();
    return $entidad->id;
  }

  public static function actualizar($id, $datos, $tipo, $estado) {
    $entidad = Entidad::ObtenerXId($id);
    $entidad->tipo = $tipo;
    if (isset($estado))
      $entidad->estado = $estado;
    $entidad->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
    unset($datos["imagenPerfil"]);
    $entidad->update($datos);
  }

  public static function actualizarEstado($id, $estado)/* - */ {
    $entidad = Entidad::ObtenerXId($id);
    $entidad->estado = $estado;
    $entidad->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
    $entidad->save();
  }

  public static function registrarActualizarImagenPerfil($id, $imagenPerfil) {
    if (isset($imagenPerfil) && !is_null($imagenPerfil)) {
      $entidad = Entidad::ObtenerXId($id);
      $nuevaImagenEntidad = Archivo::registrar($entidad->id . "_ip_", $imagenPerfil, TRUE);
      if (isset($nuevaImagenEntidad)) {
        if (isset($entidad->imagenPerfil) && $entidad->imagenPerfil != "") {
          Archivo::eliminar($entidad->imagenPerfil);
        }
        $entidad->imagenPerfil = $nuevaImagenEntidad;
        $entidad->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
        $entidad->save();
      }
    }
  }

  public static function actualizarComentariosAdministrador($id, $datos) {
    $entidad = Entidad::ObtenerXId($id);
    $entidad->comentarioAdministrador = $datos["comentarioAdministrador"];
    $entidad->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
    $entidad->save();
  }

  public static function actualizarCredencialesAcceso($id, $datos) {
    $entidad = Entidad::ObtenerXId($id);
    $entidad->correoElectronico = $datos["email"];
    $entidad->update();

    //Credenciales de acceso
    if (isset($datos["password"]) && trim($datos["password"]) !== "") {
      $usuarioRegistrado = Usuario::verificarExistencia($id);
      $datosUsuario = ["email" => $datos["email"], "rol" => ($entidad->tipo == TiposEntidad::Alumno ? RolesUsuario::Alumno : RolesUsuario::Profesor)];
      if ($usuarioRegistrado) {
        $usuario = Usuario::obtenerXId($id);
        $usuario->password = bcrypt($datos["password"]);
        $usuario->update($datosUsuario);
      } else {
        $usuario = new Usuario($datosUsuario);
        $usuario->idEntidad = $id;
        $usuario->password = bcrypt($datos["password"]);
        $usuario->save();
      }
    }

    //Código de verificación de clases
    if ($entidad->tipo == TiposEntidad::Alumno && isset($datos["codigoVerificacionClases"]) && trim($datos["codigoVerificacionClases"]) !== "") {
      $alumno = Alumno::obtenerXId($id, TRUE);
      $alumno->codigoVerificacionClases = $datos["codigoVerificacionClases"];
      $alumno->update();
    }
  }

  public static function eliminar($id) {
    $entidad = Entidad::ObtenerXId($id);
    $entidad->eliminado = 1;
    $entidad->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
    $entidad->save();
  }

  public static function verificarExistencia($id) {
    try {
      Entidad::obtenerXId($id);
    } catch (\Exception $ex) {
      return FALSE;
    }
    return TRUE;
  }

  //REPORTE
  public static function listarCampos() {
    return [
        "nombre" => ["titulo" => "Nombres"],
        "apellido" => ["titulo" => "Apellidos"],
        "fechaNacimiento" => ["titulo" => "Fecha de nacimiento"],
        "sexo" => ["titulo" => "Sexo", "tipo" => "sexo"],
        "telefono" => ["titulo" => "Teléfono"],
        "idTipoDocumento" => ["titulo" => "Tipo de documento", "tipo" => "tipoDocumento"],
        "numeroDocumento" => ["titulo" => "Número de documento"],
        "correoElectronico" => ["titulo" => "Correo electrónico"],
        "imagenPerfil" => ["titulo" => "Imagen de perfil"],
        "direccion" => ["titulo" => "Dirección"],
        "numeroDepartamento" => ["titulo" => "Número de departamento"],
        "referenciaDireccion" => ["titulo" => "Dirección-referencia"],
        "codigoUbigeo" => ["titulo" => "Código ubigeo"],
        "geoLatitud" => ["titulo" => "Geo - Latitud"],
        "geoLongitud" => ["titulo" => "Geo - Longitud"],
        "comentarioAdministrador" => ["titulo" => "Comentarios del administrador"]
    ];
  }

}
