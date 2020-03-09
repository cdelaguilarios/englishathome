<?php

namespace App\Http\Requests\Notificacion;

use Carbon\Carbon;
use App\Models\Entidad;
use App\Models\Notificacion;
use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;

class FormularioRequest extends Request/* - */ {

  public function authorize()/* - */ {
    return true;
  }

  protected function getValidatorInstance()/* - */ {
    $datos = $this->all();
    $datos["idSeccion"] = ReglasValidacion::formatoDato($datos, "idSeccion", "");
    $datos["titulo"] = ReglasValidacion::formatoDato($datos, "titulo");
    $datos["mensaje"] = ReglasValidacion::formatoDato($datos, "mensaje", "");
    $datos["nombresArchivosAdjuntos"] = ReglasValidacion::formatoDato($datos, "nombresArchivosAdjuntosNotificacion" . ucfirst($datos["idSeccion"]));
    $datos["nombresOriginalesArchivosAdjuntos"] = ReglasValidacion::formatoDato($datos, "nombresOriginalesArchivosAdjuntosNotificacion" . ucfirst($datos["idSeccion"]));
    $datos["nombresArchivosAdjuntosEliminados"] = ReglasValidacion::formatoDato($datos, "nombresArchivosAdjuntosNotificacion" . ucfirst($datos["idSeccion"]) . "Eliminados");
    $datos["enviarCorreo"] = (isset($datos["enviarCorreo"]) ? 1 : 0);
    $datos["enviarCorreoEntidad"] = (isset($datos["enviarCorreoEntidad"]) ? 1 : 0);
    $datos["mostrarEnPerfil"] = (isset($datos["mostrarEnPerfil"]) ? 1 : 0);
    $datos["notificarInmediatamente"] = (isset($datos["notificarInmediatamente"]) ? 1 : 0);
    $datos["fechaProgramada"] = ReglasValidacion::formatoDato($datos, "fechaProgramada");
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules()/* - */ {
    $datos = $this->all();
    $reglasValidacion = [
        "titulo" => "required|max:100",
        "mensaje" => "max:8000"
    ];

    if (!Entidad::verificarExistencia($datos["idEntidad"])) {
      $reglasValidacion["entidadNoValida"] = "required";
    }

    $validarProgramacion = TRUE;
    if (isset($datos["idNotificacion"]) && $datos["idNotificacion"] != "") {
      $notificacion = Notificacion::obtenerXId($datos["idNotificacion"]);
      $fechaActual = Carbon::now();
      $fechaNotificacion = Carbon::createFromFormat("Y-m-d H:i:s", $notificacion->fechaNotificacion);
      $validarProgramacion = ($fechaNotificacion > $fechaActual);
    }

    if ($validarProgramacion) {
      if ($datos["enviarCorreo"] == 0 && $datos["enviarCorreoEntidad"] == 0 && $datos["mostrarEnPerfil"] == 0) {
        $reglasValidacion["opcionEventoNoValido"] = "required";
      }
      $reglasValidacion["fechaProgramada"] = "date_format:d/m/Y H:i:s";
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
        "entidadNoValida.required" => "La entidad seleccionada no es válida.",
        "opcionEventoNoValido.required" => "Por favor selecione por lo menos una de las siguientes opciones: \"Enviar correo\" (administrador o entidad) o \"Mostrar en perfil\"."
    ];
  }

}
