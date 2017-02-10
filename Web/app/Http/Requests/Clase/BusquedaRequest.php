<?php

namespace App\Http\Requests\Clase;

use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;
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
    $datos["fechaDia"] = ReglasValidacion::formatoDato($datos, "fechaDia");
    $datos["fechaMes"] = ReglasValidacion::formatoDato($datos, "fechaMes");
    $datos["fechaAnho"] = ReglasValidacion::formatoDato($datos, "fechaAnho");
    $datos["fechaInicio"] = ReglasValidacion::formatoDato($datos, "fechaInicio");
    $datos["fechaFin"] = ReglasValidacion::formatoDato($datos, "fechaFin");
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $datos = $this->all();
    $reglasValidacion = [];

    $listaEstados = EstadosClase::listar();
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
      
    } else if ($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::Anho) {
      
    } else if ($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::RangoFecha) {
      if (!preg_match(ReglasValidacion::RegexFecha, $datos["fechaInicio"]) || !preg_match(ReglasValidacion::RegexFecha, $datos["fechaFin"])) {
        $datos["fechaInicio"] = NULL;
        $datos["fechaFin"] = NULL;
      }
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
