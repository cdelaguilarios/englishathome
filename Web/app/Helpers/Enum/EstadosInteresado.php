<?php

namespace App\Helpers\Enum;

class EstadosInteresado {

  const PendienteInformacion = "PENDIENTE-INFORMACION";
  const NoContesta = "NO-CONTESTA";
  const NoInteresado = "NO-INTERESADO";
  const ContactoAFuturo = "CONTACTO-A-FUTURO";
  const Seguimiento = "SEGUIMIENTO";
  const CotizacionEnviada = "COTIZACION-ENVIADA";
  const AlumnoRegistrado = "ALUMNO-REGISTRADO";

  public static function listar() {
    return [
        EstadosInteresado::PendienteInformacion => ["Pendiente de información", "label-warning"],        
        EstadosInteresado::NoContesta => ["No contesta", "label-warning"],
        EstadosInteresado::NoInteresado => ["No interesado", "label-warning"],
        EstadosInteresado::ContactoAFuturo => ["Contacto a futuro", "label-info"],
        EstadosInteresado::Seguimiento => ["Seguimiento", "label-info"],        
        EstadosInteresado::CotizacionEnviada => ["Cotización enviada", "label-primary"],
        EstadosInteresado::AlumnoRegistrado => ["Alumno registrado", "label-success"]
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

  public static function listarCambio() {
    $estadosBusqueda = EstadosInteresado::listarBusqueda();
    $estadosDisponibleCambio = [EstadosInteresado::PendienteInformacion, EstadosInteresado::NoContesta, EstadosInteresado::NoInteresado, EstadosInteresado::ContactoAFuturo, EstadosInteresado::Seguimiento, EstadosInteresado::CotizacionEnviada];
    $estadosCambio = [];
    foreach ($estadosBusqueda as $k => $v) {
      if (in_array($k, $estadosDisponibleCambio)) {
        $estadosCambio[$k] = $v;
      }
    }
    return $estadosCambio;
  }

}
