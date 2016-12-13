<?php

namespace App\Http\Requests;

use Config;
use App\Models\Curso;
use App\Models\Clase;
use App\Models\Docente;
use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;
use App\Helpers\Enum\TiposEntidad;
use App\Helpers\Enum\GenerosEntidad;
use App\Helpers\Enum\TiposCancelacionClase;

class ClaseRequest extends Request {

    public function authorize() {
        return true;
    }

    protected function getValidatorInstance() {
        $data = $this->all();

        $data["docentesDisponibles"] = (isset($data["docentesDisponibles"]) ? $data["docentesDisponibles"] : 0);
        $data["tipoDocente"] = (isset($data["tipoDocente"]) ? $data["tipoDocente"] : NULL);
        $data["generoDocente"] = (isset($data["generoDocente"]) ? $data["generoDocente"] : NULL);
        $data["idCursoDocente"] = (isset($data["idCursoDocente"]) ? $data["idCursoDocente"] : NULL);

        $data["cancelar"] = (isset($data["cancelar"]) ? $data["cancelar"] : 0);
        $data["idClase"] = (isset($data["idClase"]) && $data["idClase"] != "" ? $data["idClase"] : NULL);
        $data["idAlumno"] = (isset($data["idAlumno"]) && $data["idAlumno"] != "" ? $data["idAlumno"] : NULL);
        $data["idProfesor"] = (isset($data["idProfesor"]) && $data["idProfesor"] != "" ? $data["idProfesor"] : NULL);
        $data["pagoProfesor"] = (isset($data["pagoProfesor"]) ? $data["pagoProfesor"] : NULL);
        $data["tipoCancelacion"] = (isset($data["tipoCancelacion"]) ? $data["tipoCancelacion"] : NULL);
        $data["reprogramarCancelacionAlumno"] = (isset($data["reprogramarCancelacionAlumno"]) ? 1 : 0);
        $data["reprogramarCancelacionProfesor"] = (isset($data["reprogramarCancelacionProfesor"]) ? 1 : 0);

        $data["fecha"] = (isset($data["fecha"]) ? $data["fecha"] : NULL);
        $data["horaInicio"] = (isset($data["horaInicio"]) ? $data["horaInicio"] : NULL);
        $data["duracion"] = (isset($data["duracion"]) ? $data["duracion"] : NULL);
        $data["idDocente"] = (isset($data["idDocente"]) && $data["idDocente"] != "" ? $data["idDocente"] : NULL);
        $data["costoHoraDocente"] = (isset($data["costoHoraDocente"]) ? $data["costoHoraDocente"] : NULL);

        $data["notificar"] = (isset($data["notificar"]) ? 1 : 0);
        $data["costoHora"] = (isset($data["costoHora"]) ? $data["costoHora"] : NULL);
        $data["numeroPeriodo"] = (isset($data["numeroPeriodo"]) ? $data["numeroPeriodo"] : NULL);

        $this->getInputSource()->replace($data);
        return parent::getValidatorInstance();
    }

    public function rules() {
        $data = $this->all();

        $reglasValidacion = [];

        if ($data["docentesDisponibles"] == 1) {
            $reglasValidacion = [
                "fecha" => "required|date_format:d/m/Y",
                "horaInicio" => "required|numeric|between:" . ((int) Config::get("eah.minHorario") * 3600) . "," . ((int) Config::get("eah.maxHorario") * 3600),
                "duracion" => "required|numeric|between:" . ((int) Config::get("eah.minHorasClase") * 3600) . "," . ((int) Config::get("eah.maxHorasClase") * 3600)
            ];
            $listaGeneros = GenerosEntidad::listar();
            if (!(!is_null($data["generoDocente"]) && ($data["generoDocente"] == "" || array_key_exists($data["generoDocente"], $listaGeneros)))) {
                $reglasValidacion["generoDocenteNoValido"] = "required";
            }
            $listaCursos = Curso::listarSimple()->toArray();
            if (!(!is_null($data["idCursoDocente"]) && ($data["idCursoDocente"] == "" || array_key_exists($data["idCursoDocente"], $listaCursos)))) {
                $reglasValidacion["idCursoDocenteNoValido"] = "required";
            }
            $listaTiposDocente = TiposEntidad::listarTiposDocente();
            if (!(!is_null($data["tipoDocente"]) && (array_key_exists($data["tipoDocente"], $listaTiposDocente)))) {
                $reglasValidacion["tipoDocenteNoValido"] = "required";
            }
        } else if ($data["cancelar"] == 1) {
            $reglasValidacion = [
                "idClase" => "required",
                "idAlumno" => "required",
                "pagoProfesor" => ["regex:" . ReglasValidacion::RegexDecimal]
            ];

            $listaTiposCancelacion = TiposCancelacionClase::listar();
            if (!(!is_null($data["tipoCancelacion"]) && array_key_exists($data["tipoCancelacion"], $listaTiposCancelacion))) {
                $reglasValidacion["tipoCancelacionNoValido"] = "required";
            }
            if (!(is_null($data["idAlumno"]) || is_null($data["idClase"]) || Clase::verificarExistencia($data["idAlumno"], $data["idClase"]))) {
                $reglasValidacion["claseNoValida"] = "required";
            }
            //Profesor de la clase cancelada
            if (!(is_null($data["idProfesor"]) || Docente::verificarExistencia($data["idProfesor"]))) {
                $reglasValidacion["profesorNoValido"] = "required";
            }

            if ($data["reprogramarCancelacionAlumno"] == 1 || $data["reprogramarCancelacionProfesor"] == 1) {
                $reglasValidacion += [
                    "fecha" => "required|date_format:d/m/Y",
                    "horaInicio" => "required|numeric|between:" . ((int) Config::get("eah.minHorario") * 3600) . "," . ((int) Config::get("eah.maxHorario") * 3600),
                    "duracion" => "required|numeric|between:" . ((int) Config::get("eah.minHorasClase") * 3600) . "," . ((int) Config::get("eah.maxHorasClase") * 3600),
                    "costoHoraDocente" => ["regex:" . ReglasValidacion::RegexDecimal]
                ];
                //Docente para la nueva clase (reprogramación)
                if (!(is_null($data["idDocente"]) || Docente::verificarExistencia($data["idDocente"]))) {
                    $reglasValidacion["docenteNoValido"] = "required";
                }
            }
        } else {
            $reglasValidacion += [
                "costoHora" => ["required", "regex:" . ReglasValidacion::RegexDecimal],
                "numeroPeriodo" => "required|numeric|digits_between :1,11|min:1",
                "fecha" => "required|date_format:d/m/Y",
                "horaInicio" => "required|numeric|between:" . ((int) Config::get("eah.minHorario") * 3600) . "," . ((int) Config::get("eah.maxHorario") * 3600),
                "duracion" => "required|numeric|between:" . ((int) Config::get("eah.minHorasClase") * 3600) . "," . ((int) Config::get("eah.maxHorasClase") * 3600),
                "costoHoraDocente" => ["regex:" . ReglasValidacion::RegexDecimal]
            ];
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
            "tipoCancelacionNoValido.required" => "El tipo de cancelación seleccionado no es válido",
            "claseNoValida.required" => "La clase seleccionada no es válida",
            "profesorNoValido.required" => "El profesor de la clase cancelada no es válido",
            "generoDocenteNoValido.required" => "El genero seleccionado para filtrar la lista de profesores o postulantes no es válido",
            "idCursoDocenteNoValido.required" => "El curso seleccionado para filtrar la lista de profesores o postulantes no es válido",
            "tipoDocenteNoValido.required" => "El tipo seleccionado para filtrar la lista de profesores o postulantes no es válido",
            "docenteNoValido.required" => "El docente seleccionado no es válido"
        ];
    }

}
