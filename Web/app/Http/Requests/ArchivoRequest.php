<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;

class ArchivoRequest extends Request/* - */ {

  public function authorize()/* - */ {
    return true;
  }

  protected function getValidatorInstance()/* - */ {
    $datos = $this->all();
    $datos["idElemento"] = ReglasValidacion::formatoDato($datos, "idElemento");
    $datos["archivo"] = ReglasValidacion::formatoDato($datos, "archivo");
    $datos["nombre"] = ReglasValidacion::formatoDato($datos, "nombre");
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules()/* - */ {
    switch ($this->method()) {
      case "GET":
      case "PUT":
      case "PATCH": {
          return [];
        }
      case "POST": {
          return ["idElemento" => "required", "archivo" => "required|file"];
        }
      case "DELETE": {
          return ["nombre" => "required"];
        }
      default:break;
    }
  }

}
