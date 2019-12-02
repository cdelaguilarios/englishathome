<?php

namespace App\Helpers\Enum;

class EstadosPostulante/* - */ {

  const Registrado = "REGISTRADO";
  const RegistradoExterno = "REGISTRADO-EXTERNO";
  const Activo = "ACTIVO";
  const Inactivo = "INACTIVO";
  const ProfesorRegistrado = "PROFESOR-REGISTRADO";
  const Vetado = "VETADO";

  public static function listar()/* - */ {
    return [
        EstadosPostulante::Registrado => ["Registrado", "label-primary"],
        EstadosPostulante::RegistradoExterno => ["Registrado externo", "label-primary"],
        EstadosPostulante::Activo => ["Activo", "label-info"],
        EstadosPostulante::Inactivo => ["Inactivo", "label-warning"],
        EstadosPostulante::ProfesorRegistrado => ["Profesor registrado", "label-success"],
        EstadosPostulante::Vetado => ["Vetado", "label-danger"]
    ];
  }

  public static function listarBusqueda()/* - */ {
    $estados = EstadosPostulante::listar();
    $estadosBusqueda = [];
    foreach ($estados as $k => $v) {
      $estadosBusqueda[$k] = $v[0];
    }
    return $estadosBusqueda;
  }

  public static function listarDisponibleCambio()/* - */ {
    $estadosBusqueda = EstadosPostulante::listarBusqueda();
    $estadosDisponibleCambio = [
        EstadosPostulante::Registrado,
        EstadosPostulante::RegistradoExterno,
        EstadosPostulante::Activo,
        EstadosPostulante::Inactivo,
        EstadosPostulante::Vetado
    ];

    $estadosDisponibleCambioSel = [];
    foreach ($estadosBusqueda as $k => $v) {
      if (in_array($k, $estadosDisponibleCambio)) {
        $estadosDisponibleCambioSel[$k] = $v;
      }
    }
    return $estadosDisponibleCambioSel;
  }

}
