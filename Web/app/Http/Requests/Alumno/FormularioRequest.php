<?php

namespace App\Http\Requests\Alumno;

use Auth;
use Config;
use App\Models\Curso;
use App\Models\NivelIngles;
use App\Models\TipoDocumento;
use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;
use App\Helpers\Enum\SexosEntidad;

class FormularioRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();
    $datos["telefono"] = ReglasValidacion::formatoDato($datos, "telefono");
    $datos["fechaNacimiento"] = ReglasValidacion::formatoDato($datos, "fechaNacimiento");
    $datos["sexo"] = ReglasValidacion::formatoDato($datos, "sexo", "");
    $datos["idTipoDocumento"] = ReglasValidacion::formatoDato($datos, "idTipoDocumento");
    $datos["numeroDocumento"] = ReglasValidacion::formatoDato($datos, "numeroDocumento");
    $datos["imagenPerfil"] = ReglasValidacion::formatoDato($datos, "imagenPerfil");

    $datos["codigoDepartamento"] = ReglasValidacion::formatoDato($datos, "codigoDepartamento");
    $datos["codigoProvincia"] = ReglasValidacion::formatoDato($datos, "codigoProvincia");
    $datos["codigoDistrito"] = ReglasValidacion::formatoDato($datos, "codigoDistrito");
    $datos["codigoUbigeo"] = ReglasValidacion::formatoDato($datos, "codigoUbigeo");
    $datos["numeroDepartamento"] = ReglasValidacion::formatoDato($datos, "numeroDepartamento");
    $datos["referenciaDireccion"] = ReglasValidacion::formatoDato($datos, "referenciaDireccion");
    $datos["geoLatitud"] = ReglasValidacion::formatoDato($datos, "geoLatitud");
    $datos["geoLongitud"] = ReglasValidacion::formatoDato($datos, "geoLongitud");

    $datos["idNivelIngles"] = ReglasValidacion::formatoDato($datos, "idNivelIngles");
    $datos["inglesLugarEstudio"] = ReglasValidacion::formatoDato($datos, "inglesLugarEstudio");
    $datos["inglesPracticaComo"] = ReglasValidacion::formatoDato($datos, "inglesPracticaComo");
    $datos["inglesObjetivo"] = ReglasValidacion::formatoDato($datos, "inglesObjetivo");

    $datos["conComputadora"] = (isset($datos["conComputadora"]) && $datos["conComputadora"] == "on" ? 1 : 0);
    $datos["conInternet"] = (isset($datos["conInternet"]) && $datos["conInternet"] == "on" ? 1 : 0);
    $datos["conPlumonPizarra"] = (isset($datos["conPlumonPizarra"]) && $datos["conPlumonPizarra"] == "on" ? 1 : 0);
    $datos["conAmbienteClase"] = (isset($datos["conAmbienteClase"]) && $datos["conAmbienteClase"] == "on" ? 1 : 0);
    $datos["idCurso"] = ReglasValidacion::formatoDato($datos, "idCurso");
    $datos["numeroHorasClase"] = ReglasValidacion::formatoDato($datos, "numeroHorasClase");
    $datos["horario"] = ReglasValidacion::formatoDato($datos, "horario");
    $datos["comentarioAdicional"] = ReglasValidacion::formatoDato($datos, "comentarioAdicional");

    $datos["estado"] = ReglasValidacion::formatoDato($datos, "estado");
    $datos["idInteresado"] = ReglasValidacion::formatoDato($datos, "idInteresado");
    $datos["costoHoraClase"] = ReglasValidacion::formatoDato($datos, "costoHoraClase");
    $datos["codigoVerificacion"] = ReglasValidacion::formatoDato($datos, "codigoVerificacion");
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $datos = $this->all();

    $reglasValidacion = [
        "nombre" => ["required", "max:255", "regex:" . ReglasValidacion::RegexAlfabetico],
        "apellido" => ["required", "max:255", "regex:" . ReglasValidacion::RegexAlfabetico],
        "telefono" => (Auth::guest() ? "required|" : "") . "max:30",
        "fechaNacimiento" => (Auth::guest() ? "required|" : "") . "date_format:d/m/Y",
        "numeroDocumento" => (Auth::guest() ? "required|" : "") . "numeric|digits_between:8,20",
        "correoElectronico" => "required|email|max:245",
        "imagenPerfil" => "image",
        "direccion" => "required|max:255",
        "numeroDepartamento" => "max:255",
        "referenciaDireccion" => "max:255",
        "geoLatitud" => ["regex:" . ReglasValidacion::RegexGeoLatitud],
        "geoLongitud" => ["regex:" . ReglasValidacion::RegexGeoLongitud],
        "inglesLugarEstudio" => "max:255",
        "inglesPracticaComo" => "max:255",
        "inglesObjetivo" => "max:255",
        "numeroHorasClase" => "required|numeric|between:" . ((int) Config::get("eah.minHorasClase") * 3600) . "," . ((int) Config::get("eah.maxHorasClase") * 3600),
        "fechaInicioClase" => "required|date_format:d/m/Y",
        "costoHoraClase" => ["required", "regex:" . ReglasValidacion::RegexDecimal],
        "comentarioAdicional" => "max:255"
    ];

    $listaSexos = SexosEntidad::listar();
    if (!array_key_exists($datos["sexo"], $listaSexos)) {
      $reglasValidacion["sexoNoValido"] = "required";
    }

    $listaTiposDocumentos = TipoDocumento::listarSimple();
    if (!array_key_exists($datos["idTipoDocumento"], $listaTiposDocumentos->toArray())) {
      $reglasValidacion["tipoDocumenoNoValido"] = "required";
    }

    if (!ReglasValidacion::validarUbigeo($datos["codigoDepartamento"], $datos["codigoProvincia"], $datos["codigoDistrito"], $datos["codigoUbigeo"])) {
      $reglasValidacion["ubigeoNoValido"] = "required";
    }

    $listaNivelesIngles = NivelIngles::listarSimple();
    if (!array_key_exists($datos["idNivelIngles"], $listaNivelesIngles->toArray())) {
      $reglasValidacion["nivelInglesNoValido"] = "required";
    }

    $listaCursos = Curso::listarSimple();
    if (!array_key_exists($datos["idCurso"], $listaCursos->toArray())) {
      $reglasValidacion["cursoNoValido"] = "required";
    }

    if (!ReglasValidacion::validarHorario($datos["horario"])) {
      $reglasValidacion["horarioNoValido"] = "required";
    }

    if (isset($datos["idInteresado"])) {
      $reglasValidacion["codigoVerificacion"] = "required";
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
        "sexoNoValido.required" => "El sexo seleccionado no es válido",
        "tipoDocumenoNoValido.required" => "El tipo de documento seleccionado no es válido.",
        "ubigeoNoValido.required" => "Los datos de dirección ingresados no son válidos.",
        "nivelInglesNoValido.required" => "El nivel de inglés seleccionado no es válido.",
        "cursoNoValido.required" => "El curso seleccionado no es válido.",
        "horarioNoValido.required" => "El horario seleccionado no es válido."
    ];
  }

}
