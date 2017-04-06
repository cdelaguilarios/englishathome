<?php

namespace App\Http\Requests\Alumno\Clase;

use Config;
use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;

class TotalHorarioRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();
    $datos["ids"] = ReglasValidacion::formatoDato($datos, "ids", []);
    $datos["fecha"] = ReglasValidacion::formatoDato($datos, "fecha");
    $datos["horaInicio"] = ReglasValidacion::formatoDato($datos, "horaInicio");
    $datos["duracion"] = ReglasValidacion::formatoDato($datos, "duracion");
    if (!is_array($datos["ids"])) {
      $datos["ids"] = explode(",", $datos["ids"]);
    }
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $datos = $this->all();
    $reglasValidacion = [
        "fecha" => (count($datos["ids"]) > 0 ? "" : "required|") . "date_format:d/m/Y",
        "horaInicio" => "required|numeric|between:" . ((int) Config::get("eah.minHorario") * 3600) . "," . ((int) Config::get("eah.maxHorario") * 3600),
        "duracion" => "required|numeric|between:" . ((int) Config::get("eah.minHorasClase") * 3600) . "," . ((int) Config::get("eah.maxHorasClase") * 3600)
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
          return $reglasValidacion;
        }
      default:break;
    }
  }

  public function messages() {
    return [];
  }

}
