<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoClase extends Model {

  public $timestamps = false;
  protected $table = "pagoClase";
  protected $fillable = [
      "idPago",
      "idClase",
      "duracionCubierta",
      "costoXHoraClase"
  ];

  public static function nombreTabla()/* - */ {
    $modeloPagoClase = new PagoClase();
    $nombreTabla = $modeloPagoClase->getTable();
    unset($modeloPagoClase);
    return $nombreTabla;
  }

  public static function registrarActualizar($idPago, $idClase, $idAlumnoProfesor, $esPagoAlumno = TRUE)/* - */ {
    //TODO: solo se llama una vez, debe eliminarse esta función previa verificación
    if ($esPagoAlumno) {
      PagoClase::where("idClase", $idClase)
              ->whereIn("idPago", function($q) use ($idAlumnoProfesor) {
                $q->select("idPago")
                ->from(with(new PagoAlumno)->getTable())
                ->where("idAlumno", $idAlumnoProfesor);
              })->delete();
      $clase = Clase::obtenerXId($idAlumnoProfesor, $idClase);
      $pago = Pago::obtenerXId($idPago);

      $pagoClase = new PagoClase([
          "idPago" => $idPago,
          "idClase" => $idClase,
          "duracionCubierta" => $clase->duracion,
          "costoXHoraClase" => $pago->costoXHoraClase
      ]);
      $pagoClase->save();
    } else {
      PagoClase::where("idClase", $idClase)
              ->whereIn("idPago", function($q) use ($idAlumnoProfesor) {
                $q->select("idPago")
                ->from(with(new PagoProfesor)->getTable())
                ->where("idProfesor", $idAlumnoProfesor);
              })->delete();
      $pagoClase = new PagoClase([
          "idPago" => $idPago,
          "idClase" => $idClase
      ]);
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

  public static function obtenerXIdPago($idPago)/* - */ {
    return PagoClase::where("idPago", $idPago)->get();
  }

  public static function eliminarXIdPago($idPago) {
    PagoClase::where("idPago", $idPago)->delete();
  }

}
