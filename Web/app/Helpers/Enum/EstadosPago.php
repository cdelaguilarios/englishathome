<?php

namespace App\Helpers\Enum;

class EstadosPago/* - */ {

  const Realizado = "REALIZADO";
  const Pendiente = "PENDIENTE";
  const Consumido = "CONSUMIDO";//Estado especial para pagos que ya no deban ser considerados en una bolsa de horas

  public static function listarSimple()/* - */ {
    return [
        EstadosPago::Consumido => "Consumido",
        EstadosPago::Realizado => "Realizado",
        EstadosPago::Pendiente => "Pendiente"
    ];
  }

  public static function listar()/* - */ {
    return [
        EstadosPago::Pendiente => ["Pendiente", "label-warning", "#f39c12"],
        EstadosPago::Realizado => ["Realizado", "label-success", "#00a65a"],
        EstadosPago::Consumido => ["Consumido", "label-primary", "#00a65a"]
    ];
  }

  public static function listarBusqueda()/* - */ {
    $estados = EstadosPago::listar();
    
    $estadosBusqueda = [];
    foreach ($estados as $k => $v) {
      $estadosBusqueda[$k] = $v[0];
    }
    return $estadosBusqueda;
  }
  
  public static function listarDisponibleCambio()/* - */ {
    $estadosBusqueda = EstadosPago::listarBusqueda();
    $estadosDisponibleCambio = [
        EstadosPago::Pendiente,
        EstadosPago::Realizado,
        EstadosPago::Consumido
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
