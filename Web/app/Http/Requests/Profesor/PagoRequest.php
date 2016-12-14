<?php

namespace App\Http\Requests\Profesor;

use App\Models\Clase;
use App\Http\Requests\Request;
use App\Helpers\Enum\MotivosPago;
use App\Helpers\ReglasValidacion;

class PagoRequest extends Request {

    public function authorize() {
        return true;
    }

    protected function getValidatorInstance() {
        $datos = $this->all();
        $datos["motivo"] = (isset($datos["motivo"]) ? $datos["motivo"] : NULL);
        $datos["monto"] = (isset($datos["monto"]) ? $datos["monto"] : NULL);
        $datos["datosClases"] = (isset($datos["datosClases"]) ? $datos["datosClases"] : "");
        $this->getInputSource()->replace($datos);
        return parent::getValidatorInstance();
    }

    public function rules() {
        $datos = $this->all();
        $reglasValidacion = [
            "monto" => ["required", "regex:" . ReglasValidacion::RegexDecimal],
            "imagenComprobante" => "image"
        ];

        $listaMotivosPago = MotivosPago::listar();
        if (!(!is_null($datos["motivo"]) && array_key_exists($datos["motivo"], $listaMotivosPago))) {
            $reglasValidacion["motivoNoValido"] = "required";
        }

        if ($datos["motivo"] == MotivosPago::Clases) {
            $reglasValidacion += [
                "monto" => ["required", "regex:" . ReglasValidacion::RegexDecimal],
                "imagenComprobante" => "image"
            ];
            $datosClases = explode(",", $datos["datosClases"]);
            $datosClasesValidos = (count($datosClases) > 0);
            $totalClasesValidas = 0;
            foreach ($datosClases as $datClase) {
                if (trim($datClase) == "") {
                    continue;
                }
                $idClaseAlumno = explode("-", $datClase);
                if (!(count($idClaseAlumno) == 2 && ctype_digit("" . $idClaseAlumno[0]) && ctype_digit("" . $idClaseAlumno[1]) && Clase::verificarExistencia($idClaseAlumno[0], $idClaseAlumno[1]))) {
                    $datosClasesValidos = FALSE;
                    break;
                } else {
                    $totalClasesValidas++;
                }
            }
            if (!$datosClasesValidos || $totalClasesValidas == 0) {
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
            "motivoNoValido.required" => "El motivo seleccionado del pago no es válido",
            "datosClasesNoValidos.required" => "Los datos de las clases seleccionadas nos son válidas"
        ];
    }

}
