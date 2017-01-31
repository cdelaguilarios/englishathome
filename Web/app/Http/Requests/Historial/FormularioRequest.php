<?php

namespace App\Http\Requests\Historial;

use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;

class FormularioRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();
    $datos["titulo"] = ReglasValidacion::formatoDato($datos, "titulo");
    $datos["mensaje"] = ReglasValidacion::formatoDato($datos, "mensaje", "");
    $datos["enviarCorreo"] = (isset($datos["enviarCorreo"]) ? 1 : 0);
    $datos["mostrarEnPerfil"] = (isset($datos["mostrarEnPerfil"]) ? 1 : 0);
    $datos["fechaNotificacion"] = ReglasValidacion::formatoDato($datos, "fechaNotificacion");
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $reglasValidacion = [
        "titulo" => "required|max:100",
        "mensaje" => "max:4000",
        "fechaNotificacion" => "date_format:d/m/Y"
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
