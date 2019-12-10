<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlumnoBolsaHoras extends Model/* |-| */ {

  public $timestamps = false;
  protected $table = "alumnoBolsaHoras";
  protected $fillable = [
      "idAlumno",
      "idPago"
  ];

  public static function nombreTabla()/* - */ {
    $modeloAlumnoBolsaHoras = new AlumnoBolsaHoras();
    $nombreTabla = $modeloAlumnoBolsaHoras->getTable();
    unset($modeloAlumnoBolsaHoras);
    return $nombreTabla;
  }

  public static function obtenerXIdAlumno($idAlumno)/* - */ {
    return EntidadCuentaBancaria::where("idAlumno", $idAlumno)->get();
  }

}
