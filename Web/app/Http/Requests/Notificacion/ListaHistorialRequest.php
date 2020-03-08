<?php

namespace App\Http\Requests\Notificacion;

use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;

class ListaHistorialRequest extends Request/* - */ {

  public function authorize()/* - */ {
    return true;
  }

  protected function getValidatorInstance()/* - */ {
    $datos = $this->all();
    $datos["numeroCarga"] = ReglasValidacion::formatoDato($datos, "numeroCarga", 0);
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules()/* - */ {
    $reglasValidacion = [
        "numeroCarga" => "required|numeric"
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
