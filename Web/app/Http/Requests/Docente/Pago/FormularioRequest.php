<?php

namespace App\Http\Requests\Docente\Pago;

use App\Helpers\Util;
use App\Models\Profesor;
use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;
use App\Helpers\Enum\TiposBusquedaFecha;

class FormularioRequest extends Request/* - */ {

  public function authorize()/* - */ {
    return true;
  }

  protected function getValidatorInstance()/* - */ {
    $datos = $this->all();
    $datos["idProfesor"] = ReglasValidacion::formatoDato($datos, "idProfesor");
    $datos["fecha"] = ReglasValidacion::formatoDato($datos, "fecha");
    $datos["descripcion"] = ReglasValidacion::formatoDato($datos, "descripcion"); 
    $datos["nombresArchivosImagenComprobante"] = ReglasValidacion::formatoDato($datos, "nombresArchivosImagenComprobante");
    $datos["nombresOriginalesArchivosImagenComprobante"] = ReglasValidacion::formatoDato($datos, "nombresOriginalesArchivosImagenComprobante");
    $datos["nombresArchivosImagenComprobanteEliminados"] = ReglasValidacion::formatoDato($datos, "nombresArchivosImagenComprobanteEliminados");
    Util::preProcesarFiltrosBusquedaXFechas($datos);
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules()/* - */ {
    $datos = $this->all();

    $reglasValidacion = [
        "fecha" => "required|date_format:d/m/Y",
        "descripcion" => "max:255"
    ];

    if (!Profesor::verificarExistencia($datos["idProfesor"])) {
      $reglasValidacion["profesorNoValido"] = "required";
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
        "profesorNoValido.required" => "El profesor seleccionado no es válido."
    ];
  }

}
