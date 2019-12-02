<?php

namespace App\Http\Requests\Entidad;

use App\Http\Requests\Request;

class ActualizarComentariosAdministradorRequest extends Request/* - */ {

  public function authorize()/* - */ {
    return true;
  }

  public function rules()/* - */ {
    switch ($this->method()) {
      case "GET":
      case "DELETE":
      case "PUT":
      case "PATCH": {
          return [];
        }
      case "POST": {
          return [
              "comentarioAdministrador" => "max:8000"
          ];
        }
      default:break;
    }
  }

}
