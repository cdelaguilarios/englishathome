<?php

namespace App\Helpers\Enum;

class GenerosEntidad {

  const Masculino = "M";
  const Femenino = "F";

  public static function listar() {
    return [
        GenerosEntidad::Masculino => "Masculino",
        GenerosEntidad::Femenino => "Femenino"
    ];
  }

}
