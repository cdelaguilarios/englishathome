<?php

namespace App\Http\Requests\Profesor;

use App\Models\Curso;
use App\Helpers\Util;
use App\Models\Usuario;
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
    $datos["cuentasBancarias"] = ReglasValidacion::formatoDato($datos, "cuentasBancarias");

    $datos["codigoDepartamento"] = ReglasValidacion::formatoDato($datos, "codigoDepartamento");
    $datos["codigoProvincia"] = ReglasValidacion::formatoDato($datos, "codigoProvincia");
    $datos["codigoDistrito"] = ReglasValidacion::formatoDato($datos, "codigoDistrito");
    $datos["codigoUbigeo"] = ReglasValidacion::formatoDato($datos, "codigoUbigeo");
    $datos["numeroDepartamento"] = ReglasValidacion::formatoDato($datos, "numeroDepartamento");
    $datos["referenciaDireccion"] = ReglasValidacion::formatoDato($datos, "referenciaDireccion");
    $datos["geoLatitud"] = ReglasValidacion::formatoDato($datos, "geoLatitud");
    $datos["geoLongitud"] = ReglasValidacion::formatoDato($datos, "geoLongitud");

    Util::preProcesarDocumentosDocente($datos);

    $datos["idCursos"] = ReglasValidacion::formatoDato($datos, "idCursos");
    $datos["horario"] = ReglasValidacion::formatoDato($datos, "horario");
    $datos["audio"] = ReglasValidacion::formatoDato($datos, "audio");

    $datos["estado"] = ReglasValidacion::formatoDato($datos, "estado");
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $datos = $this->all();
    $modoEdicion = ($this->method() == "PATCH");
    $idEntidad = $this->route('id');

    $reglasValidacion = [
        "nombre" => ["required", "max:255", "regex:" . ReglasValidacion::RegexAlfabetico],
        "apellido" => ["required", "max:255", "regex:" . ReglasValidacion::RegexAlfabetico],
        "telefono" => "max:30",
        "fechaNacimiento" => "date_format:d/m/Y",
        "numeroDocumento" => "numeric|digits_between:8,20",
        "correoElectronico" => "required|email|max:245|unique:" . Usuario::nombreTabla() . ",email" .
        ($modoEdicion && !is_null($idEntidad) && is_numeric($idEntidad) ? "," . $idEntidad . ",idEntidad" : ""),
        "imagenPerfil" => "image",
        "direccion" => "required|max:255",
        "numeroDepartamento" => "max:255",
        "referenciaDireccion" => "max:255",
        "geoLatitud" => ["regex:" . ReglasValidacion::RegexGeoLatitud],
        "geoLongitud" => ["regex:" . ReglasValidacion::RegexGeoLongitud],
        "audio" => "mimes:mpga,wav,oga,ogv,ogx|max:2048"
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

    if (!is_null($datos["idCursos"])) {
      $listaCursos = Curso::listarSimple();
      foreach ($datos["idCursos"] as $idCurso) {
        if (!array_key_exists($idCurso, $listaCursos->toArray())) {
          $reglasValidacion["cursosNoValido"] = "required";
          break;
        }
      }
    } else {
      $reglasValidacion["cursosNoValido"] = "required";
    }

    if (!ReglasValidacion::validarHorario($datos["horario"])) {
      $reglasValidacion["horarioNoValido"] = "required";
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
        "correoElectronico.unique" => "El correo electrónico ingresado ya está siendo utilizado. Tomar en cuenta que el profesor utiliza su correo electrónico para acceder al sistema y este dato no puede ser igual al que utiliza un alumno o un usuario del sistema.",
        "sexoNoValido.required" => "El sexo seleccionado no es válido.",
        "tipoDocumenoNoValido.required" => "El tipo de documento seleccionado no es válido.",
        "ubigeoNoValido.required" => "Los datos de dirección ingresados no son válidos.",
        "cursosNoValido.required" => "Uno o más de los cursos seleccionados no es válido.",
        "audio.mimes" => "Por favor seleccione un audio válido (formatos válidos: mp3, wav y ogg).",
        "horarioNoValido.required" => "El horario seleccionado no es válido."
    ];
  }

}
