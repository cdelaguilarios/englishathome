<?php

namespace App\Http\Requests\Historial;

use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;
use App\Helpers\Enum\TiposEntidad;

class FormularioCorreoRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();
    $datos["titulo"] = ReglasValidacion::formatoDato($datos, "titulo");
    $datos["asunto"] = ReglasValidacion::formatoDato($datos, "asunto");
    $datos["mensaje"] = ReglasValidacion::formatoDato($datos, "mensaje");
    $datos["tipoEntidad"] = ReglasValidacion::formatoDato($datos, "tipoEntidad");
    $datos["idsEntidadesSeleccionadas"] = ReglasValidacion::formatoDato($datos, "idsEntidadesSeleccionadas");
    $datos["correosAdicionales"] = ReglasValidacion::formatoDato($datos, "correosAdicionales");
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $datos = $this->all();
    $reglasValidacion = [
        "titulo" => "required|max:255",
        "asunto" => "required|max:255",
        "mensaje" => "required|max:4000"
    ];

    $listaTiposEntidades = TiposEntidad::listarSeccionCorreos();
    if (!is_null($datos["tipoEntidad"]) && !array_key_exists($datos["tipoEntidad"], $listaTiposEntidades)) {
      $reglasValidacion["tipoEntidadNoValida"] = "required";
    } else if (is_null($datos["tipoEntidad"]) && is_null($datos["idsEntidadesSeleccionadas"]) && is_null($datos["correosAdicionales"])) {
      $reglasValidacion["correosNoValido"] = "required";
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
        "tipoEntidadNoValida.required" => "El tipo de entidad seleccionada no es válida.",
        "correosNoValido.required" => "Debe seleccionar por lo menos una entidad o ingresar un correo adicional."
    ];
  }

}