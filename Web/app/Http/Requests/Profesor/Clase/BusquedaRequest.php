<?php

namespace App\Http\Requests\Profesor\Clase;

use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;
use App\Helpers\Enum\EstadosPago;
use App\Helpers\Enum\EstadosClase;

class BusquedaRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();
    $datos["estadoClase"] = ReglasValidacion::formatoDato($datos, "estadoClase");
    $datos["estadoPago"] = ReglasValidacion::formatoDato($datos, "estadoPago");
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $datos = $this->all();
    $reglasValidacion = [];

    $listaEstadosClase = EstadosClase::listar();
    if (!is_null($datos["estadoClase"]) && !array_key_exists($datos["estadoClase"], $listaEstadosClase)) {
      $reglasValidacion["estadoClaseNoValido"] = "required";
    }
    $listaEstadosPago = EstadosPago::listar();
    if (!is_null($datos["estadoPago"]) && !array_key_exists($datos["estadoPago"], $listaEstadosPago)) {
      $reglasValidacion["estadoPagoNoValido"] = "required";
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
        "estadoClaseNoValido.required" => "El estado de clase seleccionado no es válido.",
        "estadoPagoNoValido.required" => "El estado de pago seleccionado no es válido."
    ];
  }

}
