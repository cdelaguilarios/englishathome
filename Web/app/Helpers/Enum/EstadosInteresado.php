<?php

namespace App\Helpers\Enum;

class EstadosInteresado {

  const PendienteInformacion = "PENDIENTE-INFORMACION";
  const Seguimiento = "SEGUIMIENTO";
  const MuyInteresado = "MUY-INTERESADO";
  const PorCerrar = "POR-CERRAR";
  const NoInteresado = "NO-INTERESADO";
  const FichaCompleta = "FICHA-COMPLETA";
  const AlumnoRegistrado = "ALUMNO-REGISTRADO";

  public static function listar() {
    return [
        EstadosInteresado::PendienteInformacion => ["Pendiente de información", "label-warning"],
        EstadosInteresado::Seguimiento => ["Seguimiento", "label-info"],
        EstadosInteresado::MuyInteresado => ["Muy interesado", "label-info"],
        EstadosInteresado::PorCerrar => ["Por cerrar", "label-info"],
        EstadosInteresado::NoInteresado => ["No interesado", "label-warning"],
        EstadosInteresado::FichaCompleta => ["Ficha completa", "label-primary"],
    ];
  }

  public static function listarBusqueda() {
    $estados = EstadosInteresado::listar();
    $estadosBusqueda = [];
    foreach ($estados as $k => $v) {
      $estadosBusqueda[$k] = $v[0];
    }
    return $estadosBusqueda;
  }

  public static function listarDisponibleCambio() {
    $estadosBusqueda = EstadosInteresado::listarBusqueda();
    $estadosDisponibleCambio = [
        EstadosInteresado::PendienteInformacion,
        EstadosInteresado::Seguimiento,
        EstadosInteresado::MuyInteresado,
        EstadosInteresado::PorCerrar,
        EstadosInteresado::NoInteresado
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
