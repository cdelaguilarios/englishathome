<?php

namespace App\Helpers\Enum;

class EstadosInteresado {

  const Pendiente = "PENDIENTE";
  const PorConfirmar = "PORCONFIRMAR";

  public static function listar() {
    return [
        EstadosInteresado::Pendiente => ["Pendiente", "label-warning"],
        EstadosInteresado::PorConfirmar => ["Por confirmar", "label-info"]
    ];
  }

}
