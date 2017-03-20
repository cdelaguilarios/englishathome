<?php

namespace App\Http\Requests\Alumno\Clase;

use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;

class DatosGrupoRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();
    $datos["ids"] = ReglasValidacion::formatoDato($datos, "ids", []);
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $reglasValidacion = [];

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

  public function messages() {
    return [];
  }

}
