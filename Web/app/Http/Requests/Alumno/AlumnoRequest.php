<?php

namespace App\Http\Requests\Alumno;

use Config;
use App\Models\Curso;
use App\Models\NivelIngles;
use App\Models\TipoDocumento;
use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;

class AlumnoRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();

    $datos["imagenPerfil"] = (isset($datos["imagenPerfil"]) ? $datos["imagenPerfil"] : NULL);
    $datos["idTipoDocumento"] = (isset($datos["idTipoDocumento"]) ? $datos["idTipoDocumento"] : NULL);
    $datos["codigoDepartamento"] = (isset($datos["codigoDepartamento"]) ? $datos["codigoDepartamento"] : NULL);
    $datos["codigoProvincia"] = (isset($datos["codigoProvincia"]) ? $datos["codigoProvincia"] : NULL);
    $datos["codigoDistrito"] = (isset($datos["codigoDistrito"]) ? $datos["codigoDistrito"] : NULL);
    $datos["codigoUbigeo"] = (isset($datos["codigoUbigeo"]) ? $datos["codigoUbigeo"] : NULL);
    $datos["referenciaDireccion"] = (isset($datos["referenciaDireccion"]) ? $datos["referenciaDireccion"] : NULL);
    $datos["geoLatitud"] = (isset($datos["geoLatitud"]) ? $datos["geoLatitud"] : NULL);
    $datos["geoLongitud"] = (isset($datos["geoLongitud"]) ? $datos["geoLongitud"] : NULL);
    $datos["idNivelIngles"] = (isset($datos["idNivelIngles"]) ? $datos["idNivelIngles"] : NULL);
    $datos["inglesLugarEstudio"] = (isset($datos["inglesLugarEstudio"]) ? $datos["inglesLugarEstudio"] : NULL);
    $datos["inglesPracticaComo"] = (isset($datos["inglesPracticaComo"]) ? $datos["inglesPracticaComo"] : NULL);
    $datos["inglesObjetivo"] = (isset($datos["inglesObjetivo"]) ? $datos["inglesObjetivo"] : NULL);
    $datos["conComputadora"] = (isset($datos["conComputadora"]) && $datos["conComputadora"] == "on" ? 1 : 0);
    $datos["conInternet"] = (isset($datos["conInternet"]) && $datos["conInternet"] == "on" ? 1 : 0);
    $datos["conPlumonPizarra"] = (isset($datos["conPlumonPizarra"]) && $datos["conPlumonPizarra"] == "on" ? 1 : 0);
    $datos["conAmbienteClase"] = (isset($datos["conAmbienteClase"]) && $datos["conAmbienteClase"] == "on" ? 1 : 0);
    $datos["numeroHorasClase"] = (isset($datos["numeroHorasClase"]) ? $datos["numeroHorasClase"] : NULL);
    $datos["idCurso"] = (isset($datos["idCurso"]) ? $datos["idCurso"] : NULL);
    $datos["horario"] = (isset($datos["horario"]) ? $datos["horario"] : NULL);
    $datos["comentarioAdicional"] = (isset($datos["comentarioAdicional"]) ? $datos["comentarioAdicional"] : NULL);

    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $datos = $this->all();

    $reglasValidacion = [
        "nombre" => ["required", "max:255", "regex:" . ReglasValidacion::RegexAlfabetico],
        "apellido" => ["required", "max:255", "regex:" . ReglasValidacion::RegexAlfabetico],
        "telefono" => "required|max:30",
        "fechaNacimiento" => "required|date_format:d/m/Y",
        "numeroDocumento" => "required|numeric|digits_between:8,20",
        "correoElectronico" => "required|email|max:245",
        "imagenPerfil" => "image",
        "direccion" => "required|max:255",
        "referenciaDireccion" => "max:255",
        "geoLatitud" => ["regex:" . ReglasValidacion::RegexGeoLatitud],
        "geoLongitud" => ["regex:" . ReglasValidacion::RegexGeoLongitud],
        "inglesLugarEstudio" => "max:255",
        "inglesPracticaComo" => "max:255",
        "inglesObjetivo" => "max:255",
        "numeroHorasClase" => "required|numeric|between:" . ((int) Config::get("eah.minHorasClase") * 3600) . "," . ((int) Config::get("eah.maxHorasClase") * 3600),
        "fechaInicioClase" => "required|date_format:d/m/Y",
        "comentarioAdicional" => "max:255"
    ];

    $listaTiposDocumentos = TipoDocumento::listarSimple();
    if (!(!is_null($datos["idTipoDocumento"]) && array_key_exists($datos["idTipoDocumento"], $listaTiposDocumentos->toArray()))) {
      $reglasValidacion["tipoDocumenoNoValido"] = "required";
    }

    if (!ReglasValidacion::validarUbigeo($datos["codigoDepartamento"], $datos["codigoProvincia"], $datos["codigoDistrito"], $datos["codigoUbigeo"])) {
      $reglasValidacion["ubigeoNoValido"] = "required";
    }

    $listaNivelesIngles = NivelIngles::listarSimple();
    if (!(!is_null($datos["idNivelIngles"]) && array_key_exists($datos["idNivelIngles"], $listaNivelesIngles->toArray()))) {
      $reglasValidacion["nivelInglesNoValido"] = "required";
    }

    $listaCursos = Curso::listarSimple();
    if (!(!is_null($datos["idCurso"]) && array_key_exists($datos["idCurso"], $listaCursos->toArray()))) {
      $reglasValidacion["cursoNoValido"] = "required";
    }

    if (!ReglasValidacion::validarHorario($datos["horario"])) {
      $reglasValidacion["horarioNoValido"] = "required";
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
        "tipoDocumenoNoValido.required" => "El tipo de documento seleccionado no es válido",
        "ubigeoNoValido.required" => "Los datos de dirección ingresados no son válidos.",
        "nivelInglesNoValido.required" => "El nivel de inglés seleccionado no es válido",
        "cursoNoValido.required" => "El curso seleccionado no es válido",
        "horarioNoValido.required" => "El horario seleccionado no es válido"
    ];
  }

}
