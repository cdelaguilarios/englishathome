<?php

namespace App\Models;

use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class EntidadNotificacion extends Model/* - */ {

  public $timestamps = false;
  protected $table = "entidadNotificacion";
  protected $fillable = [
      "idEntidad",
      "idNotificacion",
      "esObservador",
      "fechaRevision"
  ];

  public static function nombreTabla()/* - */ {
    $modeloEntidadNotificacion = new EntidadNotificacion();
    $nombreTabla = $modeloEntidadNotificacion->getTable();
    unset($modeloEntidadNotificacion);
    return $nombreTabla;
  }

  public static function revisarMultiple($idsNotificaciones) {
    if (!Auth::guest()) {
      $idEntidad = Auth::user()->idEntidad;

      foreach ($idsNotificaciones as $idNotificacion) {
        $entidadNotificacion = EntidadNotificacion::where("idEntidad", $idEntidad)
                ->where("idNotificacion", $idNotificacion)
                ->first();
        if ($entidadNotificacion == NULL) {
          $entidadNotificacion = new EntidadNotificacion();
          $entidadNotificacion->idEntidad = $idEntidad;
          $entidadNotificacion->idNotificacion = $idNotificacion;
          $entidadNotificacion->esObservador = 1;
          $entidadNotificacion->fechaRevision = Carbon::now()->toDateTimeString();
          $entidadNotificacion->save();
        } else {
          EntidadNotificacion::where("idEntidad", $idEntidad)
                  ->where("idNotificacion", $idNotificacion)
                  ->update(["fechaRevision" => Carbon::now()->toDateTimeString()]);
        }
      }
    }
  }

}
