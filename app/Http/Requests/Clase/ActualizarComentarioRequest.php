<?php

namespace App\Http\Requests\Clase;

use App\Http\Requests\Request;

class ActualizarComentarioRequest extends Request {

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
              "id" => "required",
              "comentario" => "required|max:8000",
              "tipo" => "required|numeric|digits_between:1,4|min:1"
          ];
        }
      default:break;
    }
  }

}
