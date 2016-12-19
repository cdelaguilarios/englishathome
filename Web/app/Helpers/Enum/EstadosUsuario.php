<?php

namespace App\Helpers\Enum;

class EstadosUsuario {

  const Activo = "ACTIVO";
  const Inactivo = "INACTIVO";

  public static function listar($simple = FALSE) {
    return [
        EstadosUsuario::Activo => ($simple ? "Activo" : ["Activo", "label-success"]),
        EstadosUsuario::Inactivo => ($simple ? "Inactivo" : ["Inactivo", "label-danger"])
    ];
  }

}
