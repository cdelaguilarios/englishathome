<?php

namespace App\Http\Requests\Interesado;

use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;

class FormularioRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();

    $datos["id"] = (isset($datos["id"]) ? $datos["id"] : 0);
    $datos["consulta"] = (isset($datos["consulta"]) ? $datos["consulta"] : NULL);

    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $reglasValidacion = [
        "nombre" => ["required", "max:255", "regex:" . ReglasValidacion::RegexAlfabetico],
        "apellido" => ["required", "max:255", "regex:" . ReglasValidacion::RegexAlfabetico],
        "telefono" => "required|max:30",
        "correoElectronico" => "required|email|max:245",
        "consulta" => "max:255",
        "cursoInteres" => "required|max:255"
    ];

    switch ($this->method()) {
      case "GET":
      case "DELETE": {
          return [];
        }
      case "POST": {
          return $reglasValidacion;
        }
      case "PUT":
      case "PATCH": {
          return $reglasValidacion;
        }
      default:break;
    }
  }

}
