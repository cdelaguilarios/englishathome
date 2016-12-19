<?php

namespace App\Helpers\Enum;

class TiposCancelacionClase {

  const CancelacionAlumno = "CANCELACION_ALUMNO";
  const CancelacionProfesor = "CANCELACION_PROFESOR";

  public static function listar() {
    return [
        TiposCancelacionClase::CancelacionAlumno => "Clase cancelada por el alumno",
        TiposCancelacionClase::CancelacionProfesor => "Clase cancelada por el profesor",
    ];
  }

}
