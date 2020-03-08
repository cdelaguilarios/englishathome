<?php

namespace App\Http\Requests\Docente\Pago;

use App\Helpers\Util;
use App\Http\Requests\Request;
use App\Helpers\Enum\EstadosPago;
use App\Helpers\ReglasValidacion;
use App\Helpers\Enum\TiposBusquedaFecha;

class ListarXClasesRequest extends Request/* - */ {

  public function authorize()/* - */ {
    return true;
  }

  protected function getValidatorInstance()/* - */ {
    $datos = $this->all();
    $datos["estadoPago"] = ReglasValidacion::formatoDato($datos, "estadoPago");    
    Util::preProcesarFiltrosBusquedaXFechas($datos);    
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules()/* - */ {
    $datos = $this->all();
    $reglasValidacion = [];

    $listaEstadosPagos = EstadosPago::listar();
    if (!is_null($datos["estadoPago"]) && !array_key_exists($datos["estadoPago"], $listaEstadosPagos)) {
      $reglasValidacion["estadoPagoNoValido"] = "required";
    }

    $listaTiposBusquedaFecha = TiposBusquedaFecha::listar();
    if (!array_key_exists($datos["tipoBusquedaFecha"], $listaTiposBusquedaFecha)) {
      $reglasValidacion["tipoBusquedaFechaNoValido"] = "required";
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

  public function messages()/* - */ {
    return [
        "estadoPagoNoValido.required" => "El estado de pago seleccionado no es válido.",
        "tipoBusquedaFechaNoValido.required" => "El tipo de búsqueda de la fecha no es válido."
    ];
  }

}
