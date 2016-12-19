<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;

class InteresadoRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $data = $this->all();

    $data["id"] = (isset($data["id"]) ? $data["id"] : 0);
    $data["consulta"] = (isset($data["consulta"]) ? $data["consulta"] : NULL);

    $this->getInputSource()->replace($data);
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
