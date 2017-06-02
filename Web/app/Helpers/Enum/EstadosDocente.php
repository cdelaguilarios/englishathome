<?php

namespace App\Helpers\Enum;

class EstadosDocente {

  public static function listar() {
    return EstadosPostulante::listar() + EstadosProfesor::listar();
  }
  
  public static function listarBusqueda() {
    return EstadosPostulante::listarBusqueda() + EstadosProfesor::listarBusqueda();;
  }

}
