<?php

namespace App\Models;

use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class EntidadTarea extends Model/* - */ {

  public $timestamps = false;
  protected $table = "entidadTarea";
  protected $fillable = [
      "fechaRevision",
      "fechaRealizada"
  ];

  public static function nombreTabla()/* - */ {
    $modeloEntidadTarea = new EntidadTarea();
    $nombreTabla = $modeloEntidadTarea->getTable();
    unset($modeloEntidadTarea);
    return $nombreTabla;
  }

  public static function actualizarRealizacion($idTarea, $realizado) {
    if (!Auth::guest()) {
      $idEntidad = Auth::user()->idEntidad;

      $entidadTarea = EntidadTarea::where("idEntidad", $idEntidad)
              ->where("idTarea", $idTarea)
              ->first();
      if ($entidadTarea == NULL) {
        $entidadTarea = new EntidadTarea();
        $entidadTarea->idEntidad = $idEntidad;
        $entidadTarea->idTarea = $idTarea;
        $entidadTarea->fechaRealizada = ($realizado ? Carbon::now()->toDateTimeString() : null);
        $entidadTarea->save();
      } else {
        EntidadTarea::where("idEntidad", $idEntidad)
                ->where("idTarea", $idTarea)
                ->update(["fechaRealizada" => ($realizado ? Carbon::now()->toDateTimeString() : null)]);
      }
    }
  }

}
