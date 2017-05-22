<?php

namespace App\Http\Requests\Postulante;

use Auth;
use App\Models\Curso;
use App\Models\Postulante;
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

    $datos["nombresDocumentosPersonales"] = ReglasValidacion::formatoDato($datos, "nombresDocumentosPersonales");
    $datos["nombresDocumentosPersonalesEliminados"] = ReglasValidacion::formatoDato($datos, "nombresDocumentosPersonalesEliminados");
    $datos["nombresOriginalesDocumentosPersonales"] = ReglasValidacion::formatoDato($datos, "nombresOriginalesDocumentosPersonales");

    $datos["idCursos"] = ReglasValidacion::formatoDato($datos, "idCursos");
    $datos["horario"] = ReglasValidacion::formatoDato($datos, "horario");
    $datos["audio"] = ReglasValidacion::formatoDato($datos, "audio");

    $datos["estado"] = ReglasValidacion::formatoDato($datos, "estado");
    $datos["registrarComoProfesor"] = ReglasValidacion::formatoDato($datos, "registrarComoProfesor", 0);
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $datos = $this->all();

    $reglasValidacion = [
        "nombre" => ["required", "max:255", "regex:" . ReglasValidacion::RegexAlfabetico],
        "apellido" => ["required", "max:255", "regex:" . ReglasValidacion::RegexAlfabetico],
        "telefono" => "max:30",
        "fechaNacimiento" => (Auth::guest() ? "required|" : "") . "date_format:d/m/Y",
        "numeroDocumento" => (Auth::guest() ? "required|" : "") . "numeric|digits_between:8,20",
        "correoElectronico" => "required|email|max:245",
        "imagenPerfil" => "image",
        "direccion" => "required|max:255",
        "numeroDepartamento" => "max:255",
        "referenciaDireccion" => "max:255",
        "geoLatitud" => ["regex:" . ReglasValidacion::RegexGeoLatitud],
        "geoLongitud" => ["regex:" . ReglasValidacion::RegexGeoLongitud],
        "audio" => (Auth::guest() ? "required|" : "") . "mimes:mpga,wav,oga,ogv,ogx|max:2048"
    ];

    if (Auth::guest()) {
      $reglasValidacion["ultimosTrabajos"] = "required";
      $reglasValidacion["experienciaOtrosIdiomas"] = "required";
      $reglasValidacion["descripcionPropia"] = "required";
      $reglasValidacion["ensayo"] = "required";
    }

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

    if (!(Auth::guest())) {
      $listaCursos = Curso::listarSimple();
      if (!is_null($datos["idCursos"])) {
        foreach ($datos["idCursos"] as $idCurso) {
          if (!array_key_exists($idCurso, $listaCursos->toArray())) {
            $reglasValidacion["cursosNoValido"] = "required";
            break;
          }
        }
      } else {
        $reglasValidacion["cursosNoValido"] = "required";
      }
    } else if (isset($datos["correoElectronico"]) && Postulante::verificarExistenciaXCorreoElectronico($datos["correoElectronico"])) {
      $reglasValidacion["correoElectronicoRegistradoNoValido"] = "required";
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
        "sexoNoValido.required" => "El sexo seleccionado no es válido.",
        "tipoDocumenoNoValido.required" => "El tipo de documento seleccionado no es válido.",
        "ubigeoNoValido.required" => "Los datos de dirección ingresados no son válidos.",
        "cursosNoValido.required" => "Uno o más de los cursos seleccionados no es válido.",
        "horarioNoValido.required" => "El horario seleccionado no es válido.",
        "correoElectronicoRegistradoNoValido.required" => (Auth::guest() ? "The email entered has already been registered." : "El correo electrónico ingresado ya ha sido registrado."),
        "audio.mimes" => (Auth::guest() ? "Invalid audio (valid formats: mp3, wav and ogg)." : "Por favor seleccione un audio válido (formatos válidos: mp3, wav y ogg).")
    ];
  }

}
