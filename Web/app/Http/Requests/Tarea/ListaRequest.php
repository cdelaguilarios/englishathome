<?php

namespace App\Http\Requests\Tarea;

use App\Helpers\Util;
use App\Http\Requests\Request;
use App\Helpers\Enum\TiposBusquedaFecha;

class ListaRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();
    Util::preProcesarFiltrosBusquedaXFechas($datos);    
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $datos = $this->all();
    $reglasValidacion = [];

    $listaTiposBusquedaFecha = TiposBusquedaFecha::listar();
    if (!array_key_exists($datos["tipoBusquedaFecha"], $listaTiposBusquedaFecha)) {
      $reglasValidacion["tipoBusquedaFechaNoValido"] = "required";
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
        "tipoBusquedaFechaNoValido.required" => "El tipo de búsqueda de la fecha no es válido."
    ];
  }

}
