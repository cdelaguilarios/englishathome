<?php

namespace App\Http\Requests\Tarea;

use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;

class ListaParaPanelRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();
    $datos["seleccionarMisTareas"] = ReglasValidacion::formatoDato($datos, "seleccionarMisTareas", "0");
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    switch ($this->method()) {
      case "GET":
      case "DELETE":
      case "PUT":
      case "PATCH":
      case "POST": {
          return [];
        }
      default:break;
    }
  }

}
