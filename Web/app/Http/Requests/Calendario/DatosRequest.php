<?php

namespace App\Http\Requests\Calendario;

use App\Models\Alumno;
use App\Models\Profesor;
use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;

class DatosRequest extends Request/* - */ {

  public function authorize()/* - */ {
    return true;
  }

  protected function getValidatorInstance()/* - */ {
    $datos = $this->all();
    $datos["tipoEntidad"] = ReglasValidacion::formatoDato($datos, "tipoEntidad", "0");
    $datos["idAlumno"] = ReglasValidacion::formatoDato($datos, "idAlumno");
    $datos["idProfesor"] = ReglasValidacion::formatoDato($datos, "idProfesor");
    $datos["start"] = ReglasValidacion::formatoDato($datos, "start");
    $datos["end"] = ReglasValidacion::formatoDato($datos, "end");
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules()/* - */ {
    $datos = $this->all();

    $reglasValidacion = [
        "start" => "required|date_format:Y-m-d",
        "end" => "required|date_format:Y-m-d"
    ];

    if ($datos["tipoEntidad"] !== "0" && !is_null($datos["idProfesor"]) && !Profesor::verificarExistencia($datos["idProfesor"])) {
      $reglasValidacion["idProfesorNoValido"] = "required";
    } else if (!is_null($datos["idAlumno"]) && !Alumno::verificarExistencia($datos["idAlumno"])) {
      $reglasValidacion["idAlumnoNoValido"] = "required";
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
        "idProfesorNoValido.required" => "El profesor seleccionado no es válido.",
        "idAlumnoNoValido.required" => "El alumno seleccionado no es válido."
    ];
  }

}
