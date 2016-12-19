<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoClase extends Model {

  public $timestamps = false;
  protected $table = "pagoClase";
  protected $fillable = ["idPago", "idClase"];

  public static function NombreTabla() {
    $modeloPagoClase = new PagoClase();
    $nombreTabla = $modeloPagoClase->getTable();
    unset($modeloPagoClase);
    return $nombreTabla;
  }

  public static function registrar($idPago, $idClase) {
    $pagoClase = new PagoClase(["idPago" => $idPago, "idClase" => $idClase]);
    $pagoClase->save();
  }

  public static function totalXProfesor($idClase) {
    $nombreTabla = PagoClase::nombreTabla();
    return PagoClase::leftJoin(PagoProfesor::NombreTabla() . " as pagoProfesor", $nombreTabla . ".idPago", "=", "pagoProfesor.idPago")
                    ->whereNotNull("pagoProfesor.idProfesor")
                    ->where($nombreTabla . ".idClase", $idClase)->count();
  }

}
