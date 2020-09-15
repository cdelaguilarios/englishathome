<?php

namespace App\Models;

use Log;
use Carbon\Carbon;
use App\Helpers\Enum\TiposEntidad;
use App\Helpers\Enum\RolesUsuario;
use Illuminate\Database\Eloquent\Model;

class Entidad extends Model {

  public $timestamps = false;
  protected $table = "entidad";
  protected $fillable = [
      "nombre",
      "apellido",
      "fechaNacimiento",
      "sexo",
      "telefono",
      "idTipoDocumento",
      "numeroDocumento",
      "correoElectronico",
      "imagenPerfil",
      "direccion",
      "numeroDepartamento",
      "referenciaDireccion",
      "codigoUbigeo",
      "geoLatitud",
      "geoLongitud",
      "comentarioAdministrador"
  ];

  public static function nombreTabla() {
    $modeloEntidad = new Entidad();
    $nombreTabla = $modeloEntidad->getTable();
    unset($modeloEntidad);
    return $nombreTabla;
  }

  public static function listar($tipo, $estado = NULL, $idsExcluir = []) {
    $entidades = Entidad::where("eliminado", 0)->where("tipo", $tipo)->whereNotIn("id", $idsExcluir);
    if (isset($estado) && $estado != "") {
      $entidades->where("estado", $estado);
    }
    return $entidades;
  }

  public static function ObtenerXId($id) {
    return Entidad::where("id", $id)->where("eliminado", 0)->firstOrFail();
  }

  public static function ObtenerIdsAnteriorSiguienteXEntidad($tipo, $entidad) {
    $datos = [];

    $entidadAnterior = Entidad::listar($tipo, $entidad->estado)
                    ->select("id")
                    ->where("id", "<", $entidad->id)
                    ->orderBy("id", "DESC")->first();
    if (isset($entidadAnterior)) {
      $datos["idEntidadAnterior"] = $entidadAnterior->id;
    } else {
      $ultimaEntidad = Entidad::listar($tipo, $entidad->estado)
                      ->select("id")
                      ->orderBy("id", "DESC")->first();
      $datos["idEntidadAnterior"] = (isset($ultimaEntidad) ? $ultimaEntidad->id : NULL);
    }

    $entidadSiguiente = Entidad::listar($tipo, $entidad->estado)
                    ->select("id")
                    ->where("id", ">", $entidad->id)
                    ->orderBy("id", "ASC")->first();
    if (isset($entidadSiguiente)) {
      $datos["idEntidadSiguiente"] = $entidadSiguiente->id;
    } else {
      $primeraEntidad = Entidad::listar($tipo, $entidad->estado)
                      ->select("id")
                      ->orderBy("id", "ASC")->first();
      $datos["idEntidadSiguiente"] = (isset($primeraEntidad) ? $primeraEntidad->id : NULL);
    }

    return $datos;
  }

  public static function buscar($datos) {
    $texto = $datos["texto"];
    $pagina = $datos["pagina"];
    $entidadesXPorPagina = 6;

    $entidades = Entidad::where("eliminado", 0)
            ->where(function ($q) use($texto) {
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
    if (isset($estado)) {
      $entidad->estado = $estado;
    }
    $entidad->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
    unset($datos["imagenPerfil"]);
    $entidad->update($datos);
  }

  public static function actualizarEstado($id, $estado) {
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
      $datosUsuario = ["email" => $datos["email"], "rol" => ($entidad->tipo == TiposEntidad::Alumno ? RolesUsuario::Alumno : RolesUsuario::Profesor)];

      $usuarioRegistrado = Usuario::verificarExistencia($id);
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
    } catch (\Exception $e) {
      Log::error($e);
      return FALSE;
    }
    return TRUE;
  }

}
