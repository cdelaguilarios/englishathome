<?php

namespace App\Http\Requests\Profesor\Pago;

use App\Models\Clase;
use App\Models\PagoClase;
use App\Http\Requests\Request;
use App\Helpers\Enum\MotivosPago;
use App\Helpers\ReglasValidacion;

class FormularioRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();
    $datos["motivo"] = ReglasValidacion::formatoDato($datos, "motivo");
    $datos["monto"] = ReglasValidacion::formatoDato($datos, "monto");
    $datos["datosClases"] = ReglasValidacion::formatoDato($datos, "datosClases");
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $datos = $this->all();
    $reglasValidacion = [
        "descripcion" => "max:255",
        "imagenComprobante" => "image",
        "monto" => ["required", "regex:" . ReglasValidacion::RegexDecimal]
    ];

    $listaMotivosPago = MotivosPago::listar();
    if (!array_key_exists($datos["motivo"], $listaMotivosPago)) {
      $reglasValidacion["motivoNoValido"] = "required";
    }

    if ($datos["motivo"] == MotivosPago::Clases) {
      $reglasValidacion["imagenDocumentoVerificacion"] = "required|image";
      $datosClases = explode(",", $datos["datosClases"]);
      $datosClasesValidas = (count($datosClases) > 0);
      $totalClasesValidas = 0;
      foreach ($datosClases as $datClase) {
        if (trim($datClase) == "") {
          continue;
        }
        $idClaseAlumno = explode("-", $datClase);
        if (!(count($idClaseAlumno) == 2 && ctype_digit("" . $idClaseAlumno[0]) && ctype_digit("" . $idClaseAlumno[1]) && Clase::verificarExistencia($idClaseAlumno[0], $idClaseAlumno[1]) && PagoClase::totalXProfesor($idClaseAlumno[1]) == 0)) {
          $datosClasesValidas = FALSE;
          break;
        } else {
          $totalClasesValidas++;
        }
      }
      if (!$datosClasesValidas || $totalClasesValidas == 0) {
        $reglasValidacion["datosClasesNoValidos"] = "required";
      }
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

  public function messages() {
    return [
        "motivoNoValido.required" => "El motivo seleccionado del pago no es válido.",
        "datosClasesNoValidos.required" => "Los datos de las clases seleccionadas nos son válidas."
    ];
  }

}
