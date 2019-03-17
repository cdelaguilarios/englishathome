<?php

namespace App\Helpers\Enum;

class RolesUsuario {

  const Principal = "PRINCIPAL";
  const Secundario = "SECUNDARIO";
  const Alumno = "ALUMNO";
  const Profesor = "PROFESOR";

  public static function listar() {
    return [
        RolesUsuario::Principal => "Principal",
        RolesUsuario::Secundario => "Secundario",
        RolesUsuario::Alumno => "Alumno",
        RolesUsuario::Profesor => "Profesor"
    ];
  }

  public static function listarDelSistema() {
    return [
        RolesUsuario::Principal => "Principal",
        RolesUsuario::Secundario => "Secundario"
    ];
  }

}
