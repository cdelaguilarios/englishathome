<?php

namespace App\Http\Requests\Interesado;

use App\Models\Curso;
use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;

class FormularioCotizacionRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();

    $datos["idCurso"] = (isset($datos["idCurso"]) && $datos["idCurso"] != "" ? $datos["idCurso"] : NULL);
    $datos["descripcionCurso"] = (isset($datos["descripcionCurso"]) ? $datos["descripcionCurso"] : NULL);
    $datos["metodologia"] = (isset($datos["metodologia"]) ? $datos["metodologia"] : NULL);
    $datos["cursoIncluye"] = (isset($datos["cursoIncluye"]) ? $datos["cursoIncluye"] : NULL);

    $datos["numeroHorasInversion"] = (isset($datos["numeroHorasInversion"]) ? $datos["numeroHorasInversion"] : NULL);
    $datos["costoMaterialesIversion"] = (isset($datos["costoMaterialesIversion"]) ? $datos["costoMaterialesIversion"] : NULL);
    $datos["totalInversion"] = (isset($datos["totalInversion"]) ? $datos["totalInversion"] : NULL);

    $datos["correoCotizacionPrueba"] = (isset($datos["correoCotizacionPrueba"]) && $datos["correoCotizacionPrueba"] != "" ? $datos["correoCotizacionPrueba"] : NULL);

    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $datos = $this->all();

    $reglasValidacion = [
        "descripcionCurso" => "required|max:4000",
        "metodologia" => "required|max:4000",
        "cursoIncluye" => "required|max:4000",
        "numeroHorasInversion" => "required|numeric|between:1,9999",
        "costoMaterialesIversion" => ["required", "regex:" . ReglasValidacion::RegexDecimal],
        "totalInversion" => ["required", "regex:" . ReglasValidacion::RegexDecimal],
        "correoCotizacionPrueba" => "email|max:245"
    ];

    $listaCursos = Curso::listarSimple()->toArray();
    if (!(!is_null($datos["idCurso"]) && array_key_exists($datos["idCurso"], $listaCursos))) {
      $reglasValidacion["cursoNoValido"] = "required";
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
        "cursoNoValido.required" => "El curso seleccionado no es válido"
    ];
  }

}
