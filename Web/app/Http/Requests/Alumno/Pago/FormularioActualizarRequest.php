<?php

namespace App\Http\Requests\Alumno\Pago;

use App\Http\Requests\Request;
use App\Helpers\Enum\MotivosPago;
use App\Helpers\ReglasValidacion;
use App\Helpers\Enum\EstadosPago;
use App\Helpers\Enum\CuentasBancoPago;

class FormularioActualizarRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();
    $datos["idPago"] = ReglasValidacion::formatoDato($datos, "idPago");
    $datos["motivo"] = ReglasValidacion::formatoDato($datos, "motivo");
    $datos["cuenta"] = ReglasValidacion::formatoDato($datos, "cuenta");
    $datos["fecha"] = ReglasValidacion::formatoDato($datos, "fecha");
    $datos["estado"] = ReglasValidacion::formatoDato($datos, "estado");
    $datos["usarSaldoFavor"] = (isset($datos["usarSaldoFavor"]) ? 1 : 0);
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $datos = $this->all();

    $reglasValidacion = [
        "idPago" => "required",
        "descripcion" => "max:255",
        "imagenComprobante" => "image",
        "monto" => ["required", "regex:" . ReglasValidacion::RegexDecimal],
        "fecha" => "required|date_format:d/m/Y"
    ];

    $listaMotivosPago = MotivosPago::listar();
    if (!array_key_exists($datos["motivo"], $listaMotivosPago)) {
      $reglasValidacion["motivoNoValido"] = "required";
    }

    $listaCuentasBancoPago = CuentasBancoPago::listar();
    if (!array_key_exists($datos["cuenta"], $listaCuentasBancoPago)) {
      $reglasValidacion["cuentaNoValida"] = "required";
    }

    $listaEstadosPago = EstadosPago::listar();
    if (!array_key_exists($datos["estado"], $listaEstadosPago)) {
      $reglasValidacion["estadoNoValido"] = "required";
    }

    switch ($this->method()) {
      case "GET":
      case "DELETE": {
          return [];
        }
      case "POST": {
          return $reglasValidacion;
        }
      case "PUT":
      case "PATCH": {
          return $reglasValidacion;
        }
      default:break;
    }
  }

  public function messages() {
    return [
        "motivoNoValido.required" => "El motivo seleccionado del pago no es válido.",
        "cuentaNoValida.required" => "La cuenta de banco seleccionada del pago no es válida.",
        "estadoNoValido.required" => "El estado seleccionado del pago no es válido."
    ];
  }

}
