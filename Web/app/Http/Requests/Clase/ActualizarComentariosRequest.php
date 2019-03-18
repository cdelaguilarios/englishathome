<?php

namespace App\Http\Requests\Clase;

use App\Http\Requests\Request;

class ActualizarComentariosRequest extends Request {

  public function authorize() {
    return true;
  }

  public function rules() {
    switch ($this->method()) {
      case "GET":
      case "DELETE":
      case "PUT":
      case "PATCH": {
          return [];
        }
      case "POST": {
          return [
              "idClase" => "required",
              "idAlumno" => "required",
              "comentario" => "required|max:8000"
          ];
        }
      default:break;
    }
  }

}
