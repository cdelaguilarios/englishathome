<?php

namespace App\Http\Requests\Interesado;

use App\Models\Curso;
use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;
use App\Helpers\Enum\EstadosInteresado;

class FormularioRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();
    $datos["consulta"] = ReglasValidacion::formatoDato($datos, "consulta");
    $datos["idCurso"] = ReglasValidacion::formatoDato($datos, "idCurso");
    $datos["codigoUbigeo"] = ReglasValidacion::formatoDato($datos, "codigoUbigeo");
    $datos["estado"] = ReglasValidacion::formatoDato($datos, "estado");
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $datos = $this->all();
    $reglasValidacion = [
        "nombre" => ["required", "max:255", "regex:" . ReglasValidacion::RegexAlfabetico],
        "apellido" => ["required", "max:255", "regex:" . ReglasValidacion::RegexAlfabetico],
        "telefono" => "required|max:30",
        "correoElectronico" => "required|email|max:245",
        "consulta" => "max:255",
        "cursoInteres" => "max:255"
    ];

    $listaCursos = Curso::listarSimple();
    if (!is_null($datos["idCurso"]) && !array_key_exists($datos["idCurso"], $listaCursos->toArray())) {
      $reglasValidacion["cursoNoValido"] = "required";
    }
    
    if (!is_null($datos["codigoUbigeo"]) && !ReglasValidacion::validarUbigeo($datos["codigoDepartamento"], $datos["codigoProvincia"], $datos["codigoDistrito"], $datos["codigoUbigeo"])) {
      $reglasValidacion["ubigeoNoValido"] = "required";
    }
    
    $estados = EstadosInteresado::listar(TRUE);
    if (!is_null($datos["estado"]) && !array_key_exists($datos["estado"], $estados)) {
      $reglasValidacion["estadoNoValido"] = "required";
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
        "cursoNoValido.required" => "El curso seleccionado no es válido.",
        "ubigeoNoValido.required" => "Los datos de dirección ingresados no son válidos.",
        "estadoNoValido.required" => "El estado seleccionado no es válido."
    ];
  }

}
