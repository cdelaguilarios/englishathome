<?php

namespace App\Models;

use DB;
use Log;
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

class Interesado extends Model/* |-| */ {

  public $timestamps = false;
  protected $primaryKey = "idEntidad";
  protected $table = "interesado";
  protected $fillable = [
      "consulta",
      "cursoInteres",
      "costoXHoraClase",
      "comentarioAdicional",
      "origen"
  ];

  public static function nombreTabla()/* - */ {
    $modeloInteresado = new Interesado();
    $nombreTabla = $modeloInteresado->getTable();
    unset($modeloInteresado);
    return $nombreTabla;
  }

  public static function listar($datos = NULL)/* - */ {
    $nombreTablaInteresado = Interesado::nombreTabla();
    $interesados = Interesado::
            select(DB::raw($nombreTablaInteresado . ".*, 
                           entidad.*, 
                           GROUP_CONCAT(curso.nombre SEPARATOR ', ') as curso"))
            ->leftJoin(Entidad::nombreTabla() . " as entidad", $nombreTablaInteresado . ".idEntidad", "=", "entidad.id")
            ->leftJoin(EntidadCurso::nombreTabla() . " as entidadCurso", function ($q) {
              $q->on("entidadCurso.idEntidad", "=", "entidad.id");
            })
            ->leftJoin(Curso::nombreTabla() . " as curso", function ($q) {
              $q->on("curso.id", "=", "entidadCurso.idCurso");
            })
            ->where("entidad.eliminado", 0)
            ->whereIn("entidad.estado", array_keys(EstadosInteresado::listar()))
            ->groupBy("entidad.id")
            ->distinct();

    if (isset($datos["estado"])) {
      $interesados->where("entidad.estado", $datos["estado"]);
    }
    return $interesados;
  }

  public static function listarBusqueda($terminoBus = NULL)/* - */ {
    $interesados = Interesado::listar()->select(DB::raw("entidad.id, CONCAT(entidad.nombre, ' ', entidad.apellido) AS nombreCompleto"));
    if (isset($terminoBus)) {
      $interesados->whereRaw('CONCAT(entidad.nombre, " ", entidad.apellido) like ?', ["%{$terminoBus}%"]);
    }
    return $interesados->lists("nombreCompleto", "entidad.id");
  }

  public static function listarCursosInteres()/* - */ {
    $nombreTabla = Interesado::nombreTabla();
    return Interesado::listar()
                    ->select(DB::raw("(CASE WHEN " . $nombreTabla . ".cursoInteres <> '' THEN " . $nombreTabla . ".cursoInteres ELSE 'Otros' END) AS 'cursoInteres'"))
                    ->groupBy($nombreTabla . ".cursoInteres")
                    ->lists("cursoInteres", "cursoInteres");
  }

  public static function obtenerXId($id, $simple = FALSE)/* - */ {
    $interesado = Interesado::listar()->where("entidad.id", $id)->firstOrFail();

    if (!$simple) {
      $entidadCurso = EntidadCurso::obtenerXIdEntidad($id);
      $interesado->idCurso = (!is_null($entidadCurso) ? $entidadCurso->idCurso : NULL);

      $datosIdsAntSig = Entidad::ObtenerIdsAnteriorSiguienteXEntidad(TiposEntidad::Interesado, $interesado);
      $interesado->idInteresadoAnterior = $datosIdsAntSig["idEntidadAnterior"];
      $interesado->idInteresadoSiguiente = $datosIdsAntSig["idEntidadSiguiente"];
    }
    return $interesado;
  }

  public static function registrar($datos)/* - */ {
    $estado = (isset($datos["estado"]) ? $datos["estado"] : EstadosInteresado::PendienteInformacion);
    $idEntidad = Entidad::registrar($datos, TiposEntidad::Interesado, $estado);
    EntidadCurso::registrarActualizar($idEntidad, $datos["idCurso"]);

    $interesado = new Interesado($datos);
    $interesado->idEntidad = $idEntidad;
    $interesado->save();

    //TODO: El historial va a cambiar
    Historial::registrar([
        "idEntidades" => [$idEntidad, (Auth::guest() ? NULL : Auth::user()->idEntidad)],
        "titulo" => (Auth::guest() ? MensajesHistorial::TituloInteresadoRegistro : MensajesHistorial::TituloInteresadoRegistroXUsuario),
        "mensaje" => ""
    ]);
    return $idEntidad;
  }

  public static function registrarAlumno($id, $idAlumno = NULL)/* - */ {
    $datos = Interesado::obtenerXId($id, TRUE)->toArray();

    $idAlumnoRel = Interesado::obtenerIdAlumno($id);
    if (in_array($datos["estado"], [EstadosInteresado::FichaCompleta, EstadosInteresado::AlumnoRegistrado]) || $idAlumnoRel != 0) {
      return ($idAlumnoRel != 0 ? $idAlumnoRel : NULL);
    }

    if (is_null($idAlumno)) {
      $datos["comentarioAdministrador"] = $datos["comentarioAdicional"];
      $idEntidadAlumno = Entidad::registrar($datos, TiposEntidad::Alumno, EstadosAlumno::PorConfirmar);
      $datos += [
          "conComputadora" => 0,
          "conInternet" => 0,
          "conPlumonPizarra" => 0,
          "conAmbienteClase" => 0,
          "numeroHorasClase" => 2
      ];

      $entidadCurso = EntidadCurso::obtenerXIdEntidad($id);
      if (!is_null($entidadCurso)) {
        EntidadCurso::registrarActualizar($idEntidadAlumno, $entidadCurso->idCurso);
      }

      $alumno = new Alumno($datos);
      $alumno->idEntidad = $idEntidadAlumno;
      $alumno->save();
      $idAlumno = $idEntidadAlumno;

      //TODO: El historial va a cambiar
      Historial::registrar([
          "idEntidades" => [$idAlumno, (Auth::guest() ? NULL : Auth::user()->idEntidad)],
          "titulo" => (Auth::guest() ? MensajesHistorial::TituloAlumnoRegistro : MensajesHistorial::TituloAlumnoRegistroXUsuario),
          "enviarCorreo" => (Auth::guest() ? 1 : 0),
          "mensaje" => ""
      ]);
    } else {
      $alumno = Entidad::obtenerXId($idAlumno);
      $alumno->comentarioAdministrador = $datos["comentarioAdicional"];
      $alumno->save();
    }

    RelacionEntidad::registrar($idAlumno, $id, TiposRelacionEntidad::AlumnoInteresado);
    Interesado::actualizarEstado($id, EstadosInteresado::FichaCompleta);

    //TODO: El historial va a cambiar
    Historial::registrar([
        "idEntidades" => [$id, (Auth::guest() ? NULL : Auth::user()->idEntidad)],
        "titulo" => (Auth::guest() ? MensajesHistorial::TituloInteresadoRegistroAlumno : MensajesHistorial::TituloInteresadoRegistroAlumnoXUsuario),
        "mensaje" => ""
    ]);
    return $idAlumno;
  }

  public static function actualizar($id, $datos)/* - */ {
    Entidad::actualizar($id, $datos, TiposEntidad::Interesado, $datos["estado"]);
    EntidadCurso::registrarActualizar($id, $datos["idCurso"]);
    $interesado = Interesado::obtenerXId($id, TRUE);
    $interesado->update($datos);
  }

  public static function actualizarEstado($id, $estado)/* - */ {
    Interesado::obtenerXId($id, TRUE);
    Entidad::actualizarEstado($id, $estado);
  }

  public static function enviarCotizacion($id, $datos)/* - */ {
    $interesado = Interesado::obtenerXId($id, TRUE);
    $interesado->costoXHoraClase = $datos["costoXHoraClase"];
    $interesado->save();

    $entidad = Entidad::ObtenerXId($id);
    $curso = Curso::obtenerXId($datos["idCurso"]);
    $datos["urlInscripcion"] = route("alumnos.crear.externo", ["codigoVerificacion" => Crypt::encrypt($entidad->id)]);

    $esPrueba = (isset($datos["correoCotizacionPrueba"]));
    $correo = ($esPrueba ? $datos["correoCotizacionPrueba"] : $entidad->correoElectronico);
    $nombreDestinatario = ($esPrueba ? "" : $entidad->nombre . " " . $entidad->apellido);

    $nombresArchivosAdjuntos = $datos["nombresArchivosAdjuntos"];
    $nombresOriginalesArchivosAdjuntos = $datos["nombresOriginalesArchivosAdjuntos"];
    $nombresArchivosAdjuntosEliminados = $datos["nombresArchivosAdjuntosEliminados"];
    if (isset($curso->adjuntos) && $curso->adjuntos != null) {
      $adjuntosRegistrados = explode(",", $curso->adjuntos);
      foreach ($adjuntosRegistrados as $adjuntoReg) {
        $datosAdjuntoReg = ($adjuntoReg != "" ? explode(":", $adjuntoReg) : []);
        if (count($datosAdjuntoReg) == 2) {
          $nombresArchivosAdjuntos = $datosAdjuntoReg[0] . "," . $nombresArchivosAdjuntos;
          $nombresOriginalesArchivosAdjuntos = $datosAdjuntoReg[1] . "," . $nombresOriginalesArchivosAdjuntos;
        }
      }
    }

    Config::set("mail.username", VariableSistema::obtenerXLlave("correo"));
    Config::set("mail.password", VariableSistema::obtenerXLlave("contrasenaCorreo"));
    $correoNotificaciones = VariableSistema::obtenerXLlave("correo");

    Mail::send("interesado.plantillaCorreo.cotizacionNUEVO", $datos, function ($m) use ($esPrueba, $correo, $nombreDestinatario, $nombresArchivosAdjuntos, $nombresOriginalesArchivosAdjuntos, $nombresArchivosAdjuntosEliminados, $correoNotificaciones) {
      $m->to($correo, $nombreDestinatario)->subject("English at Home Perú - Cotización");
      if (!$esPrueba && $correo !== $correoNotificaciones) {
        $m->bcc($correoNotificaciones);
      }

      if (isset($nombresArchivosAdjuntos) && isset($nombresOriginalesArchivosAdjuntos)) {
        $nombresArchivosAdjuntosSel = explode(",", $nombresArchivosAdjuntos);
        $nombresOriginalesArchivosAdjuntosSel = explode(",", $nombresOriginalesArchivosAdjuntos);

        $rutaBaseAlmacenamiento = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
        for ($i = 0; $i < count($nombresArchivosAdjuntosSel); $i++) {
          if (trim($nombresArchivosAdjuntosSel[$i]) == "") {
            continue;
          }
          if (strpos($nombresArchivosAdjuntosEliminados, $nombresArchivosAdjuntosSel[$i] . ",") !== false) {
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
          if (isset($curso->adjuntos) && $curso->adjuntos != null && strpos($curso->adjuntos, $nombresArchivosAdjuntosSel[$i] . ":") !== false) {
            continue;
          }
          Archivo::eliminar($nombresArchivosAdjuntosSel[$i]);
        }
      }
      Interesado::actualizarEstado($id, EstadosInteresado::Seguimiento);

      //TODO: El historial va a cambiar
      Historial::registrar([
          "idEntidades" => [$id, Auth::user()->idEntidad],
          "titulo" => (MensajesHistorial::TituloInteresadoEnvioCorreoCotizacion),
          "mensaje" => ""
      ]);
    }
  }

  public static function obtenerIdAlumno($id)/* - */ {
    Interesado::obtenerXId($id, TRUE);
    $relacionEntidad = RelacionEntidad::obtenerXIdEntidadB($id);
    return (count($relacionEntidad) > 0 ? $relacionEntidad[0]->idEntidadA : 0);
  }

  public static function obtenerXIdAlumno($idAlumno)/* - */ {
    try {
      $relacionEntidad = RelacionEntidad::obtenerXIdEntidadA($idAlumno);
      if (count($relacionEntidad) > 0) {
        return Interesado::obtenerXId($relacionEntidad[0]->idEntidadB, TRUE);
      } else {
        return NULL;
      }
    } catch (\Exception $e) {
      Log::error($e);
      return NULL;
    }
  }

  public static function eliminar($id)/* - */ {
    Interesado::obtenerXId($id, TRUE);
    Entidad::eliminar($id);
  }

}
