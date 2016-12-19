<?php

namespace App\Helpers\Enum;

class RolesUsuario {

  const Principal = "PRINCIPAL";
  const Secundario = "SECUNDARIO";

  public static function Listar() {
    return [
        RolesUsuario::Principal => "Principal",
        RolesUsuario::Secundario => "Secundario"
    ];
  }

}
