<?php

namespace App\Http\Requests\Alumno\Clase;

use App\Models\Clase;
use App\Http\Requests\Request;
use App\Helpers\Enum\EstadosClase;

class ActualizarEstadoRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();
    $datos["idClase"] = (isset($datos["idClase"]) && $datos["idClase"] != "" ? $datos["idClase"] : NULL);
    $datos["idAlumno"] = (isset($datos["idAlumno"]) && $datos["idAlumno"] != "" ? $datos["idAlumno"] : NULL);
    $data["estado"] = (isset($data["estado"]) ? $data["estado"] : NULL);

    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $datos = $this->all();
    $reglasValidacion = [];


    if (!is_null($datos["idClase"]) && !is_null($datos["idAlumno"]) && !Clase::verificarExistencia($datos["idAlumno"], $datos["idClase"])) {
      $reglasValidacion["claseNoValida"] = "required";
    }
    $estados = EstadosClase::listarSimple(TRUE);
    if (!array_key_exists($datos["estado"], $estados)) {
      $reglasValidacion["estadoNoValido"] = "required";
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
        "claseNoValida.required" => "La clase seleccionada no es válida",
        "estadoNoValido.required" => "El estado seleccionado no es válido"
    ];
  }

}
