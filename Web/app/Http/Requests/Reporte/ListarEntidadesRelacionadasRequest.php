<?php

namespace App\Http\Requests\Reporte;

use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;
use App\Helpers\Enum\TiposEntidad;

class ListarEntidadesRelacionadasRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();
    $datos["entidad"] = ReglasValidacion::formatoDato($datos, "entidad");
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $datos = $this->all();
    $reglasValidacion = [];

    $listaTiposEntidad = TiposEntidad::listar();
    if (!(!is_null($datos["entidad"]) && (array_key_exists($datos["entidad"], $listaTiposEntidad)))) {
      $reglasValidacion["entidadNoValida"] = "required";
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
        "entidadNoValida.required" => "La entidad seleccionada no es válida."
    ];
  }

}
