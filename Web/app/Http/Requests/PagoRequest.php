<?php

namespace App\Http\Requests;

use App\Models\Curso;
use App\Models\Docente;
use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;
use App\Helpers\Enum\MotivosPago;
use App\Helpers\Enum\TiposEntidad;
use App\Helpers\Enum\GenerosEntidad;

class PagoRequest extends Request {

    public function authorize() {
        return true;
    }

    protected function getValidatorInstance() {
        $data = $this->all();

        $data["motivo"] = (isset($data["motivo"]) ? $data["motivo"] : NULL);
        $data["monto"] = (isset($data["monto"]) ? $data["monto"] : NULL);
        $data["usarSaldoFavor"] = (isset($data["usarSaldoFavor"]) ? 1 : 0);
        $data["costoHoraClase"] = (isset($data["costoHoraClase"]) ? $data["costoHoraClase"] : NULL);
        $data["fechaInicioClases"] = (isset($data["fechaInicioClases"]) ? $data["fechaInicioClases"] : NULL);
        $data["periodoClases"] = (isset($data["periodoClases"]) ? $data["periodoClases"] : NULL);
        $data["idDocente"] = (isset($data["idDocente"]) && $data["idDocente"] != "" ? $data["idDocente"] : NULL);
        $data["costoHoraDocente"] = (isset($data["costoHoraDocente"]) ? $data["costoHoraDocente"] : NULL);
        $data["datosNotificacionClases"] = (isset($data["datosNotificacionClases"]) ? $data["datosNotificacionClases"] : NULL);

        $data["generarClases"] = (isset($data["generarClases"]) ? $data["generarClases"] : 0);
        $data["docentesDisponibles"] = (isset($data["docentesDisponibles"]) ? $data["docentesDisponibles"] : 0);
        $data["tipoDocente"] = (isset($data["tipoDocente"]) ? $data["tipoDocente"] : NULL);
        $data["generoDocente"] = (isset($data["generoDocente"]) ? $data["generoDocente"] : NULL);
        $data["idCursoDocente"] = (isset($data["idCursoDocente"]) ? $data["idCursoDocente"] : NULL);

        $this->getInputSource()->replace($data);
        return parent::getValidatorInstance();
    }

    public function rules() {
        $data = $this->all();

        $reglasValidacion = [
            "descripcion" => "max:255",
            "imagenComprobante" => "image",
            "monto" => ["required", "regex:" . ReglasValidacion::RegexDecimal]
        ];

        $listaMotivosPago = MotivosPago::listar();
        if (!(!is_null($data["motivo"]) && array_key_exists($data["motivo"], $listaMotivosPago))) {
            $reglasValidacion["motivoNoValido"] = "required";
        }

        if ($data["motivo"] == MotivosPago::Clases) {
            $reglasValidacion += [
                "costoHoraClase" => ["required", "regex:" . ReglasValidacion::RegexDecimal],
                "fechaInicioClases" => "required|date_format:d/m/Y",
                "periodoClases" => "required|numeric|digits_between :1,11|min:1"
            ];

            if ($data["generarClases"] == 1) {
                
            } else if ($data["docentesDisponibles"] == 1) {
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
            } else {
                $reglasValidacion += [
                    "saldoFavor" => ["regex:" . ReglasValidacion::RegexDecimal]
                ];
                if (!is_null($data["idDocente"])) {
                    $reglasValidacion += [
                        "costoHoraDocente" => ["required", "regex:" . ReglasValidacion::RegexDecimal]
                    ];
                    if (!Docente::verificarExistencia($data["idDocente"])) {
                        $reglasValidacion["docenteNoValido"] = "required";
                    }
                }
                if (!ReglasValidacion::validarDatosNotificacionClasesPago($data["datosNotificacionClases"])) {
                    $reglasValidacion["datosNotificacionClasesNoValido"] = "required";
                }
            }
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
            "motivoNoValido.required" => "El motivo seleccionado del pago no es válido",
            "generoDocenteNoValido.required" => "El genero seleccionado para filtrar la lista de profesores o postulantes no es válido",
            "idCursoDocenteNoValido.required" => "El curso seleccionado para filtrar la lista de profesores o postulantes no es válido",
            "tipoDocenteNoValido.required" => "El tipo seleccionado para filtrar la lista de profesores o postulantes no es válido",
            "docenteNoValido.required" => "El docente seleccionado no es válido",
            "datosNotificacionClasesNoValido.required" => "Los datos de notificación de la clases seleccionadas no son válidas"
        ];
    }

}
