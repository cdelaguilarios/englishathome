<?php

namespace App\Models;

use DB;
use Auth;
use Crypt;
use Carbon\Carbon;
use App\Helpers\Enum\TiposEntidad;
use App\Helpers\Enum\EstadosClase;
use App\Helpers\Enum\EstadosAlumno;
use App\Helpers\Enum\MensajesHistorial;
use Illuminate\Database\Eloquent\Model;

class Alumno extends Model {

  public $timestamps = false;
  protected $primaryKey = "idEntidad";
  protected $table = "alumno";
  protected $fillable = ["inglesLugarEstudio", "inglesPracticaComo", "inglesObjetivo", "conComputadora", "conInternet", "conPlumonPizarra", "conAmbienteClase", "numeroHorasClase", "fechaInicioClase", "comentarioAdicional", "costoHoraClase"];

  public static function nombreTabla() {
    $modeloAlumno = new Alumno();
    $nombreTabla = $modeloAlumno->getTable();
    unset($modeloAlumno);
    return $nombreTabla;
  }

  public static function listar($datos = NULL) {
    $alumnos = Alumno::leftJoin(Entidad::nombreTabla() . " as entidad", Alumno::nombreTabla() . ".idEntidad", "=", "entidad.id")->where("entidad.eliminado", 0)->groupBy("entidad.id")->distinct();
    if (isset($datos["estado"])) {
      if ($datos["estado"] == EstadosAlumno::Activo) {
        $alumnos->where(function ($q) use($datos) {
          $q->where("entidad.estado", $datos["estado"])->orWhere('entidad.estado', EstadosAlumno::CuotaProgramada);
        });
      } else {
        $alumnos->where("entidad.estado", $datos["estado"]);
      }
    }
    $alumnos->leftJoin(Clase::nombreTabla() . " as clase", function ($q) {
      $q->on("clase.idAlumno", "=", "entidad.id")
        ->on("clase.id", "=", DB::raw("(SELECT id FROM " . Clase::nombreTabla() . " WHERE idAlumno = entidad.id ORDER BY fechaFin DESC LIMIT 1)"))
        ->where("clase.eliminado", "=", 0);
    });
    $alumnos->join(EntidadCurso::nombreTabla() . " as entidadCurso", function ($q) {
      $q->on("entidadCurso.idEntidad", "=", "entidad.id");
    });
    $alumnos->join(Curso::nombreTabla() . " as curso", function ($q) {
      $q->on("curso.id", "=", "entidadCurso.idCurso");
    });    
    return $alumnos->select(Alumno::nombreTabla(). ".*", "entidad.*", "clase.fechaFin as fechaUltimaClase", DB::raw("GROUP_CONCAT(curso.nombre SEPARATOR ', ') as curso"));
  }

  public static function listarBusqueda($terminoBus = NULL) {
    $alumnos = Alumno::listar()->select("entidad.id", DB::raw('CONCAT(entidad.nombre, " ", entidad.apellido) AS nombreCompleto'));
    if (isset($terminoBus)) {
      $alumnos->whereRaw('CONCAT(entidad.nombre, " ", entidad.apellido) like ?', ["%{$terminoBus}%"]);
    }
    return $alumnos->lists("nombreCompleto", "entidad.id");
  }

  public static function obtenerXId($id, $simple = FALSE) {
    $alumno = Alumno::listar()->where("entidad.id", $id)->firstOrFail();
    if (!$simple) {
      $alumno->interesadoRelacionado = Interesado::obtenerXIdAlumno($id);
      $alumno->horario = Horario::obtenerFormatoJson($id);
      $alumno->direccionUbicacion = Ubigeo::obtenerTextoUbigeo($alumno->codigoUbigeo);
      $alumno->numeroPeriodos = Clase::totalPeriodos($id);
      $entidadNivelIngles = EntidadNivelIngles::obtenerXEntidad($id);
      $alumno->idNivelIngles = (isset($entidadNivelIngles) ? $entidadNivelIngles->idNivelIngles : NULL);
      $entidadCurso = EntidadCurso::obtenerXEntidad($id);
      $alumno->idCurso = (isset($entidadCurso) ? $entidadCurso->idCurso : NULL);
      $alumno->datosProximaClase = Clase::obtenerProximaClase($id);
      if (isset($alumno->datosProximaClase)) {
        $datosPago = PagoAlumno::obtenerXClase($id, $alumno->datosProximaClase->id);
        $alumno->datosProximaClase->tiempos = (isset($datosPago) ? PagoAlumno::obtenerTiemposClasesXId($id, $datosPago->id) : NULL);
      }
      $alumno->profesorProximaClase = (isset($alumno->datosProximaClase) && Profesor::verificarExistencia($alumno->datosProximaClase->idProfesor) ? Profesor::obtenerXId($alumno->datosProximaClase->idProfesor) : NULL);
      $idAlumnoAnterior = Alumno::listar()->select("entidad.id")->where("entidad.id", "<", $id)->where("entidad.estado", $alumno->estado)->orderBy("entidad.id", "DESC")->first();
      $idAlumnoSiguiente = Alumno::listar()->select("entidad.id")->where("entidad.id", ">", $id)->where("entidad.estado", $alumno->estado)->first();
      $alumno->idAlumnoAnterior = (isset($idAlumnoAnterior) ? $idAlumnoAnterior->id : NULL);
      $alumno->idAlumnoSiguiente = (isset($idAlumnoSiguiente) ? $idAlumnoSiguiente->id : NULL);
    }
    return $alumno;
  }

  public static function registrar($req) {
    $datos = $req->all();
    if (isset($datos["fechaNacimiento"])) {
      $datos["fechaNacimiento"] = Carbon::createFromFormat("d/m/Y H:i:s", $datos["fechaNacimiento"] . " 00:00:00")->toDateTimeString();
    }
    $datos["fechaInicioClase"] = Carbon::createFromFormat("d/m/Y H:i:s", $datos["fechaInicioClase"] . " 00:00:00")->toDateTimeString();

    $idEntidad = Entidad::registrar($datos, TiposEntidad::Alumno, EstadosAlumno::PorConfirmar);
    Entidad::registrarActualizarImagenPerfil($idEntidad, $req->file("imagenPerfil"));
    EntidadNivelIngles::registrarActualizar($idEntidad, $datos["idNivelIngles"]);
    EntidadCurso::registrarActualizar($idEntidad, $datos["idCurso"]);
    Horario::registrarActualizar($idEntidad, $datos["horario"]);

    $alumno = new Alumno($datos);
    $alumno->idEntidad = $idEntidad;
    $alumno->save();

    Historial::registrar([
        "idEntidades" => [$idEntidad, (Auth::guest() ? NULL : Auth::user()->idEntidad)],
        "titulo" => (Auth::guest() ? MensajesHistorial::TituloAlumnoRegistro : MensajesHistorial::TituloAlumnoRegistroXUsuario),
        "enviarCorreo" => (Auth::guest() ? 1 : 0),
        "mensaje" => ""
    ]);
    return $idEntidad;
  }

  public static function registrarExterno($req) {
    $datos = $req->all();
    $interesado = Interesado::obtenerXId(Crypt::decrypt($datos["codigoVerificacion"]), TRUE);
    if ($interesado->idEntidad == $datos["idInteresado"] && Interesado::obtenerIdAlumno($datos["idInteresado"]) == 0) {
      $idEntidad = Alumno::registrar($req);
      Interesado::registrarAlumno($datos["idInteresado"], $idEntidad);
    }
  }

  public static function actualizar($id, $req) {
    $datos = $req->all();
    if (isset($datos["fechaNacimiento"])) {
      $datos["fechaNacimiento"] = Carbon::createFromFormat("d/m/Y H:i:s", $datos["fechaNacimiento"] . " 00:00:00")->toDateTimeString();
    }
    $datos["fechaInicioClase"] = Carbon::createFromFormat("d/m/Y H:i:s", $datos["fechaInicioClase"] . " 00:00:00")->toDateTimeString();

    Entidad::actualizar($id, $datos, TiposEntidad::Alumno, $datos["estado"]);
    EntidadNivelIngles::registrarActualizar($id, $datos["idNivelIngles"]);
    Entidad::registrarActualizarImagenPerfil($id, $req->file("imagenPerfil"));
    EntidadCurso::registrarActualizar($id, $datos["idCurso"]);
    Horario::registrarActualizar($id, $datos["horario"]);
    $alumno = Alumno::obtenerXId($id, TRUE);
    $alumno->update($datos);
  }

  public static function actualizarEstado($id, $estado) {
    Alumno::obtenerXId($id, TRUE);
    Entidad::actualizarEstado($id, $estado);
  }

  public static function actualizarHorario($id, $horario) {
    Alumno::obtenerXId($id, TRUE);
    Horario::registrarActualizar($id, $horario);
  }

  public static function eliminar($id) {
    Alumno::obtenerXId($id, TRUE);
    Entidad::eliminar($id);
    Clase::eliminadXIdAdlumno($id);
  }

  public static function sincronizarEstados() {
    Clase::sincronizarEstados();
    $alumnos = Alumno::listar()
            ->whereNotIn("entidad.id", Clase::listarXEstados([EstadosClase::Programada, EstadosClase::PendienteConfirmar])->groupBy("idAlumno")->lists("idAlumno"))
            ->whereNotIn("entidad.estado", [EstadosAlumno::PorConfirmar, EstadosAlumno::StandBy, EstadosAlumno::Inactivo])
            ->get();
    foreach ($alumnos as $alumno) {
      Alumno::actualizarEstado($alumno->idEntidad, EstadosAlumno::CuotaProgramada);
    }
  }

  public static function verificarExistencia($id) {
    try {
      Alumno::obtenerXId($id, TRUE);
    } catch (\Exception $ex) {
      return FALSE;
    }
    return TRUE;
  }

  //REPORTE
  public static function listarCampos() {
    return [
        "inglesLugarEstudio" => ["titulo" => "Ingles - Lugar de estudio"],
        "inglesPracticaComo" => ["titulo" => "Ingles - Como practica"],
        "inglesObjetivo" => ["titulo" => "Ingles - Objetivo"],
        "conComputadora" => ["titulo" => "Con computadora"],
        "conInternet" => ["titulo" => "Con internet"],
        "conPlumonPizarra" => ["titulo" => "Con plumon y pizarra"],
        "conAmbienteClase" => ["titulo" => "Con ambiente adecuado para clases"],
        "numeroHorasClase" => ["titulo" => "Número de horas por clase"],
        "fechaInicioClase" => ["titulo" => "Fecha de inicio de clases"],
        "comentarioAdicional" => ["titulo" => "Comentario adicional"],
        "costoHoraClase" => ["titulo" => "Costo por hora de clase"]
    ];
  }

  public static function listarEntidadesRelacionadas() {
    return [];
  }

}
