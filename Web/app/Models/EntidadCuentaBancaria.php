<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntidadCuentaBancaria extends Model/* |-| */ {

  public $timestamps = false;
  protected $table = "entidadCuentaBancaria";
  protected $fillable = [
      "idEntidad",
      "banco",
      "numeroCuenta"
  ];

  public static function nombreTabla() {
    $modeloEntidadCuentaBancaria = new EntidadCuentaBancaria();
    $nombreTabla = $modeloEntidadCuentaBancaria->getTable();
    unset($modeloEntidadCuentaBancaria);
    return $nombreTabla;
  }

  public static function obtenerXIdEntidad($idEntidad) {
    return EntidadCuentaBancaria::where("idEntidad", $idEntidad)->get();
  }

  public static function registrarActualizar($idEntidad, $cuentasBancarias) {
    if (isset($cuentasBancarias)) {
      EntidadCuentaBancaria::where("idEntidad", $idEntidad)->delete();

      $cuentasBancariasPro = explode(";", $cuentasBancarias);
      foreach ($cuentasBancariasPro as $cuentaBancaria) {
        $datCuentaBancaria = explode("|", $cuentaBancaria);
        if (count($datCuentaBancaria) == 2) {
          $banco = $datCuentaBancaria[0];
          $numero = $datCuentaBancaria[1];

          $entidadCuentaBancaria = new EntidadCuentaBancaria([
              "idEntidad" => $idEntidad,
              "banco" => $banco,
              "numeroCuenta" => $numero
          ]);
          $entidadCuentaBancaria->save();
        }
      }
    }
  }

}
