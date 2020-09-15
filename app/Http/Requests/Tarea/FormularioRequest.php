<?php

namespace App\Http\Requests\Tarea;

use App\Models\Tarea;
use App\Models\Usuario;
use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;
use App\Helpers\Enum\RolesUsuario;

class FormularioRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();
    $datos["mensaje"] = ReglasValidacion::formatoDato($datos, "mensaje", "");
    $datos["nombresArchivosAdjuntos"] = ReglasValidacion::formatoDato($datos, "nombresArchivosAdjuntosTarea");
    $datos["nombresOriginalesArchivosAdjuntos"] = ReglasValidacion::formatoDato($datos, "nombresOriginalesArchivosAdjuntosTarea");
    $datos["nombresArchivosAdjuntosEliminados"] = ReglasValidacion::formatoDato($datos, "nombresArchivosAdjuntosTareaEliminados");
    $datos["notificarInmediatamente"] = (isset($datos["notificarInmediatamente"]) ? 1 : 0);
    $datos["fechaProgramada"] = ReglasValidacion::formatoDato($datos, "fechaProgramada");
    $datos["fechaFinalizacion"] = ReglasValidacion::formatoDato($datos, "fechaFinalizacion");
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $datos = $this->all();
    $reglasValidacion = [
        "mensaje" => "required|max:8000",
        "fechaProgramada" => ["date_format:d/m/Y H:i:s"],
        "fechaFinalizacion" => "date_format:d/m/Y H:i:s"
    ];


    if ($datos["notificarInmediatamente"] != 1) {
      array_push($reglasValidacion["fechaProgramada"], "required");
    }

    if (!Usuario::verificarExistencia($datos["idUsuarioAsignado"])) {
      $reglasValidacion["usuarioNoValido"] = "required";
    }

    if (isset($datos["idTarea"]) && $datos["idTarea"] != "") {
      $tarea = Tarea::obtenerXId($datos["idTarea"]);
      $usuarioActual = Usuario::obtenerActual();
      
      if ($usuarioActual->rol != RolesUsuario::Principal && $tarea->idUsuarioCreador != $usuarioActual->id) {
        $reglasValidacion["usuarioActualSinPermisos"] = "required";
      }
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
        "usuarioNoValido.required" => "El usuario seleccionado no es válido.",
        "mensaje.required" => "Debe ingresar la descripción de la tarea.",
        "usuarioActualSinPermisos.required" => "No tiene permisos para realizar la actualización de datos de la tarea seleccionada."
    ];
  }

}
