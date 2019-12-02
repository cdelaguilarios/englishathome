<?php

namespace App\Http\Requests\Entidad;

use App\Http\Requests\Request;

class ActualizarImagenRequest extends Request/* - */ {

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
              "imagenPerfil" => "required|image"
          ];
        }
      default:break;
    }
  }

}
