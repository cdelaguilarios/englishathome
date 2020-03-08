<?php

namespace App\Http\Requests\Tarea;

use Carbon\Carbon;
use App\Models\Tarea;
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
    $datos["nombresArchivosAdjuntos"] = ReglasValidacion::formatoDato($datos, "nombresArchivosAdjuntos");
    $datos["nombresOriginalesArchivosAdjuntos"] = ReglasValidacion::formatoDato($datos, "nombresOriginalesArchivosAdjuntos");
    $datos["nombresArchivosAdjuntosEliminados"] = ReglasValidacion::formatoDato($datos, "nombresArchivosAdjuntosEliminados");
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

    $validarProgramacion = TRUE;
    if (isset($datos["idTarea"]) && $datos["idTarea"] != "") {
      $tarea = Tarea::obtenerXId($datos["idTarea"]);
      $fechaActual = Carbon::now();
      $fechaNotificacion = Carbon::createFromFormat("Y-m-d H:i:s", $tarea->fechaNotificacion);
      $validarProgramacion = ($fechaNotificacion > $fechaActual);
    }

    if ($validarProgramacion) {
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

}
