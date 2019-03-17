<?php

namespace App\Http\Requests\Entidad;

use App\Models\Usuario;
use App\Models\Entidad;
use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;
use App\Helpers\Enum\TiposEntidad;

class ActualizarCredencialesAccesoRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();
    $datos["id"] = ReglasValidacion::formatoDato($datos, "id", 0);
    $datos["password"] = ReglasValidacion::formatoDato($datos, "password");
    $datos["password_confirmation"] = ReglasValidacion::formatoDato($datos, "password_confirmation");
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $datos = $this->all();
    $idEntidad = $datos["id"];
    

    $reglasValidacion = [
        "email" => "required|email|max:245|unique:" . Usuario::nombreTabla() . ",email," . $idEntidad . ",idEntidad",
        "password" => "required|confirmed|min:6|max:30"
    ];
        
    $entidad = Entidad::ObtenerXId($idEntidad);
    if(!(in_array($entidad->tipo, [TiposEntidad::Alumno, TiposEntidad::Profesor]))){
      $reglasValidacion["tipoEntidadNoValido"] = "required";      
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
        "email.unique" => "El correo electrónico ingresado ya esta siendo utilizado.",
        "tipoEntidadNoValido.required" => "El tipo de entidad no es válido."
    ];
  }

}
