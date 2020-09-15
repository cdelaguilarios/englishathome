<?php

namespace App\Http\Requests\Tarea;

use App\Http\Requests\Request;

class ActualizarRevisionRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();
    if (isset($datos["idsTareas"]) && !is_array($datos["idsTareas"])) {
      $datos["idsTareas"] = explode(",", $datos["idsTareas"]);
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
              "idsTareas" => "required"
          ];
        }
      default:break;
    }
  }

}
