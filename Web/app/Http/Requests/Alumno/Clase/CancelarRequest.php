<?php

namespace App\Http\Requests\Alumno\Clase;

use Config;
use App\Models\Clase;
use App\Models\Docente;
use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;
use App\Helpers\Enum\TiposCancelacionClase;

class CancelarRequest extends Request {

    public function authorize() {
        return true;
    }

    protected function getValidatorInstance() {
        $datos = $this->all();
        $datos["idClase"] = (isset($datos["idClase"]) && $datos["idClase"] != "" ? $datos["idClase"] : NULL);
        $datos["idAlumno"] = (isset($datos["idAlumno"]) && $datos["idAlumno"] != "" ? $datos["idAlumno"] : NULL);
        $datos["idProfesor"] = (isset($datos["idProfesor"]) && $datos["idProfesor"] != "" ? $datos["idProfesor"] : NULL);
        $datos["pagoProfesor"] = (isset($datos["pagoProfesor"]) ? $datos["pagoProfesor"] : NULL);
        $datos["tipoCancelacion"] = (isset($datos["tipoCancelacion"]) ? $datos["tipoCancelacion"] : NULL);
        $datos["reprogramarCancelacionAlumno"] = (isset($datos["reprogramarCancelacionAlumno"]) ? 1 : 0);
        $datos["reprogramarCancelacionProfesor"] = (isset($datos["reprogramarCancelacionProfesor"]) ? 1 : 0);
        $datos["fecha"] = (isset($datos["fecha"]) ? $datos["fecha"] : NULL);
        $datos["horaInicio"] = (isset($datos["horaInicio"]) ? $datos["horaInicio"] : NULL);
        $datos["duracion"] = (isset($datos["duracion"]) ? $datos["duracion"] : NULL);
        $datos["idDocente"] = (isset($datos["idDocente"]) && $datos["idDocente"] != "" ? $datos["idDocente"] : NULL);
        $datos["costoHoraDocente"] = (isset($datos["costoHoraDocente"]) ? $datos["costoHoraDocente"] : NULL);
        $this->getInputSource()->replace($datos);
        return parent::getValidatorInstance();
    }

    public function rules() {
        $datos = $this->all();
        $reglasValidacion = [
            "idClase" => "required",
            "idAlumno" => "required",
            "pagoProfesor" => ["regex:" . ReglasValidacion::RegexDecimal]
        ];

        $listaTiposCancelacion = TiposCancelacionClase::listar();
        if (!(!is_null($datos["tipoCancelacion"]) && array_key_exists($datos["tipoCancelacion"], $listaTiposCancelacion))) {
            $reglasValidacion["tipoCancelacionNoValido"] = "required";
        }
        if (!(is_null($datos["idAlumno"]) || is_null($datos["idClase"]) || Clase::verificarExistencia($datos["idAlumno"], $datos["idClase"]))) {
            $reglasValidacion["claseNoValida"] = "required";
        }
        //Profesor de la clase cancelada
        if (!(is_null($datos["idProfesor"]) || Docente::verificarExistencia($datos["idProfesor"]))) {
            $reglasValidacion["profesorNoValido"] = "required";
        }

        //Reprogramación
        if ($datos["reprogramarCancelacionAlumno"] == 1 || $datos["reprogramarCancelacionProfesor"] == 1) {
            $reglasValidacion += [
                "fecha" => "required|date_format:d/m/Y",
                "horaInicio" => "required|numeric|between:" . ((int) Config::get("eah.minHorario") * 3600) . "," . ((int) Config::get("eah.maxHorario") * 3600),
                "duracion" => "required|numeric|between:" . ((int) Config::get("eah.minHorasClase") * 3600) . "," . ((int) Config::get("eah.maxHorasClase") * 3600),
                "costoHoraDocente" => ["regex:" . ReglasValidacion::RegexDecimal]
            ];
            //Docente para la nueva clase (reprogramación)
            if (!(is_null($datos["idDocente"]) || Docente::verificarExistencia($datos["idDocente"]))) {
                $reglasValidacion["docenteNoValido"] = "required";
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
            "tipoCancelacionNoValido.required" => "El tipo de cancelación seleccionado no es válido",
            "claseNoValida.required" => "La clase seleccionada no es válida",
            "profesorNoValido.required" => "El profesor de la clase cancelada no es válido",
            "docenteNoValido.required" => "El docente seleccionado no es válido"
        ];
    }

}
