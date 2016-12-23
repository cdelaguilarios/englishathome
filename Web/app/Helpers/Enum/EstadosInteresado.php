<?php

namespace App\Helpers\Enum;

class EstadosInteresado {

  const PendienteInformacion = "PENDIENTE-INFORMACION";
  const PorConfirmar = "PORCONFIRMAR";

  public static function listarSimple() {
    return [
        EstadosInteresado::PendienteInformacion => "Pendiente de información",
        EstadosInteresado::PorConfirmar => "Por confirmar"
    ];
  }
  
  public static function listar() {
    return [
        EstadosInteresado::PendienteInformacion => ["Pendiente de información", "label-warning"],
        EstadosInteresado::PorConfirmar => ["Por confirmar", "label-info"]
    ];
  }

}
