<?php

namespace App\Helpers\Enum;

class EstadosPostulante {

  const Registrado = "REGISTRADO";
  const Activo = "ACTIVO";
  const Inactivo = "INACTIVO";
  const ProfesorRegistrado = "PROFESOR-REGISTRADO";

  public static function listarSimple() {
    return [
        EstadosPostulante::Registrado => "Registrado",
        EstadosPostulante::Activo => "Activo",
        EstadosPostulante::Inactivo => "Inactivo"
    ];
  }

  public static function listar() {
    return [
        EstadosPostulante::Registrado => ["Registrado", "label-primary"],
        EstadosPostulante::Activo => ["Activo", "label-info"],
        EstadosPostulante::Inactivo => ["Inactivo", "label-warning"],
        EstadosPostulante::ProfesorRegistrado => ["Profesor registrado", "label-success"]
    ];
  }

}
