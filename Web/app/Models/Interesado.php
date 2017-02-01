<?php

namespace App\Models;

use DB;
use Mail;
use Auth;
use Crypt;
use App\Helpers\Enum\TiposEntidad;
use App\Helpers\Enum\EstadosAlumno;
use App\Helpers\Enum\MensajesHistorial;
use App\Helpers\Enum\EstadosInteresado;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Enum\TiposRelacionEntidad;

class Interesado extends Model {

  public $timestamps = false;
  protected $primaryKey = "idEntidad";
  protected $table = "interesado";
  protected $fillable = ["consulta", "cursoInteres", "codigoVerificacion", "costoHoraClase", "comentarioAdicional"];

  public static function nombreTabla() {
    $modeloInteresado = new Interesado();
    $nombreTabla = $modeloInteresado->getTable();
    unset($modeloInteresado);
    return $nombreTabla;
  }

  public static function listar($datos = NULL) {
    $nombreTabla = Interesado::nombreTabla();
    $interesados = Interesado::leftJoin(Entidad::nombreTabla() . " as entidad", $nombreTabla . ".idEntidad", "=", "entidad.id")->where("entidad.eliminado", 0);
    if (isset($datos["estado"])) {
      $interesados->where("entidad.estado", $datos["estado"]);
    }
    return $interesados;
  }

  public static function listarBusqueda() {
    return Interesado::listar()->select("entidad.id", DB::raw('CONCAT(entidad.nombre, " ", entidad.apellido) AS nombreCompleto'))->lists("nombreCompleto", "entidad.id");
  }

  public static function obtenerXId($id, $simple = FALSE) {
    $interesado = Interesado::listar()->where("entidad.id", $id)->firstOrFail();
    if (!$simple) {
      $entidadCurso = EntidadCurso::listar($id)->get();
      $interesado->idCurso = ((count($entidadCurso) > 0) ? $entidadCurso[0]->idCurso : NULL);
    }
    return $interesado;
  }

  public static function registrar($datos) {
    $idEntidad = Entidad::registrar($datos, TiposEntidad::Interesado, ((isset($datos["estado"])) ? $datos["estado"] : EstadosInteresado::PendienteInformacion));
    EntidadCurso::registrarActualizar($idEntidad, $datos["idCurso"]);

    $interesado = new Interesado($datos);
    $interesado->idEntidad = $idEntidad;
    $interesado->save();
    
    Historial::registrar([
        "idEntidades" => [$idEntidad, (Auth::guest() ? NULL : Auth::user()->idEntidad)],
        "titulo" => (Auth::guest() ? MensajesHistorial::TituloInteresadoRegistro : MensajesHistorial::TituloInteresadoRegistroXUsuario),
        "mensaje" => ""
    ]);
    return $idEntidad;
  }

  public static function actualizar($id, $datos) {
    Entidad::actualizar($id, $datos, TiposEntidad::Interesado, $datos["estado"]);
    EntidadCurso::registrarActualizar($id, $datos["idCurso"]);

    $interesado = Interesado::obtenerXId($id, TRUE);
    $interesado->update($datos);
  }

  public static function actualizarEstado($id, $estado) {
    Interesado::obtenerXId($id, TRUE);
    Entidad::actualizarEstado($id, $estado);
  }

  public static function enviarCotizacion($id, $datos) {
    $entidad = Entidad::ObtenerXId($id);
    if (!isset($datos["correoCotizacionPrueba"])) {
      Interesado::actualizarEstado($id, EstadosInteresado::CotizacionEnviada);
    }

    $interesado = Interesado::obtenerXId($id, TRUE);
    $interesado->costoHoraClase = $datos["costoHoraClase"];
    $interesado->save();

    $curso = Curso::obtenerXId($datos["idCurso"]);
    $datos["titulo"] = $curso->nombre;
    $datos["curso"] = $curso->nombre;
    $datos["urlInscripcion"] = route("alumnos.crear.externo", ["codigoVerificacion" => Crypt::encrypt($entidad->id)]);
    $correo = (isset($datos["correoCotizacionPrueba"]) ? $datos["correoCotizacionPrueba"] : $entidad->correoElectronico);
    $nombreDestinatario = (isset($datos["correoCotizacionPrueba"]) ? "" : $entidad->nombre . " " . $entidad->apellido);

    Mail::send("interesado.plantillaCorreo.cotizacion", $datos, function ($m) use ($correo, $nombreDestinatario) {
      $m->to($correo, $nombreDestinatario)->subject("English at home - Cotización");
    });
  }

  public static function esAlumnoRegistrado($id) {
    Interesado::obtenerXId($id, TRUE);
    $relacionEntidad = RelacionEntidad::obtenerXIdEntidadB($id);
    return (count($relacionEntidad) > 0);
  }

  public static function registrarAlumno($id, $idAlumno = NULL) {
    $datos = Interesado::obtenerXId($id, TRUE)->toArray();
    if (!($datos["estado"] != EstadosInteresado::AlumnoRegistrado && !Interesado::esAlumnoRegistrado($id))) {
      return;
    }
    if (is_null($idAlumno)) {
      $idEntidad = Entidad::registrar($datos, TiposEntidad::Alumno, EstadosAlumno::PorConfirmar);
      $datos += [
          "conComputadora" => 0,
          "conInternet" => 0,
          "conPlumonPizarra" => 0,
          "conAmbienteClase" => 0,
          "numeroHorasClase" => 2
      ];
      $entidadCurso = EntidadCurso::listar($id)->get();
      if (count($entidadCurso) > 0) {
        EntidadCurso::registrarActualizar($idEntidad, $entidadCurso[0]->idCurso);
      }
      $alumno = new Alumno($datos);
      $alumno->idEntidad = $idEntidad;
      $alumno->save();
      $idAlumno = $idEntidad;
    }
    RelacionEntidad::registrar($idAlumno, $id, TiposRelacionEntidad::AlumnoInteresado);
    Interesado::actualizarEstado($id, EstadosInteresado::AlumnoRegistrado);
  }

  public static function eliminar($id) {
    Interesado::obtenerXId($id, TRUE);
    Entidad::eliminar($id);
  }

}
