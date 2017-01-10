<?php

namespace App\Helpers\Enum;

class EstadosAlumno {
  
  const Nuevo = "NUEVO";
  const PendientePago = "PENDIENTE-PAGO";
  const Activo = "ACTIVO";
  const Inactivo = "INACTIVO";

  public static function listarSimple() {
    return [
        EstadosAlumno::Nuevo => "Nuevo",
        EstadosAlumno::PendientePago => "Pendiente de pago",
        EstadosAlumno::Activo => "Activo",
        EstadosAlumno::Inactivo => "Inactivo"
    ];
  }

  public static function listar() {
    return [
        EstadosAlumno::Nuevo => ["Nuevo", "label-primary"],
        EstadosAlumno::PendientePago => ["Pendiente de pago", "label-warning"],
        EstadosAlumno::Activo => ["Activo", "label-success"],
        EstadosAlumno::Inactivo => ["Inactivo", "label-danger"]
    ];
  }

}
