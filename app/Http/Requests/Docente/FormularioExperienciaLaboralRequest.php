<?php

namespace App\Http\Requests\Docente;

use App\Helpers\Util;
use App\Models\Docente;
use App\Http\Requests\Request;

class FormularioExperienciaLaboralRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();
    Util::preProcesarDocumentosDocente($datos);
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $reglasValidacion = [
        "audio" => "mimes:mpga,wav,oga,ogv,ogx|max:2048"
    ];
    
    $id = $this->route('id');    
    if(!Docente::verificarExistencia($id)){
      $reglasValidacion["docenteNoValido"] = "required";      
    }

    switch ($this->method()) {
      case "GET":
      case "DELETE":
      case "POST": {
          return [];
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
        "audio.mimes" => "Por favor seleccione un audio válido (formatos válidos: mp3, wav y ogg).",
        "docenteNoValido.required" => "La entidad seleccionada debe ser un profesor o postulante."
    ];
  }

}
