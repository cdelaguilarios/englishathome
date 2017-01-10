<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntidadCurso extends Model {

  public $timestamps = false;
  protected $table = "entidadCurso";
  protected $fillable = ["idEntidad", "idCurso"];

  public static function NombreTabla() {
    $modeloEntidadCurso = new EntidadCurso();
    $nombreTabla = $modeloEntidadCurso->getTable();
    unset($modeloEntidadCurso);
    return $nombreTabla;
  }

  public static function obtenerXEntidad($idEntidad) {
    $entidadCurso = EntidadCurso::where("idEntidad", $idEntidad)->first();
    return ($entidadCurso != NULL ? $entidadCurso->idCurso : NULL);
  }

  public static function listar($idEntidad) {
    return EntidadCurso::where("idEntidad", $idEntidad);
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
