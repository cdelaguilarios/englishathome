<?php

namespace App\Helpers\Enum;

class SexosEntidad {

  const Masculino = "M";
  const Femenino = "F";

  public static function listar($ingles = FALSE) {
    return [
        SexosEntidad::Masculino => ($ingles ? "Male" : "Masculino"),
        SexosEntidad::Femenino => ($ingles ? "Female" : "Femenino")
    ];
  }

}
