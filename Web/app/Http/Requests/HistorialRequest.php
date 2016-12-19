<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class HistorialRequest extends Request {

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
      case "DELETE": {
          return [];
        }
      case "POST": {
          return $reglasValidacion;
        }
      case "PUT":
      case "PATCH": {
          return [];
        }
      default:break;
    }
  }

}
