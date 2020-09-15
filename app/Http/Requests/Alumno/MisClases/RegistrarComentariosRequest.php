<?php

namespace App\Http\Requests\Alumno\MisClases;

use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;

class RegistrarComentariosRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();
    $datos["idClase"] = ReglasValidacion::formatoDato($datos, "idClase");
    $datos["comentario"] = ReglasValidacion::formatoDato($datos, "comentario");
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $reglasValidacion = [
        "idClase" => "required",
        "comentario" => "required|max:8000"
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
