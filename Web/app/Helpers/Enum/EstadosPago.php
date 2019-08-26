<?php

namespace App\Helpers\Enum;

class EstadosPago {

  const Realizado = "REALIZADO";
  const Pendiente = "PENDIENTE";

  public static function listarSimple() {
    return [
        EstadosPago::Realizado => "Realizado",
        EstadosPago::Pendiente => "Pendiente"
    ];
  }

  public static function listar() {
    return [
        EstadosPago::Realizado => ["Realizado", "label-success", "#00a65a"],
        EstadosPago::Pendiente => ["Pendiente", "label-warning", "#f39c12"]
    ];
  }

  public static function listarDisponibleCambio() {
    $estados = EstadosPago::listar();
    $estadosDisponibleCambio = [];
    foreach ($estados as $k => $v) {
      $estadosDisponibleCambio[$k] = $v[0];
    }
    return $estadosDisponibleCambio;
  }

}
