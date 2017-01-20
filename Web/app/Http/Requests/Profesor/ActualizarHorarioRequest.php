<?php

namespace App\Http\Requests\Profesor;

use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;

class ActualizarHorarioRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();
    $datos["horario"] = ReglasValidacion::formatoDato($datos, "horario");
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $datos = $this->all();
    $reglasValidacion = [];
    
    if (!ReglasValidacion::validarHorario($datos["horario"])) {
      $reglasValidacion["horarioNoValido"] = "required";
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
        "horarioNoValido.required" => "El horario seleccionado no es válido."
    ];
  }

}
