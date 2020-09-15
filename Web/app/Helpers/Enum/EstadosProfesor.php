<?php

namespace App\Helpers\Enum;

class EstadosProfesor {

  const Registrado = "REGISTRADO";
  const Activo = "ACTIVO";
  const Inactivo = "INACTIVO";
  const Vetado = "VETADO";

  public static function listar() {
    return [
        EstadosProfesor::Registrado => ["Registrado", "label-primary"],
        EstadosProfesor::Activo => ["Activo", "label-success"],
        EstadosProfesor::Inactivo => ["Inactivo", "label-warning"],
        EstadosProfesor::Vetado => ["Vetado", "label-danger"]
    ];
  }

  public static function listarBusqueda() {
    $estados = EstadosProfesor::listar();
    $estadosBusqueda = [];
    foreach ($estados as $k => $v) {
      $estadosBusqueda[$k] = $v[0];
    }
    return $estadosBusqueda;
  }

  public static function listarDisponibleCambio() {
    $estadosBusqueda = EstadosProfesor::listarBusqueda();
    $estadosDisponibleCambio = [
        EstadosProfesor::Registrado,
        EstadosProfesor::Activo,
        EstadosProfesor::Inactivo,
        EstadosProfesor::Vetado
    ];

    $estadosDisponibleCambioSel = [];
    foreach ($estadosBusqueda as $k => $v) {
      if (in_array($k, $estadosDisponibleCambio)) {
        $estadosDisponibleCambioSel[$k] = $v;
      }
    }
    return $estadosDisponibleCambioSel;
  }

}
