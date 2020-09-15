<?php

namespace App\Http\Requests\Profesor;

use App\Http\Requests\Request;

class ActualizarComentariosPerfilRequest extends Request {

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
              "comentarioPerfil" => "max:8000"
          ];
        }
      default:break;
    }
  }

}
