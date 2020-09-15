<?php

namespace App\Helpers\Enum;

class EstadosDocente {

  public static function listar() {
    return EstadosProfesor::listar() + EstadosPostulante::listar();
  }

  public static function listarBusqueda() {
    return EstadosProfesor::listarBusqueda() + EstadosPostulante::listarBusqueda();
  }

}
