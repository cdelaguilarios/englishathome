<?php

namespace App\Helpers\Enum;

class EstadosAlumno {

  const PorConfirmar = "POR-CONFIRMAR";
  const CuotaProgramada = "CUOTA-PROGRAMADA";
  const StandBy = "STAND-BY";
  const Activo = "ACTIVO";
  const Inactivo = "INACTIVO";

  public static function listar() {
    return [
        EstadosAlumno::PorConfirmar => ["Por confirmar", "label-primary"],
        EstadosAlumno::StandBy => ["Stand by", "label-warning"],
        EstadosAlumno::Activo => ["Activo", "label-success"],
        EstadosAlumno::CuotaProgramada => ["Cuota programada", "label-warning"],
        EstadosAlumno::Inactivo => ["Inactivo", "label-danger"]
    ];
  }

  public static function listarBusqueda() {
    $estados = EstadosAlumno::listar();
    $estadosBusqueda = [];
    foreach ($estados as $k => $v) {
      $estadosBusqueda[$k] = $v[0];
    }
    return $estadosBusqueda;
  }

  public static function listarCambio() {
    $estadosBusqueda = EstadosAlumno::listarBusqueda();
    $estadosDisponibleCambio = [EstadosAlumno::CuotaProgramada, EstadosAlumno::StandBy, EstadosAlumno::Activo, EstadosAlumno::Inactivo];
    $estadosCambio = [];
    foreach ($estadosBusqueda as $k => $v) {
      if (in_array($k, $estadosDisponibleCambio)) {
        $estadosCambio[$k] = $v;
      }
    }
    return $estadosCambio;
  }

}
