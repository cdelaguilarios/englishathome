<?php

namespace App\Http\Requests\Notificacion;

use App\Http\Requests\Request;

class ActualizarRevisionRequest extends Request/* - */ {

  public function authorize()/* - */ {
    return true;
  }

  protected function getValidatorInstance()/* - */ {
    $datos = $this->all();
    if (isset($datos["idsNotificaciones"]) && !is_array($datos["idsNotificaciones"])) {
      $datos["idsNotificaciones"] = explode(",", $datos["idsNotificaciones"]);
    }
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
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
              "idsNotificaciones" => "required"
          ];
        }
      default:break;
    }
  }

}
