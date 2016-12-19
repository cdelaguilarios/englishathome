<?php

namespace App\Models;

use Auth;
use Carbon\Carbon;
use App\Helpers\Enum\TiposEntidad;
use App\Helpers\Enum\EstadosAlumno;
use App\Helpers\Enum\MensajesHistorial;
use Illuminate\Database\Eloquent\Model;

class Alumno extends Model {

  public $timestamps = false;
  protected $table = "alumno";
  protected $fillable = ["inglesLugarEstudio", "inglesPracticaComo", "inglesObjetivo", "conComputadora", "conInternet", "conPlumonPizarra", "conAmbienteClase", "numeroHorasClase", "fechaInicioClase", "comentarioAdicional", "costoHoraClase"];

  public static function nombreTabla() {
    $modeloAlumno = new Alumno();
    $nombreTabla = $modeloAlumno->getTable();
    unset($modeloAlumno);
    return $nombreTabla;
  }

  public static function listar() {
    $nombreTabla = Alumno::nombreTabla();
    return Alumno::select($nombreTabla . ".*", "entidad.*")
                    ->leftJoin(Entidad::nombreTabla() . " as entidad", $nombreTabla . ".idEntidad", "=", "entidad.id")
                    ->where("entidad.eliminado", 0);
  }

  public static function obtenerXId($id, $simple = FALSE) {
    $nombreTabla = Alumno::nombreTabla();
    $alumno = Alumno::select($nombreTabla . ".*", "entidad.*")
                    ->leftJoin(Entidad::nombreTabla() . " as entidad", $nombreTabla . ".idEntidad", "=", "entidad.id")
                    ->where("entidad.id", $id)
                    ->where("entidad.eliminado", 0)->firstOrFail();

    if (!$simple) {
      $alumno->horario = Horario::obtenerFormatoJson($id);
      $alumno->direccionUbicacion = Ubigeo::obtenerTextoUbigeo($alumno->codigoUbigeo);
      $alumno->numeroPeriodos = Clase::totalPeriodos($id);
      $alumno->idCurso = EntidadCurso::listar($id)->firstOrFail()->idCurso;
      $alumno->totalSaldoFavor = PagoAlumno::totalSaldoFavor($id);
      $alumno->idNivelIngles = EntidadNivelIngles::obtenerXEntidad($id);
      $alumno->idCurso = EntidadCurso::obtenerXEntidad($id);
    }
    return $alumno;
  }

  public static function registrar($req) {
    $datos = $req->all();
    $datos["fechaNacimiento"] = Carbon::createFromFormat("d/m/Y H:i:s", $datos["fechaNacimiento"] . " 00:00:00")->toDateTimeString();
    $datos["fechaInicioClase"] = Carbon::createFromFormat("d/m/Y H:i:s", $datos["fechaInicioClase"] . " 00:00:00")->toDateTimeString();

    $idEntidad = Entidad::registrar($datos, TiposEntidad::Alumno, EstadosAlumno::Registrado);
    EntidadNivelIngles::registrarActualizar($idEntidad, $datos["idNivelIngles"]);
    Entidad::registrarActualizarImagenPerfil($idEntidad, $req->file("imagenPerfil"));
    EntidadCurso::registrarActualizar($idEntidad, $datos["idCurso"]);
    Horario::registrarActualizar($idEntidad, $datos["horario"]);

    $alumno = new Alumno($datos);
    $alumno->idEntidad = $idEntidad;
    $alumno->save();

    Historial::Registrar([$idEntidad, ((Auth::guest()) ? NULL : Auth::user()->idEntidad)], ((Auth::guest()) ? MensajesHistorial::TituloAlumnoRegistro : MensajesHistorial::TituloAlumnoRegistroXUsuario), "");
    return $idEntidad;
  }

  public static function actualizar($id, $req) {
    $datos = $req->all();
    $datos["fechaNacimiento"] = Carbon::createFromFormat("d/m/Y H:i:s", $datos["fechaNacimiento"] . " 00:00:00")->toDateTimeString();
    $datos["fechaInicioClase"] = Carbon::createFromFormat("d/m/Y H:i:s", $datos["fechaInicioClase"] . " 00:00:00")->toDateTimeString();

    Entidad::Actualizar($id, $datos, TiposEntidad::Alumno, EstadosAlumno::Registrado);
    EntidadNivelIngles::registrarActualizar($id, $datos["idNivelIngles"]);
    Entidad::registrarActualizarImagenPerfil($id, $req->file("imagenPerfil"));
    EntidadCurso::registrarActualizar($id, $datos["idCurso"]);
    Horario::registrarActualizar($id, $datos["horario"]);

    $alumno = Alumno::obtenerXId($id, TRUE);
    $alumno->update($datos);
  }

  public static function eliminar($id) {
    Entidad::eliminar($id);
  }

}
