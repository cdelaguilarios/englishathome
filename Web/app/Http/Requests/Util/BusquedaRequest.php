<?php

namespace App\Http\Requests\Util;

use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;
use App\Helpers\Enum\EstadosPago;
use App\Helpers\Enum\EstadosClase;
use App\Helpers\Enum\TiposBusquedaFecha;

class BusquedaRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();
    $datos["estado"] = ReglasValidacion::formatoDato($datos, "estado");
    $datos["tipoBusquedaFecha"] = ReglasValidacion::formatoDato($datos, "tipoBusquedaFecha");
    $datos["tipoPago"] = ReglasValidacion::formatoDato($datos, "tipoPago", "0");
    $datos["fechaDia"] = ReglasValidacion::formatoDato($datos, "fechaDia");
    $datos["fechaMesInicio"] = ReglasValidacion::formatoDato($datos, "fechaMesInicio");
    $datos["fechaMesFin"] = ReglasValidacion::formatoDato($datos, "fechaMesFin");
    $datos["fechaAnhoInicio"] = ReglasValidacion::formatoDato($datos, "fechaAnhoInicio");
    $datos["fechaAnhoFin"] = ReglasValidacion::formatoDato($datos, "fechaAnhoFin");
    $datos["fechaInicio"] = ReglasValidacion::formatoDato($datos, "fechaInicio");
    $datos["fechaFin"] = ReglasValidacion::formatoDato($datos, "fechaFin");
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $datos = $this->all();
    $reglasValidacion = [];

    $listaEstadosClase = EstadosClase::listar();
    $listaEstadosPagos = EstadosPago::listar();
    $listaEstados = $listaEstadosClase + $listaEstadosPagos;
    if (!is_null($datos["estado"]) && !array_key_exists($datos["estado"], $listaEstados)) {
      $reglasValidacion["estadoNoValido"] = "required";
    }
    $listaTiposBusquedaFecha = TiposBusquedaFecha::listar();
    if (!array_key_exists($datos["tipoBusquedaFecha"], $listaTiposBusquedaFecha)) {
      $reglasValidacion["tipoBusquedaFechaNoValido"] = "required";
    }
    if ($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::Dia && !preg_match(ReglasValidacion::RegexFecha, $datos["fechaDia"])) {
      $datos["fechaDia"] = NULL;
    } else if ($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::Mes) {
      $datos["fechaMesInicio"] = ((!is_null($datos["fechaMesInicio"]) && preg_match(ReglasValidacion::RegexFecha, "01/" . $datos["fechaMesInicio"])) ? $datos["fechaMesInicio"] : NULL);
      $datos["fechaMesFin"] = ((!is_null($datos["fechaMesFin"]) && preg_match(ReglasValidacion::RegexFecha, "01/" . $datos["fechaMesFin"])) ? $datos["fechaMesFin"] : NULL);
    } else if ($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::Anho) {
      $datos["fechaAnhoInicio"] = ((!is_null($datos["fechaAnhoInicio"]) && preg_match(ReglasValidacion::RegexFecha, "01/01/" . $datos["fechaAnhoInicio"])) ? $datos["fechaAnhoInicio"] : NULL);
      $datos["fechaAnhoFin"] = ((!is_null($datos["fechaAnhoFin"]) && preg_match(ReglasValidacion::RegexFecha, "01/01/" . $datos["fechaAnhoFin"])) ? $datos["fechaAnhoFin"] : NULL);
    } else if ($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::RangoFecha && (!preg_match(ReglasValidacion::RegexFecha, $datos["fechaInicio"]) || !preg_match(ReglasValidacion::RegexFecha, $datos["fechaFin"]))) {
      $datos["fechaInicio"] = NULL;
      $datos["fechaFin"] = NULL;
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
        "estadoNoValido.required" => "El estado seleccionado no es válido.",
        "tipoBusquedaFechaNoValido.required" => "El tipo de busqueda de la fecha no es válido."
    ];
  }

}
