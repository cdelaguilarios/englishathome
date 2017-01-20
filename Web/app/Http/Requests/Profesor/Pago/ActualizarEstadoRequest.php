<?php

namespace App\Http\Requests\Profesor\Pago;

use App\Models\PagoProfesor;
use App\Http\Requests\Request;
use App\Helpers\Enum\EstadosPago;
use App\Helpers\ReglasValidacion;

class ActualizarEstadoRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();
    $datos["idPago"] = ReglasValidacion::formatoDato($datos, "idPago");
    $datos["idProfesor"] = ReglasValidacion::formatoDato($datos, "idProfesor");
    $datos["estado"] = ReglasValidacion::formatoDato($datos, "estado");
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $datos = $this->all();
    $reglasValidacion = [];

    if (!PagoProfesor::verificarExistencia($datos["idProfesor"], $datos["idPago"])) {
      $reglasValidacion["pagoNoValido"] = "required";
    }
    $estados = EstadosPago::listarSimple();
    if (!array_key_exists($datos["estado"], $estados)) {
      $reglasValidacion["estadoNoValido"] = "required";
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
        "pagoNoValido.required" => "El pago seleccionado no es válido.",
        "estadoNoValido.required" => "El estado seleccionado no es válido."
    ];
  }

}
