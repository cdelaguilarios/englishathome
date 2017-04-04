<?php

namespace App\Http\Requests\Alumno\Clase;

use Config;
use App\Models\Clase;
use App\Models\Docente;
use App\Models\PagoAlumno;
use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;
use App\Helpers\Enum\EstadosClase;

class FormularioRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();
    $datos["idClase"] = ReglasValidacion::formatoDato($datos, "idClase");
    $datos["idAlumno"] = ReglasValidacion::formatoDato($datos, "idAlumno");
    $datos["numeroPeriodo"] = ReglasValidacion::formatoDato($datos, "numeroPeriodo");
    $datos["estado"] = ReglasValidacion::formatoDato($datos, "estado");
    $datos["notificar"] = (isset($datos["notificar"]) ? 1 : 0);
    $datos["fecha"] = ReglasValidacion::formatoDato($datos, "fecha");
    $datos["horaInicio"] = ReglasValidacion::formatoDato($datos, "horaInicio");
    $datos["duracion"] = ReglasValidacion::formatoDato($datos, "duracion");
    $datos["costoHora"] = ReglasValidacion::formatoDato($datos, "costoHora");
    $datos["idPago"] = ReglasValidacion::formatoDato($datos, "idPago");
    $datos["idDocente"] = ReglasValidacion::formatoDato($datos, "idDocente");
    $datos["costoHoraDocente"] = ReglasValidacion::formatoDato($datos, "costoHoraDocente");
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $datos = $this->all();
    $reglasValidacion = [
        "numeroPeriodo" => "required|numeric|digits_between :1,11|min:1",
        "fecha" => "required|date_format:d/m/Y",
        "horaInicio" => "required|numeric|between:" . ((int) Config::get("eah.minHorario") * 3600) . "," . ((int) Config::get("eah.maxHorario") * 3600),
        "duracion" => "required|numeric|between:" . ((int) Config::get("eah.minHorasClase") * 3600) . "," . ((int) Config::get("eah.maxHorasClase") * 3600),
        "costoHora" => ["required", "regex:" . ReglasValidacion::RegexDecimal],
        "costoHoraDocente" => ["regex:" . ReglasValidacion::RegexDecimal]
    ];

    if (!is_null($datos["idClase"]) && !Clase::verificarExistencia($datos["idAlumno"], $datos["idClase"])) {
      $reglasValidacion["claseNoValida"] = "required";
    }
    $estados = EstadosClase::listarCambio();
    if ((is_null($datos["idClase"]) || (!is_null($datos["idClase"]) && !is_null($datos["estado"]))) && !array_key_exists($datos["estado"], $estados)) {
      $reglasValidacion["estadoNoValido"] = "required";
    }
    if (!is_null($datos["idPago"]) && !PagoAlumno::verificarExistencia($datos["idAlumno"], $datos["idPago"])) {
      $reglasValidacion["pagoNoValido"] = "required";
    }
    if (!is_null($datos["idDocente"]) && !Docente::verificarExistencia($datos["idDocente"])) {
      $reglasValidacion["docenteNoValido"] = "required";
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
        "claseNoValida.required" => "La clase seleccionada no es válida.",
        "estadoNoValido.required" => "El estado seleccionado no es válido.",
        "pagoNoValido.required" => "El pago seleccionado no es válido.",
        "docenteNoValido.required" => "El docente seleccionado no es válido."
    ];
  }

}
