<?php

namespace App\Helpers\Enum;

class EstadosAlumno {

  const Registrado = "REGISTRADO";
  const Activo = "ACTIVO";
  const Inactivo = "INACTIVO";

  public static function listar() {
    return [
        EstadosAlumno::Registrado => ["Registrado", "label-primary"],
        EstadosAlumno::Activo => ["Activo", "label-success"],
        EstadosAlumno::Inactivo => ["Inactivo", "label-warning"]
    ];
  }

}
