<?php

namespace App\Http\Requests\Alumno\Pago;

use App\Http\Requests\Request;
use App\Helpers\Enum\MotivosPago;
use App\Helpers\ReglasValidacion;
use App\Helpers\Enum\EstadosPago;
use App\Helpers\Enum\CuentasBancoPago;

class FormularioRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();
    $datos["motivo"] = ReglasValidacion::formatoDato($datos, "motivo");
    $datos["fecha"] = ReglasValidacion::formatoDato($datos, "fecha");
    $datos["cuenta"] = ReglasValidacion::formatoDato($datos, "cuenta");
    $datos["estado"] = ReglasValidacion::formatoDato($datos, "estado");
    $datos["descripcion"] = ReglasValidacion::formatoDato($datos, "descripcion");
    $datos["imagenComprobante"] = ReglasValidacion::formatoDato($datos, "imagenComprobante");
    $datos["usarSaldoFavor"] = (isset($datos["usarSaldoFavor"]) ? 1 : 0);
    $datos["periodoClases"] = ReglasValidacion::formatoDato($datos, "periodoClases");
    $datos["costoXHoraClase"] = ReglasValidacion::formatoDato($datos, "costoXHoraClase");
    $datos["pagoXHoraProfesor"] = ReglasValidacion::formatoDato($datos, "pagoXHoraProfesor");
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $datos = $this->all();

    $reglasValidacion = [
        "fecha" => "required|date_format:d/m/Y",
        "descripcion" => "max:255",
        "imagenComprobante" => "image",
        "monto" => ["required", "regex:" . ReglasValidacion::RegexDecimal],
        "saldoFavor" => ["regex:" . ReglasValidacion::RegexDecimal]
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

    if ($datos["motivo"] == MotivosPago::Clases) {
      $reglasValidacion += [
          "periodoClases" => "required|numeric|digits_between :1,11|min:1",
          "costoXHoraClase" => ["required", "regex:" . ReglasValidacion::RegexDecimal],
          "pagoXHoraProfesor" => ["required", "regex:" . ReglasValidacion::RegexDecimal]
      ];
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

  public function messages() {
    return [
        "motivoNoValido.required" => "El motivo seleccionado del pago no es válido.",
        "cuentaNoValida.required" => "La cuenta de banco seleccionada del pago no es válida.",
        "estadoNoValido.required" => "El estado seleccionado del pago no es válido."
    ];
  }

}
