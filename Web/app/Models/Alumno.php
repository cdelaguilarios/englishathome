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
      $alumnos->where("entidad.estado", $datos["estado"]);
    }
    return $alumnos;
  }

  public static function listarBusqueda() {
    return Alumno::listar()->select("entidad.id", DB::raw('CONCAT(entidad.nombre, " ", entidad.apellido) AS nombreCompleto'))->lists("nombreCompleto", "entidad.id");
  }

  public static function obtenerXId($id, $simple = FALSE) {
    $alumno = Alumno::listar()->where("entidad.id", $id)->firstOrFail();
    if (!$simple) {
      $alumno->horario = Horario::obtenerFormatoJson($id);
      $alumno->direccionUbicacion = Ubigeo::obtenerTextoUbigeo($alumno->codigoUbigeo);
      $alumno->numeroPeriodos = Clase::totalPeriodos($id);
      $alumno->totalSaldoFavor = PagoAlumno::totalSaldoFavor($id);
      $alumno->idNivelIngles = EntidadNivelIngles::obtenerXEntidad($id);
      $entidadCurso = EntidadCurso::obtenerXEntidad($id);
      $alumno->idCurso = (!is_null($entidadCurso) ? $entidadCurso->idCurso : NULL);
      $datosProximaClase = Clase::obtenerProximaClase($id);
      $alumno->profesorProximaClase = (!is_null($datosProximaClase) ? Profesor::obtenerXId($datosProximaClase->idProfesor) : NULL);
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

}
