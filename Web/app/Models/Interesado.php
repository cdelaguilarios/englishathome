<?php

namespace App\Models;

use Mail;
use Crypt;
use Carbon\Carbon;
use App\Helpers\Enum\TiposEntidad;
use App\Helpers\Enum\EstadosInteresado;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Enum\TiposRelacionEntidad;

class Interesado extends Model {

  public $timestamps = false;
  protected $primaryKey = "idEntidad";
  protected $table = "interesado";
  protected $fillable = ["consulta", "cursoInteres", "codigoVerificacion"];

  public static function nombreTabla() {
    $modeloInteresado = new Interesado();
    $nombreTabla = $modeloInteresado->getTable();
    unset($modeloInteresado);
    return $nombreTabla;
  }

  protected static function listar($datos = NULL) {
    $nombreTabla = Interesado::nombreTabla();
    $interesados = Interesado::select($nombreTabla . ".*", "entidad.*")
            ->leftJoin(Entidad::nombreTabla() . " as entidad", $nombreTabla . ".idEntidad", "=", "entidad.id")
            ->where("entidad.eliminado", 0);

    if (isset($datos["estado"])) {
      $interesados->where("entidad.estado", $datos["estado"]);
    }
    return $interesados;
  }

  protected static function obtenerXId($id) {
    return Interesado::listar()->where("entidad.id", $id)->firstOrFail();
  }

  protected static function registrar($datos) {
    $entidad = new Entidad($datos);
    $entidad->tipo = TiposEntidad::Interesado;
    $entidad->estado = EstadosInteresado::PendienteInformacion;
    $entidad->save();

    $interesado = new Interesado($datos);
    $interesado->idEntidad = $entidad->id;
    $interesado->save();
    return $entidad->id;
  }

  protected static function actualizar($id, $datos) {
    $entidad = Entidad::ObtenerXId($id);
    $entidad->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
    $entidad->update($datos);

    $interesado = Interesado::obtenerXId($id);
    $interesado->update($datos);
  }

  protected static function actualizarEstado($id, $estado) {
    Entidad::actualizarEstado($id, $estado);
  }

  protected static function envioCotizacion($id, $datos) {
    $entidad = Entidad::ObtenerXId($id);
    $entidad->estado = EstadosInteresado::CotizacionEnviada;
    $entidad->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
    $entidad->save();

    $curso = Curso::obtenerXId($datos["idCurso"]);
    $datos["titulo"] = $curso->nombre;
    $datos["curso"] = $curso->nombre;
    $datos["urlInscripcion"] = route("usuarios.crear.externo", ["codigoVerificacion" => Crypt::encrypt($entidad->id)]);
    $correo = (!is_null($datos["correoCotizacionPrueba"]) ? $datos["correoCotizacionPrueba"] : $entidad->correoElectronico);
    $nombreDestinatario = (!is_null($datos["correoCotizacionPrueba"]) ? "" : $entidad->nombre . " " . $entidad->apellido);

    Mail::send('plantillaCorreo.cotizacion', $datos, function ($m) use ($correo, $nombreDestinatario) {
      $m->to($correo, $nombreDestinatario)->subject('English at home - Cotización');
    });
  }

  protected static function alumnoRegistrado($id, $idAlumno) {
    RelacionEntidad::registrar($idAlumno, $id, TiposRelacionEntidad::AlumnoInteresado);
    $entidad = Entidad::ObtenerXId($id);
    $entidad->estado = EstadosInteresado::AlumnoRegistrado;
    $entidad->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
    $entidad->save();
  }

  protected static function eliminar($id) {
    Interesado::obtenerXId($id);
    Entidad::eliminar($id);
  }

}
