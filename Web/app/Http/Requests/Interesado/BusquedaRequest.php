<?php

namespace App\Http\Requests\Interesado;

use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;
use App\Helpers\Enum\EstadosInteresado;

class BusquedaRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();
    $datos["estado"] = (isset($datos["estado"]) && trim($datos["estado"]) != "" ? $datos["estado"] : NULL);
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $datos = $this->all();
    $reglasValidacion = [];

    $listaEstados = EstadosInteresado::listar();
    if (!is_null($datos["estado"]) && !array_key_exists($datos["estado"], $listaEstados)) {
      $reglasValidacion["estadoNoValido"] = "required";
    }

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

  public function messages() {
    return [
        "estadoNoValido.required" => "El estado seleccionado no es válido"
    ];
  }

}