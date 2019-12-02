<?php

namespace App\Helpers\Enum;

class MotivosPago/* - */ {

  const Clases = "CLASES";
  const Otros = "OTROS";
  const ClaseCancelada = "CLASE_CANCELADA";

  public static function listar()/* - */ {
    return [
        MotivosPago::Clases => "Pago por clases",
        MotivosPago::Otros => "Otros"
    ];
  }

}
