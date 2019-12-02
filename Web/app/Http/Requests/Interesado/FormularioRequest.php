<?php

namespace App\Http\Requests\Interesado;

use Auth;
use App\Models\Curso;
use App\Models\Usuario;
use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;
use App\Helpers\Enum\EstadosInteresado;
use App\Helpers\Enum\OrigenesInteresado;

class FormularioRequest extends Request/* - */ {

  public function authorize()/* - */ {
    return true;
  }

  protected function getValidatorInstance()/* - */ {
    $datos = $this->all();
    $datos["telefono"] = ReglasValidacion::formatoDato($datos, "telefono");
    $datos["consulta"] = ReglasValidacion::formatoDato($datos, "consulta");
    $datos["idCurso"] = ReglasValidacion::formatoDato($datos, "idCurso");
    $datos["cursoInteres"] = ReglasValidacion::formatoDato($datos, "cursoInteres", "");
    $datos["codigoDepartamento"] = ReglasValidacion::formatoDato($datos, "codigoDepartamento");
    $datos["codigoProvincia"] = ReglasValidacion::formatoDato($datos, "codigoProvincia");
    $datos["codigoDistrito"] = ReglasValidacion::formatoDato($datos, "codigoDistrito");
    $datos["codigoUbigeo"] = ReglasValidacion::formatoDato($datos, "codigoUbigeo");
    $datos["estado"] = ReglasValidacion::formatoDato($datos, "estado");
    $datos["origen"] = ReglasValidacion::formatoDato($datos, "origen");
    $datos["costoXHoraClase"] = ReglasValidacion::formatoDato($datos, "costoXHoraClase");
    $datos["comentarioAdicional"] = ReglasValidacion::formatoDato($datos, "comentarioAdicional");
    $datos["registrarComoAlumno"] = ReglasValidacion::formatoDato($datos, "registrarComoAlumno", 0);
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules()/* - */ {
    $datos = $this->all();
    $reglasValidacion = [
        "nombre" => [(Auth::guest() ? "" : "required"), "max:255", "regex:" . ReglasValidacion::RegexAlfabetico],
        "apellido" => [(Auth::guest() ? "" : "required"), "max:255", "regex:" . ReglasValidacion::RegexAlfabetico],
        "telefono" => (Auth::guest() ? "" : "required|") . "max:30",
        "correoElectronico" => (Auth::guest() ? "" : "required|email|") . "max:245" . ($datos["registrarComoAlumno"] == 1 ? "|unique:" . Usuario::nombreTabla() . ",email" : ""),
        "consulta" => "max:255",
        "cursoInteres" => "max:255",
        "costoXHoraClase" => (Auth::guest() ? "" : ["required", "regex:" . ReglasValidacion::RegexDecimal]),
        "comentarioAdicional" => "max:8000"
    ];

    $listaCursos = Curso::listarSimple();
    if (!is_null($datos["idCurso"]) && !array_key_exists($datos["idCurso"], $listaCursos->toArray())) {
      $reglasValidacion["cursoNoValido"] = "required";
    }

    if (!is_null($datos["codigoUbigeo"]) && !ReglasValidacion::validarUbigeo($datos["codigoDepartamento"], $datos["codigoProvincia"], $datos["codigoDistrito"], $datos["codigoUbigeo"])) {
      $reglasValidacion["ubigeoNoValido"] = "required";
    }

    $estados = EstadosInteresado::listarDisponibleCambio();
    if (!is_null($datos["estado"]) && !array_key_exists($datos["estado"], $estados)) {
      $reglasValidacion["estadoNoValido"] = "required";
    }

    $origenes = OrigenesInteresado::listar();
    if (!is_null($datos["origen"]) && !array_key_exists($datos["origen"], $origenes)) {
      $reglasValidacion["origenNoValido"] = "required";
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

  public function messages()/* - */ {
    return [
        "correoElectronico.unique" => "El correo electrónico ingresado ya está siendo utilizado por un alumno. Tomar en cuenta que el alumno utiliza su correo electrónico para acceder al sistema y este dato no puede ser igual al que utiliza un profesor o un usuario del sistema.",
        "cursoNoValido.required" => "El curso seleccionado no es válido.",
        "ubigeoNoValido.required" => "Los datos de dirección ingresados no son válidos.",
        "estadoNoValido.required" => "El estado seleccionado no es válido.",
        "origenNoValido.required" => "El origen seleccionado no es válido."
    ];
  }

}
