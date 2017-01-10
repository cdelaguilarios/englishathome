<?php

namespace App\Http\Requests\Alumno\Pago;

use App\Models\Docente;
use App\Http\Requests\Request;
use App\Helpers\Enum\MotivosPago;
use App\Helpers\ReglasValidacion;

class FormularioRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();
    $datos["motivo"] = ReglasValidacion::formatoDato($datos, "motivo");
    $datos["monto"] = ReglasValidacion::formatoDato($datos, "monto");
    $datos["costoHoraClase"] = ReglasValidacion::formatoDato($datos, "costoHoraClase");
    $datos["usarSaldoFavor"] = (isset($datos["usarSaldoFavor"]) ? 1 : 0);
    $datos["fechaInicioClases"] = ReglasValidacion::formatoDato($datos, "fechaInicioClases");
    $datos["periodoClases"] = ReglasValidacion::formatoDato($datos, "periodoClases");
    $datos["idDocente"] = ReglasValidacion::formatoDato($datos, "idDocente");
    $datos["costoHoraDocente"] = ReglasValidacion::formatoDato($datos, "costoHoraDocente");
    $datos["datosNotificacionClases"] = ReglasValidacion::formatoDato($datos, "datosNotificacionClases");
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $datos = $this->all();

    $reglasValidacion = [
        "descripcion" => "max:255",
        "imagenComprobante" => "image",
        "monto" => ["required", "regex:" . ReglasValidacion::RegexDecimal]
    ];

    $listaMotivosPago = MotivosPago::listar();
    if (!(!is_null($datos["motivo"]) && array_key_exists($datos["motivo"], $listaMotivosPago))) {
      $reglasValidacion["motivoNoValido"] = "required";
    }

    if ($datos["motivo"] == MotivosPago::Clases) {
      $reglasValidacion += [
          "costoHoraClase" => ["required", "regex:" . ReglasValidacion::RegexDecimal],
          "fechaInicioClases" => "required|date_format:d/m/Y",
          "periodoClases" => "required|numeric|digits_between :1,11|min:1",
          "saldoFavor" => ["regex:" . ReglasValidacion::RegexDecimal]
      ];
      
      if (!is_null($datos["idDocente"])) {
        $reglasValidacion += [
            "costoHoraDocente" => ["required", "regex:" . ReglasValidacion::RegexDecimal]
        ];
        if (!Docente::verificarExistencia($datos["idDocente"])) {
          $reglasValidacion["docenteNoValido"] = "required";
        }
      }
      if (!ReglasValidacion::validarDatosNotificacionClasesPago($datos["datosNotificacionClases"])) {
        $reglasValidacion["datosNotificacionClasesNoValido"] = "required";
      }
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
        "motivoNoValido.required" => "El motivo seleccionado del pago no es válido",
        "docenteNoValido.required" => "El docente seleccionado no es válido",
        "datosNotificacionClasesNoValido.required" => "Los datos de notificación de la clases seleccionadas no son válidas"
    ];
  }

}
