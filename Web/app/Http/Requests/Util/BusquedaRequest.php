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
    $datos["estadoClase"] = ReglasValidacion::formatoDato($datos, "estadoClase");
    $datos["estadoPago"] = ReglasValidacion::formatoDato($datos, "estadoPago");
    $datos["tipoPago"] = ReglasValidacion::formatoDato($datos, "tipoPago", "0");

    $datos["tipoBusquedaFecha"] = ReglasValidacion::formatoDato($datos, "tipoBusquedaFecha");
    $datos["fechaDia"] = ReglasValidacion::formatoDato($datos, "fechaDia");
    $datos["fechaMes"] = ReglasValidacion::formatoDato($datos, "fechaMes");
    $datos["fechaAnho"] = ReglasValidacion::formatoDato($datos, "fechaAnho");
    $datos["fechaDiaInicio"] = ReglasValidacion::formatoDato($datos, "fechaDiaInicio");
    $datos["fechaDiaFin"] = ReglasValidacion::formatoDato($datos, "fechaDiaFin");
    $datos["fechaMesInicio"] = ReglasValidacion::formatoDato($datos, "fechaMesInicio");
    $datos["fechaMesFin"] = ReglasValidacion::formatoDato($datos, "fechaMesFin");
    $datos["fechaAnhoInicio"] = ReglasValidacion::formatoDato($datos, "fechaAnhoInicio");
    $datos["fechaAnhoFin"] = ReglasValidacion::formatoDato($datos, "fechaAnhoFin");
    $datos["ids"] = ReglasValidacion::formatoDato($datos, "ids", []);
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $datos = $this->all();
    $reglasValidacion = [];

    $listaEstadosClase = EstadosClase::listarBusqueda();
    if (!is_null($datos["estadoClase"]) && !array_key_exists($datos["estadoClase"], $listaEstadosClase)) {
      $reglasValidacion["estadoClaseNoValido"] = "required";
    }

    $listaEstadosPagos = EstadosPago::listar();
    if (!is_null($datos["estadoPago"]) && !array_key_exists($datos["estadoPago"], $listaEstadosPagos)) {
      $reglasValidacion["estadoPagoNoValido"] = "required";
    }

    $listaTiposBusquedaFecha = TiposBusquedaFecha::listar();
    if (!array_key_exists($datos["tipoBusquedaFecha"], $listaTiposBusquedaFecha)) {
      $reglasValidacion["tipoBusquedaFechaNoValido"] = "required";
    }
    if ($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::Dia && !preg_match(ReglasValidacion::RegexFecha, $datos["fechaDia"])) {
      $datos["fechaDia"] = NULL;
    } else if ($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::Mes && !preg_match(ReglasValidacion::RegexFecha, "01/" . $datos["fechaMes"])) {
      $datos["fechaMes"] = NULL;
    } else if ($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::Anho && !preg_match(ReglasValidacion::RegexFecha, "01/01/" . $datos["fechaAnho"])) {
      $datos["fechaAnho"] = NULL;
    } else if ($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::RangoDias) {
      $datos["fechaDiaInicio"] = ((!is_null($datos["fechaDiaInicio"]) && preg_match(ReglasValidacion::RegexFecha, $datos["fechaDiaInicio"])) ? $datos["fechaDiaInicio"] : NULL);
      $datos["fechaDiaFin"] = ((!is_null($datos["fechaDiaFin"]) && preg_match(ReglasValidacion::RegexFecha, $datos["fechaDiaFin"])) ? $datos["fechaDiaFin"] : NULL);
    } else if ($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::RangoMeses) {
      $datos["fechaMesInicio"] = ((!is_null($datos["fechaMesInicio"]) && preg_match(ReglasValidacion::RegexFecha, "01/" . $datos["fechaMesInicio"])) ? $datos["fechaMesInicio"] : NULL);
      $datos["fechaMesFin"] = ((!is_null($datos["fechaMesFin"]) && preg_match(ReglasValidacion::RegexFecha, "01/" . $datos["fechaMesFin"])) ? $datos["fechaMesFin"] : NULL);
    } else if ($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::RangoAnhos) {
      $datos["fechaAnhoInicio"] = ((!is_null($datos["fechaAnhoInicio"]) && preg_match(ReglasValidacion::RegexFecha, "01/01/" . $datos["fechaAnhoInicio"])) ? $datos["fechaAnhoInicio"] : NULL);
      $datos["fechaAnhoFin"] = ((!is_null($datos["fechaAnhoFin"]) && preg_match(ReglasValidacion::RegexFecha, "01/01/" . $datos["fechaAnhoFin"])) ? $datos["fechaAnhoFin"] : NULL);
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
        "estadoClaseNoValido.required" => "El estado de clase seleccionado no es válido.",
        "estadoPagoNoValido.required" => "El estado de pago seleccionado no es válido.",
        "tipoBusquedaFechaNoValido.required" => "El tipo de busqueda de la fecha no es válido."
    ];
  }

}
