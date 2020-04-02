<?php

namespace App\Http\Requests\Profesor\MisAlumnos;

use Config;
use App\Models\Alumno;
use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;

class ConfirmarClaseRequest extends Request/* - */ {

  public function authorize()/* - */ {
    return true;
  }

  protected function getValidatorInstance()/* - */ {
    $datos = $this->all();
    $datos["duracion"] = ReglasValidacion::formatoDato($datos, "duracion");
    $datos["comentario"] = ReglasValidacion::formatoDato($datos, "comentario");
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $reglasValidacion = [
        "duracion" => "required|numeric|between:" . ((int) Config::get("eah.minHorasClase") * 3600) . "," . ((int) Config::get("eah.maxHorasClase") * 3600),
        "comentario" => "max:8000"
    ];

    $idAlumno = $this->route()->getParameter('id');
    if (!Alumno::verificarExistencia($idAlumno)) {
      $reglasValidacion["alumnoNoValido"] = "required";
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

  public function messages()/* - */ {
    return [
        "alumnoNoValido.required" => "El alumno seleccionado no es válido."
    ];
  }

}
