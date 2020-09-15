<?php

namespace App\Http\Requests\Horario;

use App\Http\Requests\Request;

class HorarioMultipleRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();
    if (isset($datos["idsEntidades"]) && !is_array($datos["idsEntidades"])) {
      $datos["idsEntidades"] = explode(",", $datos["idsEntidades"]);
    }
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    switch ($this->method()) {
      case "GET":
      case "DELETE":
      case "PUT":
      case "PATCH": {
          return [];
        }
      case "POST": {
          return [
              "idsEntidades" => "required"
          ];
        }
      default:break;
    }
  }

}
