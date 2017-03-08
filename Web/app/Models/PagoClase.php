<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoClase extends Model {

  public $timestamps = false;
  protected $table = "pagoClase";
  protected $fillable = ["idPago", "idClase"];

  public static function nombreTabla() {
    $modeloPagoClase = new PagoClase();
    $nombreTabla = $modeloPagoClase->getTable();
    unset($modeloPagoClase);
    return $nombreTabla;
  }

  public static function registrar($idPago, $idClase) {
    if (PagoClase::where("idPago", $idPago)->where("idClase", $idClase)->count() == 0) {
      $pagoClase = new PagoClase(["idPago" => $idPago, "idClase" => $idClase]);
      $pagoClase->save();
    }
  }

  public static function totalXProfesor($idClase) {
    $nombreTabla = PagoClase::nombreTabla();
    return PagoClase::leftJoin(PagoProfesor::nombreTabla() . " as pagoProfesor", $nombreTabla . ".idPago", "=", "pagoProfesor.idPago")
                    ->whereNotNull("pagoProfesor.idProfesor")
                    ->where($nombreTabla . ".idClase", $idClase)->count();
  }

  public static function obtenerXIdClase($idClase) {
    return PagoClase::where("idClase", $idClase)->get();
  }

  public static function obtenerXIdPago($idPago) {
    return PagoClase::where("idPago", $idPago)->get();
  }

}
