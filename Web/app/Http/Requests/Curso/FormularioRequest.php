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
        "nombre" => ["max:255", "regex:" . ReglasValidacion::RegexAlfabetico],
        "imagenPerfil" => "image",
        "descripcion" => "required|max:4000",
        "modulos" => "required|max:4000",
        "metodologia" => "required|max:4000",
        "incluye" => "required|max:4000",
        "inversion" => "required|max:4000",
        "inversionCuotas" => "required|max:4000"
    ];

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

}
