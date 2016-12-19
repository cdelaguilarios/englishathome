<?php

namespace App\Http\Requests\Alumno\Clase;

use Config;
use App\Models\Docente;
use App\Models\Clase;
use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;
use App\Helpers\Enum\EstadosClase;

class ClaseRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();
    $datos["idClase"] = (isset($datos["idClase"]) && $datos["idClase"] != "" ? $datos["idClase"] : NULL);
    $datos["idAlumno"] = (isset($datos["idAlumno"]) && $datos["idAlumno"] != "" ? $datos["idAlumno"] : NULL);
    $datos["numeroPeriodo"] = (isset($datos["numeroPeriodo"]) ? $datos["numeroPeriodo"] : NULL);
    $data["estado"] = (isset($data["estado"]) ? $data["estado"] : NULL);
    $datos["notificar"] = (isset($datos["notificar"]) ? 1 : 0);
    $datos["fecha"] = (isset($datos["fecha"]) ? $datos["fecha"] : NULL);
    $datos["horaInicio"] = (isset($datos["horaInicio"]) ? $datos["horaInicio"] : NULL);
    $datos["duracion"] = (isset($datos["duracion"]) ? $datos["duracion"] : NULL);
    $datos["costoHora"] = (isset($datos["costoHora"]) ? $datos["costoHora"] : NULL);
    $datos["idDocente"] = (isset($datos["idDocente"]) && $datos["idDocente"] != "" ? $datos["idDocente"] : NULL);
    $datos["costoHoraDocente"] = (isset($datos["costoHoraDocente"]) ? $datos["costoHoraDocente"] : NULL);
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


    if (!is_null($datos["idClase"]) && !is_null($datos["idAlumno"]) && !Clase::verificarExistencia($datos["idAlumno"], $datos["idClase"])) {
      $reglasValidacion["claseNoValida"] = "required";
    }
    $estados = EstadosClase::listarSimple(TRUE);
    if (!array_key_exists($datos["estado"], $estados)) {
      $reglasValidacion["estadoNoValido"] = "required";
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
        "claseNoValida.required" => "La clase seleccionada no es válida",
        "estadoNoValido.required" => "El estado seleccionado no es válido",
        "docenteNoValido.required" => "El docente seleccionado no es válido"
    ];
  }

}
