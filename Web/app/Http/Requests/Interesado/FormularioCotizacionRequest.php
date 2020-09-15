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
    $datos["contenidoCorreo"] = ReglasValidacion::formatoDato($datos, "contenidoCorreo");
    $datos["costoXHoraClase"] = ReglasValidacion::formatoDato($datos, "costoXHoraClase");
    $datos["correoCotizacionPrueba"] = ReglasValidacion::formatoDato($datos, "correoCotizacionPrueba");
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $datos = $this->all();
    $reglasValidacion = [
        "contenidoCorreo" => "required|max:8000",        
        "costoXHoraClase" => ["required", "regex:" . ReglasValidacion::RegexDecimal],
        "correoCotizacionPrueba" => "email|max:245"
    ];

    $listaCursos = Curso::listarSimple();
    if (!array_key_exists($datos["idCurso"], $listaCursos->toArray())){
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
