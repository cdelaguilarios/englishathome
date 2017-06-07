<?php

namespace App\Http\Requests\Curso;

use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;

class FormularioRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();
    $datos["id"] = ReglasValidacion::formatoDato($datos, "id", 0);
    $datos["nombre"] = ReglasValidacion::formatoDato($datos, "nombre");
    $datos["descripcion"] = ReglasValidacion::formatoDato($datos, "descripcion");
    $datos["modulos"] = ReglasValidacion::formatoDato($datos, "modulos");
    $datos["metodologia"] = ReglasValidacion::formatoDato($datos, "metodologia");
    $datos["incluye"] = ReglasValidacion::formatoDato($datos, "incluye");
    $datos["inversion"] = ReglasValidacion::formatoDato($datos, "inversion");
    $datos["inversionCuotas"] = ReglasValidacion::formatoDato($datos, "inversionCuotas");
    $datos["activo"] = (isset($datos["activo"]) ? 1 : 0);
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $reglasValidacion = [
        "nombre" => ["required", "max:255"],
        "imagenPerfil" => "image",
        "descripcion" => "required|max:8000",
        "modulos" => "required|max:8000",
        "metodologia" => "required|max:8000",
        "incluye" => "required|max:8000",
        "inversion" => "required|max:8000",
        "inversionCuotas" => "required|max:8000"
    ];

    switch ($this->method()) {
      case "GET":
      case "DELETE": {
          return [];
        }
      case "POST":
      case "PUT":
      case "PATCH": {
          return $reglasValidacion;
        }
      default:break;
    }
  }

}
