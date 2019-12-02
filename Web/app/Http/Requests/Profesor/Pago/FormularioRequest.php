<?php

namespace App\Http\Requests\Profesor\Pago;

use App\Http\Requests\Request;
use App\Helpers\Enum\EstadosPago;
use App\Helpers\ReglasValidacion;

class FormularioRequest extends Request/* - */ {

  public function authorize()/* - */ {
    return true;
  }

  protected function getValidatorInstance()/* - */ {
    $datos = $this->all();
    $datos["fecha"] = ReglasValidacion::formatoDato($datos, "fecha");
    $datos["estado"] = ReglasValidacion::formatoDato($datos, "estado");
    $datos["descripcion"] = ReglasValidacion::formatoDato($datos, "descripcion");
    $datos["imagenComprobante"] = ReglasValidacion::formatoDato($datos, "imagenComprobante");
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules()/* - */ {
    $datos = $this->all();
    
    $reglasValidacion = [
        "fecha" => "required|date_format:d/m/Y",
        "descripcion" => "max:255",
        "imagenComprobante" => "image",
        "monto" => ["required", "regex:" . ReglasValidacion::RegexDecimal]
    ];

    $listaEstadosPago = EstadosPago::listar();
    if (!array_key_exists($datos["estado"], $listaEstadosPago)) {
      $reglasValidacion["estadoNoValido"] = "required";
    }

    switch ($this->method()) {
      case "GET":
      case "DELETE": {
          return [];
        }
      case "POST":
      case "PUT":
      case "PATCH": {
          return $reglasValidacion;
        }
      default:break;
    }
  }

  public function messages()/* - */ {
    return [
        "estadoNoValido.required" => "El estado seleccionado del pago no es válido."
    ];
  }

}
