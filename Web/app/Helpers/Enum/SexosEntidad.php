<?php

namespace App\Helpers\Enum;

class SexosEntidad {

  const Masculino = "M";
  const Femenino = "F";

  public static function listar() {
    return [
        SexosEntidad::Masculino => "Masculino",
        SexosEntidad::Femenino => "Femenino"
    ];
  }

}
