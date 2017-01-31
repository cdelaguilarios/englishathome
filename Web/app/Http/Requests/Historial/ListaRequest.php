<?php

namespace App\Http\Requests\Historial;

use App\Http\Requests\Request;

class ListaRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $data = $this->all();
    $data["numeroCarga"] = (isset($data["numeroCarga"]) ? $data["numeroCarga"] : 0);
    $this->getInputSource()->replace($data);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $reglasValidacion = [
        "numeroCarga" => "required|numeric"
    ];

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

}
