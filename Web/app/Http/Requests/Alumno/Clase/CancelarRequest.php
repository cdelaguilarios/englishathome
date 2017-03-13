<?php

namespace App\Http\Requests\Alumno\Clase;

use Config;
use App\Models\Clase;
use App\Models\Docente;
use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;
use App\Helpers\Enum\TiposCancelacionClase;

class CancelarRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();
    $datos["idClase"] = ReglasValidacion::formatoDato($datos, "idClase");
    $datos["idAlumno"] = ReglasValidacion::formatoDato($datos, "idAlumno");
    $datos["idProfesor"] = ReglasValidacion::formatoDato($datos, "idProfesor");
    $datos["pagoProfesor"] = ReglasValidacion::formatoDato($datos, "pagoProfesor");
    $datos["tipoCancelacion"] = ReglasValidacion::formatoDato($datos, "tipoCancelacion");
    $datos["reprogramarCancelacion"] = (isset($datos["reprogramarCancelacion"]) ? 1 : 0);
    $datos["fecha"] = ReglasValidacion::formatoDato($datos, "fecha");
    $datos["horaInicio"] = ReglasValidacion::formatoDato($datos, "horaInicio");
    $datos["duracion"] = ReglasValidacion::formatoDato($datos, "duracion");
    $datos["idDocente"] = ReglasValidacion::formatoDato($datos, "idDocente");
    $datos["costoHoraDocente"] = ReglasValidacion::formatoDato($datos, "costoHoraDocente");
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $datos = $this->all();
    $reglasValidacion = [
        "idClase" => "required",
        "idAlumno" => "required",
        "pagoProfesor" => ["regex:" . ReglasValidacion::RegexDecimalNegativo]
    ];

    $listaTiposCancelacion = TiposCancelacionClase::listar();
    if (!array_key_exists($datos["tipoCancelacion"], $listaTiposCancelacion)) {
      $reglasValidacion["tipoCancelacionNoValido"] = "required";
    }
    if (!Clase::verificarExistencia($datos["idAlumno"], $datos["idClase"])) {
      $reglasValidacion["claseNoValida"] = "required";
    }
    //Profesor de la clase cancelada
    if (!is_null($datos["idProfesor"]) && !Docente::verificarExistencia($datos["idProfesor"])) {
      $reglasValidacion["profesorNoValido"] = "required";
    }

    //Reprogramación
    if ($datos["reprogramarCancelacion"] == 1) {
      $reglasValidacion += [
          "fecha" => "required|date_format:d/m/Y",
          "horaInicio" => "required|numeric|between:" . ((int) Config::get("eah.minHorario") * 3600) . "," . ((int) Config::get("eah.maxHorario") * 3600),
          "duracion" => "required|numeric|between:" . ((int) Config::get("eah.minHorasClase") * 3600) . "," . ((int) Config::get("eah.maxHorasClase") * 3600),
          "costoHoraDocente" => ["regex:" . ReglasValidacion::RegexDecimal]
      ];
      //Docente para la nueva clase (reprogramación)
      if (!is_null($datos["idDocente"]) && !Docente::verificarExistencia($datos["idDocente"])) {
        $reglasValidacion["docenteNoValido"] = "required";
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
        "tipoCancelacionNoValido.required" => "El tipo de cancelación seleccionado no es válido",
        "claseNoValida.required" => "La clase seleccionada no es válida",
        "profesorNoValido.required" => "El profesor de la clase cancelada no es válido",
        "docenteNoValido.required" => "El docente seleccionado no es válido"
    ];
  }

}
