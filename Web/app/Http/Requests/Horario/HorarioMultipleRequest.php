<?php

namespace App\Http\Requests\Horario;

use App\Http\Requests\Request;

class HorarioMultipleRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();
    if (!is_array($datos["idsEntidades"])) {
      $datos["idsEntidades"] = explode(",", $datos["idsEntidades"]);
    }
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $datos = $this->all();
    $reglasValidacion = [];

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
