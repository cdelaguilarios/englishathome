<?php

namespace App\Http\Requests\Tarea;

use App\Models\Usuario;
use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;

class FormularioRequest extends Request/* - */ {

  public function authorize()/* - */ {
    return true;
  }

  protected function getValidatorInstance()/* - */ {
    $datos = $this->all();
    $datos["titulo"] = ReglasValidacion::formatoDato($datos, "titulo");
    $datos["mensaje"] = ReglasValidacion::formatoDato($datos, "mensaje", "");
    $datos["nombresArchivosAdjuntos"] = ReglasValidacion::formatoDato($datos, "nombresArchivosAdjuntosTarea");
    $datos["nombresOriginalesArchivosAdjuntos"] = ReglasValidacion::formatoDato($datos, "nombresOriginalesArchivosAdjuntosTarea");
    $datos["nombresArchivosAdjuntosEliminados"] = ReglasValidacion::formatoDato($datos, "nombresArchivosAdjuntosTareaEliminados");
    $datos["notificarInmediatamente"] = (isset($datos["notificarInmediatamente"]) ? 1 : 0);
    $datos["fechaProgramada"] = ReglasValidacion::formatoDato($datos, "fechaProgramada");
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules()/* - */ {
    $datos = $this->all();
    $reglasValidacion = [
        "titulo" => "required|max:100",
        "mensaje" => "max:8000",
        "fechaProgramada" => "date_format:d/m/Y H:i:s"
    ];

    if (!Usuario::verificarExistencia($datos["idUsuarioAsignado"])) {
      $reglasValidacion["usuarioNoValido"] = "required";
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

  public function messages()/* - */ {
    return [
        "usuarioNoValido.required" => "El usuario seleccionado no es válido."
    ];
  }

}
