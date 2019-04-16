<?php

namespace App\Http\Requests\Interesado;

use App\Models\Curso;
use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;

class FormularioCotizacionRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();
    $datos["idCurso"] = ReglasValidacion::formatoDato($datos, "idCurso");
    $datos["imagenCurso"] = ReglasValidacion::formatoDato($datos, "imagenCurso");
    $datos["textoIntroductorio"] = ReglasValidacion::formatoDato($datos, "textoIntroductorio");
    $datos["descripcionCurso"] = ReglasValidacion::formatoDato($datos, "descripcionCurso");
    $datos["modulos"] = ReglasValidacion::formatoDato($datos, "modulos");
    $datos["metodologia"] = ReglasValidacion::formatoDato($datos, "metodologia");
    $datos["cursoIncluye"] = ReglasValidacion::formatoDato($datos, "cursoIncluye");
    $datos["inversion"] = ReglasValidacion::formatoDato($datos, "inversion");
    $datos["inversionCuotas"] = ReglasValidacion::formatoDato($datos, "inversionCuotas");
    $datos["notasAdicionales"] = ReglasValidacion::formatoDato($datos, "notasAdicionales");
    $datos["nombresArchivosAdjuntos"] = ReglasValidacion::formatoDato($datos, "nombresArchivosAdjuntos");
    $datos["nombresOriginalesArchivosAdjuntos"] = ReglasValidacion::formatoDato($datos, "nombresOriginalesArchivosAdjuntos");
    $datos["costoHoraClase"] = ReglasValidacion::formatoDato($datos, "costoHoraClase");
    $datos["cuentaBancoEmpresarial"] = (isset($datos["cuentaBancoEmpresarial"]));
    $datos["correoCotizacionPrueba"] = ReglasValidacion::formatoDato($datos, "correoCotizacionPrueba");
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $datos = $this->all();
    $reglasValidacion = [
        "textoIntroductorio" => "required|max:8000",
        "descripcionCurso" => "required|max:8000",
        "modulos" => "required|max:8000",
        "metodologia" => "required|max:8000",
        "cursoIncluye" => "required|max:8000",
        "inversion" => "required|max:8000",
        "inversionCuotas" => "required|max:8000",
        "costoHoraClase" => ["required", "regex:" . ReglasValidacion::RegexDecimal],
        "correoCotizacionPrueba" => "email|max:245"
    ];

    $listaCursos = Curso::listarSimple();
    if (!array_key_exists($datos["idCurso"], $listaCursos->toArray()))
      $reglasValidacion["cursoNoValido"] = "required";

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

  public function messages() {
    return [
        "cursoNoValido.required" => "El curso seleccionado no es válido."
    ];
  }

}
