<?php

namespace App\Http\Requests\Alumno\Clase;

use Config;
use App\Models\Curso;
use App\Http\Requests\Request;
use App\Helpers\Enum\TiposEntidad;
use App\Helpers\Enum\GenerosEntidad;

class DocenteDisponibleRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();
    $datos["fecha"] = (isset($datos["fecha"]) ? $datos["fecha"] : NULL);
    $datos["horaInicio"] = (isset($datos["horaInicio"]) ? $datos["horaInicio"] : NULL);
    $datos["duracion"] = (isset($datos["duracion"]) ? $datos["duracion"] : NULL);
    $datos["tipoDocente"] = (isset($datos["tipoDocente"]) ? $datos["tipoDocente"] : NULL);
    $datos["generoDocente"] = (isset($datos["generoDocente"]) ? $datos["generoDocente"] : NULL);
    $datos["idCursoDocente"] = (isset($datos["idCursoDocente"]) ? $datos["idCursoDocente"] : NULL);
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $datos = $this->all();
    $reglasValidacion = [
        "fecha" => "required|date_format:d/m/Y",
        "horaInicio" => "required|numeric|between:" . ((int) Config::get("eah.minHorario") * 3600) . "," . ((int) Config::get("eah.maxHorario") * 3600),
        "duracion" => "required|numeric|between:" . ((int) Config::get("eah.minHorasClase") * 3600) . "," . ((int) Config::get("eah.maxHorasClase") * 3600)
    ];
    $listaTiposDocente = TiposEntidad::listarTiposDocente();
    if (!(!is_null($datos["tipoDocente"]) && (array_key_exists($datos["tipoDocente"], $listaTiposDocente)))) {
      $reglasValidacion["tipoDocenteNoValido"] = "required";
    }
    $listaGeneros = GenerosEntidad::listar();
    if (!(!is_null($datos["generoDocente"]) && ($datos["generoDocente"] == "" || array_key_exists($datos["generoDocente"], $listaGeneros)))) {
      $reglasValidacion["generoDocenteNoValido"] = "required";
    }
    $listaCursos = Curso::listarSimple()->toArray();
    if (!(!is_null($datos["idCursoDocente"]) && ($datos["idCursoDocente"] == "" || array_key_exists($datos["idCursoDocente"], $listaCursos)))) {
      $reglasValidacion["idCursoDocenteNoValido"] = "required";
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
        "generoDocenteNoValido.required" => "El genero seleccionado para filtrar la lista de profesores o postulantes no es válido",
        "idCursoDocenteNoValido.required" => "El curso seleccionado para filtrar la lista de profesores o postulantes no es válido",
        "tipoDocenteNoValido.required" => "El tipo seleccionado para filtrar la lista de profesores o postulantes no es válido"
    ];
  }

}
