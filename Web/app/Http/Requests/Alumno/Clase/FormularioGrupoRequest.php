<?php

namespace App\Http\Requests\Alumno\Clase;

use Config;
use App\Models\Docente;
use App\Models\PagoAlumno;
use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;
use App\Helpers\Enum\EstadosClase;

class FormularioGrupoRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();
    $datos["editarDatosGenerales"] = (isset($datos["editarDatosGenerales"]) ? 1 : 0);
    $datos["editarDatosTiempo"] = (isset($datos["editarDatosTiempo"]) ? 1 : 0);
    $datos["editarDatosPago"] = (isset($datos["editarDatosPagos"]) ? 1 : 0);
    $datos["editarDatosProfesor"] = (isset($datos["editarDatosProfesor"]) ? 1 : 0);
    $datos["idAlumno"] = ReglasValidacion::formatoDato($datos, "idAlumno");
    $datos["numeroPeriodo"] = ReglasValidacion::formatoDato($datos, "numeroPeriodo");
    $datos["estado"] = ReglasValidacion::formatoDato($datos, "estado");
    $datos["horaInicio"] = ReglasValidacion::formatoDato($datos, "horaInicio");
    $datos["duracion"] = ReglasValidacion::formatoDato($datos, "duracion");
    $datos["costoHora"] = ReglasValidacion::formatoDato($datos, "costoHora");
    $datos["idPago"] = ReglasValidacion::formatoDato($datos, "idPago");
    $datos["idDocente"] = ReglasValidacion::formatoDato($datos, "idDocente");
    $datos["pagoXHoraProfesor"] = ReglasValidacion::formatoDato($datos, "pagoXHoraProfesor");
    $datos["idsClases"] = ReglasValidacion::formatoDato($datos, "idsClases", []);
    if (!is_array($datos["idsClases"])) {
      $datos["idsClases"] = explode(",", $datos["idsClases"]);
    }
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $datos = $this->all();
    $reglasValidacion = [
        "numeroPeriodo" => ($datos["editarDatosGenerales"] == 1 ? "required|" : "") . "numeric|digits_between :1,11|min:1",
        "horaInicio" => ($datos["editarDatosTiempo"] == 1 ? "required|" : "") . "numeric|between:" . ((int) Config::get("eah.minHorario") * 3600) . "," . ((int) Config::get("eah.maxHorario") * 3600),
        "duracion" => ($datos["editarDatosTiempo"] == 1 ? "required|" : "") . "numeric|between:" . ((int) Config::get("eah.minHorasClase") * 3600) . "," . ((int) Config::get("eah.maxHorasClase") * 3600),
        "costoHora" => [($datos["editarDatosPago"] == 1 ? "required" : ""), "regex:" . ReglasValidacion::RegexDecimal],
        "pagoXHoraProfesor" => ["regex:" . ReglasValidacion::RegexDecimal]
    ];

    $estados = EstadosClase::listarDisponibleCambio();
    if ($datos["editarDatosGenerales"] == 1 && !array_key_exists($datos["estado"], $estados)) {
      $reglasValidacion["estadoNoValido"] = "required";
    }
    if ($datos["editarDatosPago"] == 1 && !is_null($datos["idPago"]) && !PagoAlumno::verificarExistencia($datos["idAlumno"], $datos["idPago"])) {
      $reglasValidacion["pagoNoValido"] = "required";
    }
    if ($datos["editarDatosProfesor"] == 1 && !is_null($datos["idDocente"]) && !Docente::verificarExistencia($datos["idDocente"])) {
      $reglasValidacion["docenteNoValido"] = "required";
    }

    switch ($this->method()) {
      case "GET":
      case "DELETE": {
          return [];
        }
      case "POST": {
          return $reglasValidacion;
        }
      case "PUT":
      case "PATCH": {
          return $reglasValidacion;
        }
      default:break;
    }
  }

  public function messages() {
    return [
        "estadoNoValido.required" => "El estado seleccionado no es válido.",
        "pagoNoValido.required" => "El pago seleccionado no es válido.",
        "docenteNoValido.required" => "El docente seleccionado no es válido."
    ];
  }

}
