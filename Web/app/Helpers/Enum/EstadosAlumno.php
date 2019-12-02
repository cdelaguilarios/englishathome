<?php

namespace App\Helpers\Enum;

class EstadosAlumno/* - */ {

  const PorConfirmar = "POR-CONFIRMAR";
  const Activo = "ACTIVO";
  const PeriodoConcluido = "PERIODO-CONCLUIDO";
  const PeriodoTrunco = "PERIODO-TRUNCO";
  const StandBy = "STAND-BY";
  const Inactivo = "INACTIVO";

  public static function listar()/* - */ {
    return [
        EstadosAlumno::PorConfirmar => ["Por confirmar", "label-primary"],
        EstadosAlumno::Activo => ["Activo", "label-success"],
        EstadosAlumno::PeriodoConcluido => ["Período concluido", "label-warning"],
        EstadosAlumno::PeriodoTrunco => ["Período trunco", "label-warning"],
        EstadosAlumno::StandBy => ["Stand by", "label-warning"],
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
    $estadosDisponibleCambio = [
        EstadosAlumno::Activo,
        EstadosAlumno::PeriodoConcluido,
        EstadosAlumno::PeriodoTrunco,
        EstadosAlumno::StandBy,
        EstadosAlumno::Inactivo
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
