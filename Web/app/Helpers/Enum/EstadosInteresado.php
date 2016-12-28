<?php

namespace App\Helpers\Enum;

class EstadosInteresado {

  const PendienteInformacion = "PENDIENTE-INFORMACION";
  const Contactado = "CONTACTADO";
  const CotizacionEnviada = "COTIZACION-ENVIADA";
  const AlumnoRegistrado = "ALUMNO-REGISTRADO";

  public static function listarSimple() {
    return [
        EstadosInteresado::PendienteInformacion => "Pendiente de información",
        EstadosInteresado::Contactado => "Contactado",
        EstadosInteresado::CotizacionEnviada => "Cotización enviada",
        EstadosInteresado::AlumnoRegistrado => "Alumno registrado"
    ];
  }

  public static function listar() {
    return [
        EstadosInteresado::PendienteInformacion => ["Pendiente de información", "label-warning"],
        EstadosInteresado::Contactado => ["Contactado", "label-info"],
        EstadosInteresado::CotizacionEnviada => ["Cotización enviada", "label-primary"],
        EstadosInteresado::AlumnoRegistrado => ["Alumno registrado", "label-success"]
    ];
  }

}
