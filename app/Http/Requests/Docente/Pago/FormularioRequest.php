<?php

namespace App\Http\Requests\Docente\Pago;

use App\Helpers\Util;
use App\Models\Profesor;
use App\Models\PagoProfesor;
use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;
use App\Helpers\Enum\TiposBusquedaFecha;

class FormularioRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();
    $datos["idPago"] = ReglasValidacion::formatoDato($datos, "idPago");
    $datos["idProfesor"] = ReglasValidacion::formatoDato($datos, "idProfesor");
    $datos["fecha"] = ReglasValidacion::formatoDato($datos, "fecha");
    $datos["descripcion"] = ReglasValidacion::formatoDato($datos, "descripcion");
    $datos["nombresArchivosImagenesComprobantes"] = ReglasValidacion::formatoDato($datos, "nombresArchivosImagenesComprobantes");
    $datos["nombresOriginalesArchivosImagenesComprobantes"] = ReglasValidacion::formatoDato($datos, "nombresOriginalesArchivosImagenesComprobantes");
    $datos["nombresArchivosImagenesComprobantesEliminados"] = ReglasValidacion::formatoDato($datos, "nombresArchivosImagenesComprobantesEliminados");
    Util::preProcesarFiltrosBusquedaXFechas($datos);
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $datos = $this->all();
    $reglasValidacion = [
        "fecha" => "required|date_format:d/m/Y",
        "descripcion" => "max:255"
    ];

    if (!Profesor::verificarExistencia($datos["idProfesor"])) {
      $reglasValidacion["profesorNoValido"] = "required";
    } else if (isset($datos["idPago"]) && !PagoProfesor::verificarExistencia($datos["idProfesor"], $datos["idPago"])) {
      $reglasValidacion["pagoNoValido"] = "required";
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

  public function messages() {
    return [
        "profesorNoValido.required" => "El profesor seleccionado no es válido.",
        "pagoNoValido.required" => "El pago seleccionado no es válido."
    ];
  }

}
