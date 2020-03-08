<?php

namespace App\Models;

use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class TareaNotificacion extends Model/* - */ {

  public $timestamps = false;
  protected $table = "tareaNotificacion";
  protected $fillable = [
      "titulo",
      "mensaje",
      "adjuntos",
      "fechaProgramada",
      "fechaNotificacion"
  ];

  public static function nombreTabla()/* - */ {
    $modeloTareaNotificacion = new TareaNotificacion();
    $nombreTabla = $modeloTareaNotificacion->getTable();
    unset($modeloTareaNotificacion);
    return $nombreTabla;
  }

  public static function listar()/* - */ {
    return TareaNotificacion::where("eliminado", 0);
  }

  public static function ObtenerXId($id)/* - */ {
    return TareaNotificacion::listar()->where("id", $id)->firstOrFail();
  }

  public static function registrar($datos, $creadoPorElSistema = TRUE)/* - */ {
    $tareaNotificacion = new TareaNotificacion($datos);
    $tareaNotificacion->idUsuarioCreador = ($creadoPorElSistema || Auth::guest() ? NULL : Auth::user()->idEntidad);
    $tareaNotificacion->fechaRegistro = Carbon::now()->toDateTimeString();
    $tareaNotificacion->save();
    return $tareaNotificacion->id;
  }

  public static function actualizar($id, $datos, $creadoPorElSistema = TRUE)/* - */ {
    $tareaNotificacion = TareaNotificacion::ObtenerXId($id);
    $tareaNotificacion->idUsuarioCreador = ($creadoPorElSistema || Auth::guest() ? NULL : Auth::user()->idEntidad);
    $tareaNotificacion->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
    $tareaNotificacion->update($datos);
  }

  public static function eliminarGrupo($ids)/* - */ {
    if (isset($ids) && count($ids) > 0) {
      foreach ($ids as $id) {
        $tareaNotificacion = TareaNotificacion::ObtenerXId($id);
        $tareaNotificacion->eliminado = 1;
        $tareaNotificacion->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
        $tareaNotificacion->save();
      }
    }
  }

}
