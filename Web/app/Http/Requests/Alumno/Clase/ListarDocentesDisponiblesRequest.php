<?php

namespace App\Http\Requests\Alumno\Clase;

use Config;
use App\Models\Curso;
use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;
use App\Helpers\Enum\TiposEntidad;
use App\Helpers\Enum\SexosEntidad;

class ListarDocentesDisponiblesRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();    
    $datos["fecha"] = ReglasValidacion::formatoDato($datos, "fecha");
    $datos["horaInicio"] = ReglasValidacion::formatoDato($datos, "horaInicio");
    $datos["duracion"] = ReglasValidacion::formatoDato($datos, "duracion");
    $datos["tipoDocente"] = ReglasValidacion::formatoDato($datos, "tipoDocente");
    $datos["sexoDocente"] = ReglasValidacion::formatoDato($datos, "sexoDocente", "");
    $datos["idCursoDocente"] = ReglasValidacion::formatoDato($datos, "idCursoDocente", "");
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
    $listaSexos = SexosEntidad::listar();
    if (!(!is_null($datos["sexoDocente"]) && ($datos["sexoDocente"] == "" || array_key_exists($datos["sexoDocente"], $listaSexos)))) {
      $reglasValidacion["sexoDocenteNoValido"] = "required";
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
        "tipoDocenteNoValido.required" => "El tipo seleccionado para filtrar la lista de profesores o postulantes no es válido",
        "sexoDocenteNoValido.required" => "El sexo seleccionado para filtrar la lista de profesores o postulantes no es válido",
        "idCursoDocenteNoValido.required" => "El curso seleccionado para filtrar la lista de profesores o postulantes no es válido"
    ];
  }

}
