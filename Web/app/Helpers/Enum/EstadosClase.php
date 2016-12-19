<?php

namespace App\Helpers\Enum;

class EstadosClase {

  const Programada = "PROGRAMADA";
  const Cancelada = "CANCELADA";
  const PendienteConfirmar = "PENDIENTE_CONFIRMAR";
  const Realizada = "REALIZADA";

  public static function listarSimple() {
    return [
        EstadosClase::Programada => "Programada",
        EstadosClase::Cancelada => "Cancelada",
        EstadosClase::PendienteConfirmar => "Pendiente de confirmación",
        EstadosClase::Realizada => "Realizada"
    ];
  }

  public static function listar() {
    return [
        EstadosClase::Programada => ["Programada", "label-primary"],
        EstadosClase::Cancelada => ["Cancelada", "label-danger"],
        EstadosClase::PendienteConfirmar => ["Pendiente de confirmación", "label-warning"],
        EstadosClase::Realizada => ["Realizada", "label-success"]
    ];
  }

}
