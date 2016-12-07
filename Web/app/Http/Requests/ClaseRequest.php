<?php

namespace App\Http\Requests;

use App\Models\Curso;
use App\Models\Docente;
use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;
use App\Helpers\Enum\MotivosClase;
use App\Helpers\Enum\TiposEntidad;
use App\Helpers\Enum\GenerosEntidad;

class ClaseRequest extends Request {

    public function authorize() {
        return true;
    }

    protected function getValidatorInstance() {
        $data = $this->all();

        $data["docentesDisponibles"] = (isset($data["docentesDisponibles"]) ? $data["docentesDisponibles"] : 0);
        $data["generoDocenteClase"] = (isset($data["generoDocenteClase"]) ? $data["generoDocenteClase"] : NULL);
        $data["idCursoDocenteClase"] = (isset($data["idCursoDocenteClase"]) ? $data["idCursoDocenteClase"] : NULL);
        $data["tipoDocenteClase"] = (isset($data["tipoDocenteClase"]) ? $data["tipoDocenteClase"] : NULL);
        $data["fechaClaseReprogramada"] = (isset($data["fechaClaseReprogramada"]) ? $data["fechaClaseReprogramada"] : NULL);

        $this->getInputSource()->replace($data);
        return parent::getValidatorInstance();
    }

    public function rules() {
        $data = $this->all();

        $reglasValidacion = [
            'fechaClaseReprogramada' => 'required|date_format:d/m/Y',
            'duracionClaseReprogramada' => 'required|numeric|digits_between:1,2|between:1,24',
        ];

        if ($data["docentesDisponibles"] == 1) {
            $listaGeneros = GenerosEntidad::listar();
            if (!(!is_null($data["generoDocenteClase"]) && ($data["generoDocenteClase"] == "" || array_key_exists($data["generoDocenteClase"], $listaGeneros)))) {
                $reglasValidacion['generoDocenteClaseNoValido'] = 'required';
            }
            $listaCursos = Curso::listarSimple()->toArray();
            if (!(!is_null($data["idCursoDocenteClase"]) && ($data["idCursoDocenteClase"] == "" || array_key_exists($data["idCursoDocenteClase"], $listaCursos)))) {
                $reglasValidacion['idCursoDocenteClaseNoValido'] = 'required';
            }
            $listaTiposDocente = TiposEntidad::listarTiposDocente();
            if (!(!is_null($data["tipoDocenteClase"]) && (array_key_exists($data["tipoDocenteClase"], $listaTiposDocente)))) {
                $reglasValidacion['tipoDocenteClaseNoValido'] = 'required';
            }
        }

        switch ($this->method()) {
            case 'GET':
            case 'DELETE': {
                    return [];
                }
            case 'POST': {
                    return $reglasValidacion;
                }
            case 'PUT':
            case 'PATCH': {
                    return $reglasValidacion;
                }
            default:break;
        }
    }

    public function messages() {
        return [
            'generoDocenteClaseNoValido.required' => 'El genero seleccionado para filtrar la lista de profesores o postulantes no es válido',
            'idCursoDocenteClaseNoValido.required' => 'El curso seleccionado para filtrar la lista de profesores o postulantes no es válido',
            'tipoDocenteClaseNoValido.required' => 'El tipo seleccionado para filtrar la lista de profesores o postulantes no es válido'
        ];
    }

}
