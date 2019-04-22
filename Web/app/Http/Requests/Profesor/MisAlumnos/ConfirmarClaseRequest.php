<?php

namespace App\Http\Requests\Profesor\MisAlumnos;

use Config;
use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;

class ConfirmarClaseRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();
    $datos["idClase"] = ReglasValidacion::formatoDato($datos, "idClase");
    $datos["duracion"] = ReglasValidacion::formatoDato($datos, "duracion");
    $datos["comentario"] = ReglasValidacion::formatoDato($datos, "comentario");
    $datos["codigoVerificacion"] = ReglasValidacion::formatoDato($datos, "codigoVerificacion");
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $reglasValidacion = [
        "idClase" => "required",
        "duracion" => "required|numeric|between:" . ((int) Config::get("eah.minHorasClase") * 3600) . "," . ((int) Config::get("eah.maxHorasClase") * 3600),
        "codigoVerificacion" => "required|min:4|max:6",
        "comentario" => "max:8000"
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
