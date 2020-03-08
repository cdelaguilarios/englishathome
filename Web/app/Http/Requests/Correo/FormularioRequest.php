<?php

namespace App\Http\Requests\Correo;

use App\Models\Interesado;
use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;
use App\Helpers\Enum\TiposEntidad;

class FormularioRequest extends Request/* - */ {

  public function authorize()/* - */ {
    return true;
  }

  protected function getValidatorInstance()/* - */ {
    $datos = $this->all();
    $datos["titulo"] = "";
    $datos["asunto"] = ReglasValidacion::formatoDato($datos, "asunto");
    $datos["mensaje"] = ReglasValidacion::formatoDato($datos, "mensaje");
        
    $datos["nombresArchivosAdjuntos"] = ReglasValidacion::formatoDato($datos, "nombresArchivosAdjuntos");
    $datos["nombresOriginalesArchivosAdjuntos"] = ReglasValidacion::formatoDato($datos, "nombresOriginalesArchivosAdjuntos");
    $datos["nombresArchivosAdjuntosEliminados"] = ReglasValidacion::formatoDato($datos, "nombresArchivosAdjuntosEliminados");
    
    $datos["tipoEntidad"] = ReglasValidacion::formatoDato($datos, "tipoEntidad");    
    foreach(TiposEntidad::listarSeccionCorreos() as $estado => $v){
      $datos["estado" . $estado] = ReglasValidacion::formatoDato($datos, "estado" . $estado);      
    }    
    $datos["cursoInteres"] = ReglasValidacion::formatoDato($datos, "cursoInteres");
    
    $datos["idsEntidadesSeleccionadas"] = ReglasValidacion::formatoDato($datos, "idsEntidadesSeleccionadas");
    $datos["idsEntidadesExcluidas"] = ReglasValidacion::formatoDato($datos, "idsEntidadesExcluidas", []);
    $datos["correosAdicionales"] = ReglasValidacion::formatoDato($datos, "correosAdicionales");
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules()/* - */ {
    $datos = $this->all();
    $reglasValidacion = [
        "asunto" => "required|max:255",
        "mensaje" => "required|max:8000"
    ];

    $listaTiposEntidades = TiposEntidad::listarSeccionCorreos();
    if (!is_null($datos["tipoEntidad"]) && !array_key_exists($datos["tipoEntidad"], $listaTiposEntidades)) {
      $reglasValidacion["tipoEntidadNoValida"] = "required";
    } else if (is_null($datos["tipoEntidad"]) && is_null($datos["idsEntidadesSeleccionadas"]) && is_null($datos["correosAdicionales"])) {
      $reglasValidacion["correosNoValido"] = "required";
    }
    
    $listaCursosInteres = Interesado::listarCursosInteres();
    if (!is_null($datos["tipoEntidad"]) && $datos["tipoEntidad"] == TiposEntidad::Interesado && !is_null($datos["cursoInteres"]) && !array_key_exists($datos["cursoInteres"], $listaCursosInteres->toArray())) {
      $reglasValidacion["cursoInteresNoValido"] = "required";
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
        "tipoEntidadNoValida.required" => "El tipo de entidad seleccionada no es válida.",
        "correosNoValido.required" => "Debe seleccionar por lo menos una entidad o ingresar un correo adicional.",
        "cursoInteresNoValido.required" => "El curso de interes seleccionado no es válido."
    ];
  }

}
