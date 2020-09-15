<?php

namespace App\Helpers\Enum;

class EstadosTarea {

  const Pendiente = "PENDIENTE";
  const EnProceso = "EN-PROCESO";
  const Realizada = "REALIZADA";

  public static function listar() {
    return [
        EstadosTarea::Pendiente => ["Pendiente", "label-primary"],
        EstadosTarea::EnProceso => ["En proceso", "label-warning"],
        EstadosTarea::Realizada => ["Realizada", "label-success"]
    ];
  }

  public static function listarBusqueda() {
    $estados = EstadosTarea::listar();
    
    $estadosBusqueda = [];
    foreach ($estados as $k => $v) {
      $estadosBusqueda[$k] = $v[0];
    }
    return $estadosBusqueda;
  }

  public static function listarDisponibleCambio() {
    $estadosBusqueda = EstadosTarea::listarBusqueda();
    $estadosDisponibleCambio = [
        EstadosTarea::Pendiente,
        EstadosTarea::EnProceso,
        EstadosTarea::Realizada
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
