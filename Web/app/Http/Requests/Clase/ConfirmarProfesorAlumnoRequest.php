<?php

namespace App\Http\Requests\Clase;

use Config;
use App\Models\Alumno;
use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;

class ConfirmarProfesorAlumnoRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();
    $datos["duracion"] = ReglasValidacion::formatoDato($datos, "duracion");
    $datos["idAlumno"] = ReglasValidacion::formatoDato($datos, "idAlumno");
    $datos["codigoVerificacionClases"] = ReglasValidacion::formatoDato($datos, "codigoVerificacionClases");
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $datos = $this->all();
    $reglasValidacion = [
        "duracion" => "required|numeric|between:" . ((int) Config::get("eah.minHorasClase") * 3600) . "," . ((int) Config::get("eah.maxHorasClase") * 3600),
        "codigoVerificacionClases" => "min:4|max:6"
    ];

    if (Alumno::verificarExistencia($datos["idAlumno"])) {
      $alumno = Alumno::obtenerXId($datos["idAlumno"], TRUE);
      if ($alumno->codigoVerificacionClases != $datos["codigoVerificacionClases"]) {
        $reglasValidacion["codigoVerificacionClasesNoValido"] = "required";
      }
    } else {
      $reglasValidacion["alumnoNoValido"] = "required";
    }

    switch ($this->method()) {
      case "GET":
      case "DELETE":
      case "PUT":
      case "PATCH": {
          return [];
        }
      case "POST": {
          return $reglasValidacion;
        }
      default:break;
    }
  }

  public function messages() {
    return [
        "alumnoNoValido.required" => "El alumno seleccionado no es válido.",
        "codigoVerificacionClasesNoValido.required" => "El código de verificación del alumno no es válido."
    ];
  }

}
