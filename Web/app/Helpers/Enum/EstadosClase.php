<?php

namespace App\Helpers\Enum;

class EstadosClase {

  const Programada = "PROGRAMADA";
  const Cancelada = "CANCELADA";
  const PendienteConfirmar = "PENDIENTE_CONFIRMAR";
  const ConfirmadaProfesorAlumno = "CONFIRMADA_PROFESOR_ALUMNO";
  const Realizada = "REALIZADA";

  public static function listar()/* - */ {
    return [
        EstadosClase::Programada => ["Programada", "label-primary", "#3c8dbc"],
        EstadosClase::Cancelada => ["Cancelada", "label-danger", "#dd4b39"],
        EstadosClase::PendienteConfirmar => ["Pendiente de confirmación", "label-warning", "#f39c12"],
        EstadosClase::ConfirmadaProfesorAlumno => ["Confirmada Profesor-Alumno", "label-info", "#39cccc"],
        EstadosClase::Realizada => ["Realizada", "label-success", "#00a65a"]
    ];
  }

  public static function listarBusqueda()/* - */ {
    $estados = EstadosClase::listar();
    $estadosBusqueda = [];
    foreach ($estados as $k => $v) {
      $estadosBusqueda[$k] = $v[0];
    }
    return $estadosBusqueda;
  }

  public static function listarDisponibleCambio()/* - */ {
    $estadosBusqueda = EstadosClase::listarBusqueda();
    $estadosDisponibleCambio = [EstadosClase::Programada, EstadosClase::PendienteConfirmar, EstadosClase::ConfirmadaProfesorAlumno, EstadosClase::Realizada];
    $estadosDisponibleCambioSel = [];
    foreach ($estadosBusqueda as $k => $v) {
      if (in_array($k, $estadosDisponibleCambio)) {
        $estadosDisponibleCambioSel[$k] = $v;
      }
    }
    return $estadosDisponibleCambioSel;
  }

}
