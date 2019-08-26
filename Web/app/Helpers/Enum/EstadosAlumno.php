<?php

namespace App\Helpers\Enum;

class EstadosAlumno {

  const PorConfirmar = "POR-CONFIRMAR";
  const CuotaProgramada = "CUOTA-PROGRAMADA";
  const StandBy = "STAND-BY";
  const PeriodoTrunco = "PERIODO-TRUNCO";
  const Activo = "ACTIVO";
  const Inactivo = "INACTIVO";

  public static function listar()/* - */ {
    return [
        EstadosAlumno::PorConfirmar => ["Por confirmar", "label-primary"],
        EstadosAlumno::StandBy => ["Stand by", "label-warning"],
        EstadosAlumno::PeriodoTrunco => ["Período trunco", "label-warning"],
        EstadosAlumno::Activo => ["Activo", "label-success"],
        EstadosAlumno::CuotaProgramada => ["Período concluido", "label-warning"],
        EstadosAlumno::Inactivo => ["Inactivo", "label-danger"]
    ];
  }

  public static function listarBusqueda()/* - */ {
    $estados = EstadosAlumno::listar();
    $estadosBusqueda = [];
    foreach ($estados as $k => $v) {
      $estadosBusqueda[$k] = $v[0];
    }
    return $estadosBusqueda;
  }

  public static function listarDisponibleCambio()/* - */ {
    $estadosBusqueda = EstadosAlumno::listarBusqueda();
    $estadosDisponibleCambio = [EstadosAlumno::StandBy, EstadosAlumno::PeriodoTrunco, EstadosAlumno::Activo, EstadosAlumno::CuotaProgramada, EstadosAlumno::Inactivo];
    $estadosDisponibleCambioSel = [];
    foreach ($estadosBusqueda as $k => $v) {
      if (in_array($k, $estadosDisponibleCambio)) {
        $estadosDisponibleCambioSel[$k] = $v;
      }
    }
    return $estadosDisponibleCambioSel;
  }

}
