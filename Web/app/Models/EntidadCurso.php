<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntidadCurso extends Model {

  public $timestamps = false;
  protected $table = "entidadCurso";
  protected $fillable = ["idEntidad", "idCurso"];

  public static function nombreTabla() {
    $modeloEntidadCurso = new EntidadCurso();
    $nombreTabla = $modeloEntidadCurso->getTable();
    unset($modeloEntidadCurso);
    return $nombreTabla;
  }

  public static function obtenerXEntidad($idEntidad, $primerCurso = TRUE) {
    $entidadCursos = EntidadCurso::where("idEntidad", $idEntidad)->select("idCurso")->get();
    return (count($entidadCursos) > 0 ? ($primerCurso ? $entidadCursos[0] : $entidadCursos) : NULL);
  }

  public static function registrarActualizar($idEntidad, $idCursos) {
    EntidadCurso::where("idEntidad", $idEntidad)->delete();
    if (isset($idCursos)) {
      $idCursosSel = (is_array($idCursos) ? $idCursos : [$idCursos]);
      foreach ($idCursosSel as $idCurso) {
        $entidadCurso = new EntidadCurso([
            "idEntidad" => $idEntidad,
            "idCurso" => $idCurso
        ]);
        $entidadCurso->save();
      }
    }
  }

}
