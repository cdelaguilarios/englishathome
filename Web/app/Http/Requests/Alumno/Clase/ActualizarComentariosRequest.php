<?php

namespace App\Http\Requests\Alumno\Clase;

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
              "tipo" => "required|numeric|digits_between :1,4|min:1",
              "idClase" => "required",
              "idAlumno" => "required",
              "comentario" => "required|max:8000"
          ];
        }
      default:break;
    }
  }

}
