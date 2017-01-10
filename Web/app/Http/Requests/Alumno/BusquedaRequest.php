<?php

namespace App\Http\Requests\Alumno;

use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;
use App\Helpers\Enum\EstadosAlumno;

class BusquedaRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();
    $datos["estado"] = ReglasValidacion::formatoDato($datos, "estado");
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $datos = $this->all();
    $reglasValidacion = [];

    $listaEstados = EstadosAlumno::listar();
    if (!is_null($datos["estado"]) && !array_key_exists($datos["estado"], $listaEstados)) {
      $reglasValidacion["estadoNoValido"] = "required";
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
        "estadoNoValido.required" => "El estado seleccionado no es válido."
    ];
  }

}
