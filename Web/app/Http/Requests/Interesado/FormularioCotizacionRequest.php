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
    $datos["idCurso"] = ReglasValidacion::formatoDato($datos, "idCurso");
    $datos["textoIntroductorio"] = ReglasValidacion::formatoDato($datos, "textoIntroductorio");
    $datos["descripcionCurso"] = ReglasValidacion::formatoDato($datos, "descripcionCurso");
    $datos["metodologia"] = ReglasValidacion::formatoDato($datos, "metodologia");
    $datos["cursoIncluye"] = ReglasValidacion::formatoDato($datos, "cursoIncluye");
    $datos["inversion"] = ReglasValidacion::formatoDato($datos, "inversion");
    $datos["inversionCuotas"] = ReglasValidacion::formatoDato($datos, "inversionCuotas");
    $datos["costoHoraClase"] = ReglasValidacion::formatoDato($datos, "costoHoraClase");
    $datos["correoCotizacionPrueba"] = ReglasValidacion::formatoDato($datos, "correoCotizacionPrueba");
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $datos = $this->all();

    $reglasValidacion = [
        "textoIntroductorio" => "required|max:4000",
        "descripcionCurso" => "required|max:4000",
        "metodologia" => "required|max:4000",
        "cursoIncluye" => "required|max:4000",
        "inversion" => "required|max:4000",
        "inversionCuotas" => "required|max:4000",
        "costoHoraClase" => ["required", "regex:" . ReglasValidacion::RegexDecimal],
        "correoCotizacionPrueba" => "email|max:245"
    ];

    $listaCursos = Curso::listarSimple()->toArray();
    if (!array_key_exists($datos["idCurso"], $listaCursos)) {
      $reglasValidacion["cursoNoValido"] = "required";
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
        "cursoNoValido.required" => "El curso seleccionado no es válido."
    ];
  }

}
