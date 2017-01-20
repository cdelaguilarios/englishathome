<?php

namespace App\Http\Requests\Clase;

use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;
use App\Helpers\Enum\EstadosClase;

class BusquedaRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();
    $datos["estado"] = ReglasValidacion::formatoDato($datos, "estado");
    $datos["fechaInicio"] = ReglasValidacion::formatoDato($datos, "fechaInicio");
    $datos["fechaFin"] = ReglasValidacion::formatoDato($datos, "fechaFin");
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $datos = $this->all();
    $reglasValidacion = [];

    $listaEstados = EstadosClase::listar();
    if (!is_null($datos["estado"]) && !array_key_exists($datos["estado"], $listaEstados)) {
      $reglasValidacion["estadoNoValido"] = "required";
    }    
    if(!preg_match(ReglasValidacion::RegexFecha, $datos["fechaInicio"]) || !preg_match(ReglasValidacion::RegexFecha, $datos["fechaFin"])){
      $datos["fechaInicio"] = NULL;
      $datos["fechaFin"] = NULL;
    }

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
    return [
        "estadoNoValido.required" => "El estado seleccionado no es válido."
    ];
  }

}
