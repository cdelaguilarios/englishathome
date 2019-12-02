<?php

namespace App\Http\Requests\Historial;

use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;

class BusquedaEntidadRequest extends Request/* - */ {

  public function authorize()/* - */ {
    return true;
  }

  protected function getValidatorInstance()/* - */ {
    $datos = $this->all();
    $datos["texto"] = ReglasValidacion::formatoDato($datos, "texto");
    $datos["pagina"] = ReglasValidacion::formatoDato($datos, "pagina", 1);
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules()/* - */ {
    $reglasValidacion = [
        "texto" => "required|min:3|max:255",
        "pagina" => "required|numeric|min:1"
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
