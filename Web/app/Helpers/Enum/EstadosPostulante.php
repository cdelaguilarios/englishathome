<?php

namespace App\Helpers\Enum;

class EstadosPostulante {

  const Registrado = "REGISTRADO";
  const Activo = "ACTIVO";
  const Inactivo = "INACTIVO";
  const ProfesorRegistrado = "PROFESOR-REGISTRADO";

  public static function listar() {
    return [
        EstadosPostulante::Registrado => ["Registrado", "label-primary"],
        EstadosPostulante::Activo => ["Activo", "label-info"],
        EstadosPostulante::Inactivo => ["Inactivo", "label-warning"],
        EstadosPostulante::ProfesorRegistrado => ["Profesor registrado", "label-success"]
    ];
  }

  public static function listarBusqueda() {
    $estados = EstadosPostulante::listar();
    $estadosBusqueda = [];
    foreach ($estados as $k => $v) {
      $estadosBusqueda[$k] = $v[0];
    }
    return $estadosBusqueda;
  }

  public static function listarCambio() {
    $estadosBusqueda = EstadosPostulante::listarBusqueda();
    $estadosDisponibleCambio = [EstadosPostulante::Registrado, EstadosPostulante::Activo, EstadosPostulante::Inactivo];
    $estadosCambio = [];
    foreach ($estadosBusqueda as $k => $v) {
      if (in_array($k, $estadosDisponibleCambio)) {
        $estadosCambio[$k] = $v;
      }
    }
    return $estadosCambio;
  }

}
