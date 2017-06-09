<?php

namespace App\Http\Requests\Profesor\Pago;

use App\Http\Requests\Request;
use App\Helpers\Enum\MotivosPago;
use App\Helpers\Enum\EstadosPago;
use App\Helpers\ReglasValidacion;

class FormularioActualizarRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();
    $datos["idPago"] = ReglasValidacion::formatoDato($datos, "idPago");
    $datos["motivo"] = ReglasValidacion::formatoDato($datos, "motivo");
    $datos["fecha"] = ReglasValidacion::formatoDato($datos, "fecha");
    $datos["estado"] = ReglasValidacion::formatoDato($datos, "estado");
    $datos["nombresDocumentosVerificacion"] = ReglasValidacion::formatoDato($datos, "nombresDocumentosVerificacion");
    $datos["nombresDocumentosVerificacionEliminados"] = ReglasValidacion::formatoDato($datos, "nombresDocumentosVerificacionEliminados");
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

    $listaEstadosPago = EstadosPago::listar();
    if (!array_key_exists($datos["estado"], $listaEstadosPago)) {
      $reglasValidacion["estadoNoValido"] = "required";
    }

    if ($datos["motivo"] == MotivosPago::Clases) {
      $reglasValidacion["nombresDocumentosVerificacion"] = "required|max:3950";
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
        "estadoNoValido.required" => "El estado seleccionado del pago no es válido."
    ];
  }

}
