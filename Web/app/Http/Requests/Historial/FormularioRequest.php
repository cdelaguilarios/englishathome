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
    $datos["nombresArchivosAdjuntos"] = ReglasValidacion::formatoDato($datos, "nombresArchivosAdjuntos");
    $datos["nombresOriginalesArchivosAdjuntos"] = ReglasValidacion::formatoDato($datos, "nombresOriginalesArchivosAdjuntos");
    $datos["enviarCorreo"] = (isset($datos["enviarCorreo"]) ? 1 : 0);
    $datos["enviarCorreoEntidad"] = (isset($datos["enviarCorreoEntidad"]) ? 1 : 0);
    $datos["mostrarEnPerfil"] = (isset($datos["mostrarEnPerfil"]) ? 1 : 0);
    $datos["notificarInmediatamente"] = (isset($datos["notificarInmediatamente"]) ? 1 : 0);
    $datos["fechaNotificacion"] = ReglasValidacion::formatoDato($datos, "fechaNotificacion");
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $datos = $this->all();
    $reglasValidacion = [
        "titulo" => "required|max:100",
        "mensaje" => "max:8000",
        "fechaNotificacion" => "date_format:d/m/Y H:i:s"
    ];

    if ($datos["enviarCorreo"] == 0 && $datos["enviarCorreoEntidad"] == 0 && $datos["mostrarEnPerfil"] == 0) {
      $reglasValidacion["opcionEventoNoValido"] = "required";
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
        "opcionEventoNoValido.required" => "Por favor selecione por lo menos una de las siguientes opciones: \"Enviar correo\" (administrador o entidad) o \"Mostrar en perfil\"."
    ];
  }

}
