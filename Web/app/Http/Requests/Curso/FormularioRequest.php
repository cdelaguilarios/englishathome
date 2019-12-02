<?php

namespace App\Http\Requests\Curso;

use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;

class FormularioRequest extends Request/* - */ {

  public function authorize()/* - */ {
    return true;
  }

  protected function getValidatorInstance()/* - */ {
    $datos = $this->all();
    $datos["id"] = ReglasValidacion::formatoDato($datos, "id", 0);
    $datos["nombre"] = ReglasValidacion::formatoDato($datos, "nombre");   
    $datos["descripcion"] = ReglasValidacion::formatoDato($datos, "descripcion"); 
    $datos["nombresArchivosAdjuntos"] = ReglasValidacion::formatoDato($datos, "nombresArchivosAdjuntos");
    $datos["nombresOriginalesArchivosAdjuntos"] = ReglasValidacion::formatoDato($datos, "nombresOriginalesArchivosAdjuntos");
    $datos["nombresArchivosAdjuntosEliminados"] = ReglasValidacion::formatoDato($datos, "nombresArchivosAdjuntosEliminados");
    $datos["activo"] = (isset($datos["activo"]) ? 1 : 0);
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules()/* - */ {
    $reglasValidacion = [
        "nombre" => ["required", "max:255"],
        "descripcion" => "required|max:8000",
        "imagenPerfil" => "image"
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
