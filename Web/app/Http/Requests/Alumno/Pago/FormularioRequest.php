<?php

namespace App\Http\Requests\Alumno\Pago;

use App\Models\Docente;
use App\Http\Requests\Request;
use App\Helpers\Enum\MotivosPago;
use App\Helpers\ReglasValidacion;
use App\Helpers\Enum\EstadosPago;
use App\Helpers\Enum\CuentasBancoPago;

class FormularioRequest extends Request/* - */ {

  public function authorize()/* - */ {
    return true;
  }

  protected function getValidatorInstance()/* - */ {
    $datos = $this->all();
    $datos["motivo"] = ReglasValidacion::formatoDato($datos, "motivo");
    $datos["cuenta"] = ReglasValidacion::formatoDato($datos, "cuenta");
    $datos["fecha"] = ReglasValidacion::formatoDato($datos, "fecha");
    $datos["estado"] = ReglasValidacion::formatoDato($datos, "estado");
    $datos["descripcion"] = ReglasValidacion::formatoDato($datos, "descripcion");
    $datos["imagenComprobante"] = ReglasValidacion::formatoDato($datos, "imagenComprobante");
    $datos["usarSaldoFavor"] = (isset($datos["usarSaldoFavor"]) ? 1 : 0);
    $datos["costoXHoraClase"] = ReglasValidacion::formatoDato($datos, "costoXHoraClase");
    $datos["periodoClases"] = ReglasValidacion::formatoDato($datos, "periodoClases");
    $datos["pagoXHoraProfesor"] = ReglasValidacion::formatoDato($datos, "pagoXHoraProfesor");
    $datos["idDocente"] = ReglasValidacion::formatoDato($datos, "idDocente");
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules()/* - */ {
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
          "costoXHoraClase" => ["required", "regex:" . ReglasValidacion::RegexDecimal],
          "periodoClases" => "required|numeric|digits_between :1,11|min:1"
      ];

      if (!is_null($datos["idDocente"])) {
        $reglasValidacion += [
            "pagoXHoraProfesor" => ["required", "regex:" . ReglasValidacion::RegexDecimal]
        ];
        if (!Docente::verificarExistencia($datos["idDocente"])) {
          $reglasValidacion["docenteNoValido"] = "required";
        }
      }
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
        "motivoNoValido.required" => "El motivo seleccionado del pago no es válido.",
        "cuentaNoValida.required" => "La cuenta de banco seleccionada del pago no es válida.",
        "estadoNoValido.required" => "El estado seleccionado del pago no es válido.",
        "docenteNoValido.required" => "El docente seleccionado no es válido."
    ];
  }

}
