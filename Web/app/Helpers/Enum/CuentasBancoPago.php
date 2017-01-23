<?php

namespace App\Helpers\Enum;

class CuentasBancoPago {

  const Bcp = "BCP";
  const Interbank = "INTERBANK";
  const Scotiabank = "SCOTIABANK";
  const Bbva = "BBVA";
  const Empresarial = "EMPRESARIAL";

  public static function listar() {
    return [
        CuentasBancoPago::Bcp => "BCP",
        CuentasBancoPago::Interbank => "Interbank",
        CuentasBancoPago::Scotiabank => "Scotiabank",
        CuentasBancoPago::Bbva => "BBVA",
        CuentasBancoPago::Empresarial => "Empresarial"
    ];
  }

}
