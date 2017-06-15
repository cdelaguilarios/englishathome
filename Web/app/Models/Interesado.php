<?php

namespace App\Models;

use DB;
use Mail;
use Auth;
use Crypt;
use Config;
use Storage;
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
  protected $fillable = ["consulta", "cursoInteres", "costoHoraClase", "comentarioAdicional"];

  public static function nombreTabla() {
    $modeloInteresado = new Interesado();
    $nombreTabla = $modeloInteresado->getTable();
    unset($modeloInteresado);
    return $nombreTabla;
  }

  public static function listar($datos = NULL) {
    $interesados = Interesado::leftJoin(Entidad::nombreTabla() . " as entidad", Interesado::nombreTabla() . ".idEntidad", "=", "entidad.id")->where("entidad.eliminado", 0)->groupBy("entidad.id")->distinct();
    if (isset($datos["estado"])) {
      $interesados->where("entidad.estado", $datos["estado"]);
    }
    return $interesados;
  }

  public static function listarBusqueda() {
    return Interesado::listar()->select("entidad.id", DB::raw('CONCAT(entidad.nombre, " ", entidad.apellido) AS nombreCompleto'))->lists("nombreCompleto", "entidad.id");
  }

  public static function listarCursosInteres() {
    return Interesado::listar()->select(DB::raw("(CASE WHEN " . Interesado::nombreTabla() . ".cursoInteres <> '' THEN " . Interesado::nombreTabla() . ".cursoInteres ELSE 'Otros' END) AS 'cursoInteres'"))->groupBy(Interesado::nombreTabla() . ".cursoInteres")->lists("cursoInteres", "cursoInteres");
  }

  public static function obtenerXId($id, $simple = FALSE) {
    $interesado = Interesado::listar()->where("entidad.id", $id)->firstOrFail();
    if (!$simple) {
      $entidadCurso = EntidadCurso::obtenerXEntidad($id);
      $interesado->idCurso = (!is_null($entidadCurso) ? $entidadCurso->idCurso : NULL);
      $idInteresadoAnterior = Interesado::listar()->select("entidad.id")->where("entidad.id", "<", $id)->where("entidad.estado", $interesado->estado)->orderBy("entidad.id", "DESC")->first();
      $idInteresadoSiguiente = Interesado::listar()->select("entidad.id")->where("entidad.id", ">", $id)->where("entidad.estado", $interesado->estado)->first();
      $interesado->idInteresadoAnterior = (isset($idInteresadoAnterior) ? $idInteresadoAnterior->id : NULL);
      $interesado->idInteresadoSiguiente = (isset($idInteresadoSiguiente) ? $idInteresadoSiguiente->id : NULL);
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
    $interesado = Interesado::obtenerXId($id, TRUE);
    $interesado->costoHoraClase = $datos["costoHoraClase"];
    $interesado->save();

    $curso = Curso::obtenerXId($datos["idCurso"]);
    $datos["titulo"] = $curso->nombre;
    $datos["curso"] = $curso->nombre;
    $datos["urlInscripcion"] = route("alumnos.crear.externo", ["codigoVerificacion" => Crypt::encrypt($entidad->id)]);
    $correo = (isset($datos["correoCotizacionPrueba"]) ? $datos["correoCotizacionPrueba"] : $entidad->correoElectronico);
    $nombreDestinatario = (isset($datos["correoCotizacionPrueba"]) ? "" : $entidad->nombre . " " . $entidad->apellido);
    $nombresArchivosAdjuntos = $datos["nombresArchivosAdjuntos"];
    $nombresOriginalesArchivosAdjuntos = $datos["nombresOriginalesArchivosAdjuntos"];
    $esPrueba = (isset($datos["correoCotizacionPrueba"]));

    Config::set("eah.correoNotificaciones", VariableSistema::obtenerXLlave("correo"));
    Config::set("mail.username", VariableSistema::obtenerXLlave("correo"));
    Config::set("mail.password", VariableSistema::obtenerXLlave("contrasenaCorreo"));
    Mail::send("interesado.plantillaCorreo.cotizacion" . ($datos["cuentaBancoEmpresarial"] ? "Empresarial" : ""), $datos, function ($m) use ($correo, $nombreDestinatario, $nombresArchivosAdjuntos, $nombresOriginalesArchivosAdjuntos) {
      $m->to($correo, $nombreDestinatario)->bcc("cdelaguilarios@gmail.com")->subject("English at home - Cotización");
      if (!is_null($nombresArchivosAdjuntos) && !is_null($nombresOriginalesArchivosAdjuntos)) {
        $rutaBaseAlmacenamiento = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
        $nombresArchivosAdjuntosSel = explode(",", $nombresArchivosAdjuntos);
        $nombresOriginalesArchivosAdjuntosSel = explode(",", $nombresOriginalesArchivosAdjuntos);
        for ($i = 0; $i < count($nombresArchivosAdjuntosSel); $i++) {
          if (trim($nombresArchivosAdjuntosSel[$i]) == "") {
            continue;
          }
          $m->attach($rutaBaseAlmacenamiento . "/" . $nombresArchivosAdjuntosSel[$i], ['as' => $nombresOriginalesArchivosAdjuntosSel[$i]]);
        }
      }
    });

    if (!$esPrueba) {
      if (!is_null($nombresArchivosAdjuntos)) {
        $nombresArchivosAdjuntosSel = explode(",", $nombresArchivosAdjuntos);
        for ($i = 0; $i < count($nombresArchivosAdjuntosSel); $i++) {
          if (trim($nombresArchivosAdjuntosSel[$i]) == "") {
            continue;
          }
          Archivo::eliminar($nombresArchivosAdjuntosSel[$i]);
        }
      }
      Interesado::actualizarEstado($id, EstadosInteresado::CotizacionEnviada);
      Historial::registrar([
          "idEntidades" => [$id, Auth::user()->idEntidad],
          "titulo" => (MensajesHistorial::TituloInteresadoEnvioCorreoCotizacion),
          "mensaje" => ""
      ]);
    }
  }

  public static function obtenerIdAlumno($id) {
    Interesado::obtenerXId($id, TRUE);
    $relacionEntidad = RelacionEntidad::obtenerXIdEntidadB($id);
    return ((count($relacionEntidad) > 0) ? $relacionEntidad[0]->idEntidadA : 0);
  }

  public static function registrarAlumno($id, $idAlumno = NULL) {
    $datos = Interesado::obtenerXId($id, TRUE)->toArray();
    $idAlumnoRel = Interesado::obtenerIdAlumno($id);
    if (!($datos["estado"] != EstadosInteresado::AlumnoRegistrado && $idAlumnoRel == 0)) {
      return ($idAlumnoRel != 0 ? $idAlumnoRel : NULL);
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
      $entidadCurso = EntidadCurso::obtenerXEntidad($id);
      if (!is_null($entidadCurso)) {
        EntidadCurso::registrarActualizar($idEntidad, $entidadCurso->idCurso);
      }
      $alumno = new Alumno($datos);
      $alumno->idEntidad = $idEntidad;
      $alumno->save();
      $idAlumno = $idEntidad;

      Historial::registrar([
          "idEntidades" => [$idAlumno, (Auth::guest() ? NULL : Auth::user()->idEntidad)],
          "titulo" => (Auth::guest() ? MensajesHistorial::TituloAlumnoRegistro : MensajesHistorial::TituloAlumnoRegistroXUsuario),
          "enviarCorreo" => (Auth::guest() ? 1 : 0),
          "mensaje" => ""
      ]);
    }
    RelacionEntidad::registrar($idAlumno, $id, TiposRelacionEntidad::AlumnoInteresado);
    Interesado::actualizarEstado($id, EstadosInteresado::AlumnoRegistrado);
    Historial::registrar([
        "idEntidades" => [$id, (Auth::guest() ? NULL : Auth::user()->idEntidad)],
        "titulo" => (Auth::guest() ? MensajesHistorial::TituloInteresadoRegistroAlumno : MensajesHistorial::TituloInteresadoRegistroAlumnoXUsuario),
        "mensaje" => ""
    ]);
    return $idAlumno;
  }

  public static function eliminar($id) {
    Interesado::obtenerXId($id, TRUE);
    Entidad::eliminar($id);
  }

}
