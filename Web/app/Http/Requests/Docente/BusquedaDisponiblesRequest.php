<?php

namespace App\Http\Requests\Docente;

use App\Models\Curso;
use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;
use App\Helpers\Enum\TiposEntidad;
use App\Helpers\Enum\SexosEntidad;
use App\Helpers\Enum\EstadosDocente;

class BusquedaDisponiblesRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();
    $datos["tipoDocente"] = ReglasValidacion::formatoDato($datos, "tipoDocente");
    $datos["estadoDocente"] = ReglasValidacion::formatoDato($datos, "estadoDocente");
    $datos["sexoDocente"] = ReglasValidacion::formatoDato($datos, "sexoDocente", "");
    $datos["idCursoDocente"] = ReglasValidacion::formatoDato($datos, "idCursoDocente", "");
    $datos["horarioDocente"] = ReglasValidacion::formatoDato($datos, "horarioDocente");
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $datos = $this->all();
    $reglasValidacion = [];

    $listaTipos = TiposEntidad::listarTiposDocente();
    if (!(!is_null($datos["tipoDocente"]) && (array_key_exists($datos["tipoDocente"], $listaTipos)))) {
      $reglasValidacion["tipoDocenteNoValido"] = "required";
    }
    $listaEstados = EstadosDocente::listarBusqueda();
    if (!is_null($datos["estadoDocente"]) && !array_key_exists($datos["estadoDocente"], $listaEstados)) {
      $reglasValidacion["estadoDocenteNoValido"] = "required";
    }
    $listaSexos = SexosEntidad::listar();
    if (!(!is_null($datos["sexoDocente"]) && ($datos["sexoDocente"] == "" || array_key_exists($datos["sexoDocente"], $listaSexos)))) {
      $reglasValidacion["sexoDocenteNoValido"] = "required";
    }
    $listaCursos = Curso::listarSimple()->toArray();
    if (!(!is_null($datos["idCursoDocente"]) && ($datos["idCursoDocente"] == "" || array_key_exists($datos["idCursoDocente"], $listaCursos)))) {
      $reglasValidacion["idCursoDocenteNoValido"] = "required";
    }
    if (!is_null($datos["horarioDocente"]) && $datos["horarioDocente"] != "[]" && !ReglasValidacion::validarHorario($datos["horarioDocente"])) {
      $reglasValidacion["horarioDocenteNoValido"] = "required";
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
        "tipoDocenteNoValido.required" => "El tipo seleccionado para filtrar la lista de docentes no es válido.",
        "estadoDocenteNoValido.required" => "El estado seleccionado para filtrar la lista de docentes no es válido.",
        "sexoDocenteNoValido.required" => "El sexo seleccionado para filtrar la lista de docentes no es válido.",
        "idCursoDocenteNoValido.required" => "El curso seleccionado para filtrar la lista de docentes no es válido.",
        "horarioDocenteNoValido.required" => "El horario seleccionado para filtrar la lista de docentes no es válido."
    ];
  }

}
