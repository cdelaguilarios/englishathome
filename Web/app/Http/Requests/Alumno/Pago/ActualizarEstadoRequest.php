<?php

namespace App\Http\Requests\Alumno\Pago;

use App\Models\PagoAlumno;
use App\Http\Requests\Request;
use App\Helpers\Enum\EstadosPago;

class ActualizarEstadoRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();
    $datos["idPago"] = (isset($datos["idPago"]) && $datos["idPago"] != "" ? $datos["idPago"] : NULL);
    $datos["idAlumno"] = (isset($datos["idAlumno"]) && $datos["idAlumno"] != "" ? $datos["idAlumno"] : NULL);
    $data["estado"] = (isset($data["estado"]) ? $data["estado"] : NULL);

    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $datos = $this->all();
    $reglasValidacion = [];


    if (!is_null($datos["idPago"]) && !is_null($datos["idAlumno"]) && !PagoAlumno::verificarExistencia($datos["idAlumno"], $datos["idPago"])) {
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
        "pagoNoValida.required" => "El pago seleccionado no es válido",
        "estadoNoValido.required" => "El estado seleccionado no es válido"
    ];
  }

}
