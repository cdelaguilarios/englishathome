<?php

namespace App\Helpers\Enum;

class EstadosPago {

  const Realizado = "REALIZADO";
  const Pendiente = "PENDIENTE";

  public static function listar() {
    return [
        EstadosPago::Realizado => ["Realizado", "label-success"],
        EstadosPago::Pendiente => ["Pendiente", "label-warning"]
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
