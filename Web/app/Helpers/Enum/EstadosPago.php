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

  public static function listarCambio() {
    $estados = EstadosPago::listar();
    $estadosCambio = [];
    foreach ($estados as $k => $v) {
      $estadosCambio[$k] = $v[0];
    }
    return $estadosCambio;
  }

}
