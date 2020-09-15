<?php

namespace App\Http\Requests\Usuario;

use App\Models\Usuario;
use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;
use App\Helpers\Enum\RolesUsuario;
use App\Helpers\Enum\EstadosUsuario;

class FormularioRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();
    $datos["id"] = ReglasValidacion::formatoDato($datos, "id", 0);
    $datos["nombre"] = ReglasValidacion::formatoDato($datos, "nombre", "");
    $datos["apellido"] = ReglasValidacion::formatoDato($datos, "apellido", "");
    $datos["password"] = ReglasValidacion::formatoDato($datos, "password");
    $datos["password_confirmation"] = ReglasValidacion::formatoDato($datos, "password_confirmation");
    $datos["rol"] = ReglasValidacion::formatoDato($datos, "rol");
    $datos["estado"] = ReglasValidacion::formatoDato($datos, "estado");
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $datos = $this->all();
    $modoEdicion = ($this->method() == "PATCH");
    $idUsuario = $datos["id"];

    $reglasValidacion = [
        "nombre" => ["max:255", "regex:" . ReglasValidacion::RegexAlfabetico],
        "apellido" => ["max:255", "regex:" . ReglasValidacion::RegexAlfabetico],
        "email" => "required|email|max:245|unique:" . Usuario::nombreTabla() . ",email" .
        ($modoEdicion && !is_null($idUsuario) && is_numeric($idUsuario) ? "," . $idUsuario . ",idEntidad" : ""),
        "imagenPerfil" => "image",
        "codigoVerificacionClases" => "min:4|max:6"
    ];

    $roles = RolesUsuario::listar();
    if (!array_key_exists($datos["rol"], $roles)) {
      $reglasValidacion["rolNoValido"] = "required";
    }
    
    $estados = EstadosUsuario::listar(TRUE);
    if (!array_key_exists($datos["estado"], $estados)) {
      $reglasValidacion["estadoNoValido"] = "required";
    }
    
    if (!$modoEdicion || (!is_null($datos["password"]) && $datos["password"] != "")) {
      $reglasValidacion["password"] = "required|confirmed|min:6|max:30";
    }

    switch ($this->method()) {
      case "GET":
      case "DELETE": {
          return [];
        }
      case "POST":
      case "PUT":
      case "PATCH": {
          return $reglasValidacion;
        }
      default:break;
    }
  }

  public function messages() {
    return [
        "email.unique" => "El correo electrónico ingresado ya está siendo utilizado.",
        "rolNoValido.required" => "El rol seleccionado no es válido.",
        "estadoNoValido.required" => "El estado seleccionado no es válido."
    ];
  }

}
