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

}
