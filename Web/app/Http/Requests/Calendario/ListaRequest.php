<?php

namespace App\Http\Requests\Calendario;

use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;

class ListaRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();
    $datos["start"] = ReglasValidacion::formatoDato($datos, "start");
    $datos["end"] = ReglasValidacion::formatoDato($datos, "end");
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $reglasValidacion = [
      "start" => "required|date_format:Y-m-d",
      "end" => "required|date_format:Y-m-d"
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
