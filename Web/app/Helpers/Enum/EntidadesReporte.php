<?php

namespace App\Helpers\Enum;

class EntidadesReporte {

  const Interesado = "INTERESADO";
  const Alumno = "ALUMNO";
  const Postulante = "POSTULANTE";
  const Profesor = "PROFESOR";
  const Usuario = "USUARIO";
  const Curso = "CURSO";
  const Clase = "CLASE";
  const Pago = "PAGO";

  public static function listar() {
    return [
        EntidadesReporte::Interesado => ["Interesado"],
        EntidadesReporte::Alumno => ["Alumno"],
        EntidadesReporte::Postulante => ["Postulante"],
        EntidadesReporte::Profesor => ["Profesor"],
        EntidadesReporte::Usuario => ["Usuario"],
        EntidadesReporte::Curso => ["Curso"],
        EntidadesReporte::Clase => ["Clase"],
        EntidadesReporte::Pago => ["Pago"]
    ];
  }

}
