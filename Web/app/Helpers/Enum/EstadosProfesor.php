<?php

namespace App\Helpers\Enum;

class EstadosProfesor {

  const Registrado = "REGISTRADO";
  const Activo = "ACTIVO";
  const Inactivo = "INACTIVO";

  public static function listar() {
    return [
        EstadosProfesor::Registrado => ["Registrado", "label-primary"],
        EstadosProfesor::Activo => ["Activo", "label-success"],
        EstadosProfesor::Inactivo => ["Inactivo", "label-warning"]
    ];
  }

}