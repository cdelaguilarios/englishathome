<?php

namespace App\Helpers\Enum;

class EstadosUsuario {

  const Activo = "ACTIVO";
  const Inactivo = "INACTIVO";

  public static function listarSimple() {
    return [
        EstadosUsuario::Activo => "Activo",
        EstadosUsuario::Inactivo => "Inactivo"
    ];
  }

  public static function listar($simple = FALSE) {
    return [
        EstadosUsuario::Activo => ($simple ? "Activo" : ["Activo", "label-success"]),
        EstadosUsuario::Inactivo => ($simple ? "Inactivo" : ["Inactivo", "label-danger"])
    ];
  }

  public static function listarBusqueda() {
    $estados = EstadosUsuario::listar();
    $estadosBusqueda = [];
    foreach ($estados as $k => $v) {
      $estadosBusqueda[$k] = $v[0];
    }
    return $estadosBusqueda;
  }

  public static function listarDisponibleCambio() {
    $estadosBusqueda = EstadosUsuario::listarBusqueda();
    $estadosDisponibleCambio = [EstadosUsuario::Activo, EstadosUsuario::Inactivo];
    $estadosDisponibleCambioSel = [];
    foreach ($estadosBusqueda as $k => $v) {
      if (in_array($k, $estadosDisponibleCambio)) {
        $estadosDisponibleCambioSel[$k] = $v;
      }
    }
    return $estadosDisponibleCambioSel;
  }

}
