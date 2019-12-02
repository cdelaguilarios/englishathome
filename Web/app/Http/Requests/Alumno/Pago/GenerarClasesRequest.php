<?php

namespace App\Http\Requests\Alumno\Pago;

use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;

class GenerarClasesRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();
    $datos["monto"] = ReglasValidacion::formatoDato($datos, "monto");
    $datos["costoXHoraClase"] = ReglasValidacion::formatoDato($datos, "costoXHoraClase");
    $datos["fechaInicioClases"] = ReglasValidacion::formatoDato($datos, "fechaInicioClases");
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $reglasValidacion = [
        "monto" => ["required", "regex:" . ReglasValidacion::RegexDecimal],
        "costoXHoraClase" => ["required", "regex:" . ReglasValidacion::RegexDecimal],
        "fechaInicioClases" => "required|date_format:d/m/Y",
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
