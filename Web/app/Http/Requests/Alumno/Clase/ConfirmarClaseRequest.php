<?php

namespace App\Http\Requests\Alumno\Clase;

use Config;
use App\Models\Alumno;
use App\Models\Profesor;
use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;

class ConfirmarClaseRequest extends Request/* - */ {

  public function authorize()/* - */ {
    return true;
  }

  protected function getValidatorInstance()/* - */ {
    $datos = $this->all();
    $datos["idProfesor"] = ReglasValidacion::formatoDato($datos, "idProfesor");
    $datos["fecha"] = ReglasValidacion::formatoDato($datos, "fecha");
    $datos["horaInicio"] = ReglasValidacion::formatoDato($datos, "horaInicio");
    $datos["duracion"] = ReglasValidacion::formatoDato($datos, "duracion");
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $datos = $this->all();
    $reglasValidacion = [
        "fecha" => "required|date_format:d/m/Y",
        "horaInicio" => "required|numeric|between:" . ((int) Config::get("eah.minHorario") * 3600) . "," . ((int) Config::get("eah.maxHorario") * 3600),
        "duracion" => "required|numeric|between:" . ((int) Config::get("eah.minHorasClase") * 3600) . "," . ((int) Config::get("eah.maxHorasClase") * 3600)
    ];

    $idAlumno = $this->route()->getParameter('id');
    if (!Alumno::verificarExistencia($idAlumno)) {
      $reglasValidacion["alumnoNoValido"] = "required";
    }
    
    if (!Profesor::verificarExistencia($datos["idProfesor"])) {
      $reglasValidacion["profesorNoValido"] = "required";
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

  public function messages()/* - */ {
    return [
        "alumnoNoValido.required" => "El alumno seleccionado no es válido.",
        "profesorNoValido.required" => "El profesor seleccionado no es válido.",
        "codigoVerificacionNoValido.required" => "El código de verificación del alumno no es válido."
    ];
  }

}
