<?php

namespace App\Http\Requests;

use App\Models\Curso;
use App\Models\NivelIngles;
use App\Models\TipoDocumento;
use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;

class AlumnoRequest extends Request {

    public function authorize() {
        return true;
    }

    protected function getValidatorInstance() {
        $data = $this->all();

        $data["imagenPerfil"] = (isset($data["imagenPerfil"]) ? $data["imagenPerfil"] : NULL);
        $data["idTipoDocumento"] = (isset($data["idTipoDocumento"]) ? $data["idTipoDocumento"] : NULL);
        $data["codigoDepartamento"] = (isset($data["codigoDepartamento"]) ? $data["codigoDepartamento"] : NULL);
        $data["codigoProvincia"] = (isset($data["codigoProvincia"]) ? $data["codigoProvincia"] : NULL);
        $data["codigoDistrito"] = (isset($data["codigoDistrito"]) ? $data["codigoDistrito"] : NULL);
        $data["codigoUbigeo"] = (isset($data["codigoUbigeo"]) ? $data["codigoUbigeo"] : NULL);
        $data["referenciaDireccion"] = (isset($data["referenciaDireccion"]) ? $data["referenciaDireccion"] : NULL);
        $data["geoLatitud"] = (isset($data["geoLatitud"]) ? $data["geoLatitud"] : NULL);
        $data["geoLongitud"] = (isset($data["geoLongitud"]) ? $data["geoLongitud"] : NULL);
        $data["idNivelIngles"] = (isset($data["idNivelIngles"]) ? $data["idNivelIngles"] : NULL);
        $data["inglesLugarEstudio"] = (isset($data["inglesLugarEstudio"]) ? $data["inglesLugarEstudio"] : NULL);
        $data["inglesPracticaComo"] = (isset($data["inglesPracticaComo"]) ? $data["inglesPracticaComo"] : NULL);
        $data["inglesObjetivo"] = (isset($data["inglesObjetivo"]) ? $data["inglesObjetivo"] : NULL);
        $data["conComputadora"] = (isset($data["conComputadora"]) && $data["conComputadora"] == "on" ? 1 : 0);
        $data["conInternet"] = (isset($data["conInternet"]) && $data["conInternet"] == "on" ? 1 : 0);
        $data["conPlumonPizarra"] = (isset($data["conPlumonPizarra"]) && $data["conPlumonPizarra"] == "on" ? 1 : 0);
        $data["conAmbienteClase"] = (isset($data["conAmbienteClase"]) && $data["conAmbienteClase"] == "on" ? 1 : 0);
        $data["idCurso"] = (isset($data["idCurso"]) ? $data["idCurso"] : NULL);
        $data["horario"] = (isset($data["horario"]) ? $data["horario"] : NULL);
        $data["comentarioAdicional"] = (isset($data["comentarioAdicional"]) ? $data["comentarioAdicional"] : NULL);

        $this->getInputSource()->replace($data);
        return parent::getValidatorInstance();
    }

    public function rules() {
        $data = $this->all();

        $reglasValidacion = [
            'nombre' => ['required', 'max:255', 'regex:' . ReglasValidacion::RegexAlfabetico],
            'apellido' => ['required', 'max:255', 'regex:' . ReglasValidacion::RegexAlfabetico],
            'telefono' => 'required|max:30',
            'fechaNacimiento' => 'required|date_format:d/m/Y',
            'numeroDocumento' => 'required|numeric|digits_between:8,20',
            'correoElectronico' => 'required|email|max:245',
            'imagenPerfil' => 'image',
            'direccion' => 'required|max:255',
            'referenciaDireccion' => 'max:255',
            'geoLatitud' => ['regex:' . ReglasValidacion::RegexGeoLatitud],
            'geoLongitud' => ['regex:' . ReglasValidacion::RegexGeoLongitud],
            'inglesLugarEstudio' => 'max:255',
            'inglesPracticaComo' => 'max:255',
            'inglesObjetivo' => 'max:255',
            'numeroHorasClase' => 'required|numeric|digits_between:1,2|between:1,24',
            'fechaInicioClase' => 'required|date_format:d/m/Y',
            'comentarioAdicional' => 'max:255'
        ];

        $listaTiposDocumentos = TipoDocumento::listarSimple();
        if (!(!is_null($data["idTipoDocumento"]) && array_key_exists($data["idTipoDocumento"], $listaTiposDocumentos->toArray()))) {
            $reglasValidacion['tipoDocumenoNoValido'] = 'required';
        }

        if (!ReglasValidacion::validarUbigeo($data["codigoDepartamento"], $data["codigoProvincia"], $data["codigoDistrito"], $data["codigoUbigeo"])) {
            $reglasValidacion["ubigeoNoValido"] = "required";
        }

        $listaNivelesIngles = NivelIngles::listarSimple();
        if (!(!is_null($data["idNivelIngles"]) && array_key_exists($data["idNivelIngles"], $listaNivelesIngles->toArray()))) {
            $reglasValidacion['nivelInglesNoValido'] = 'required';
        }

        $listaCursos = Curso::listarSimple();
        if (!(!is_null($data["idCurso"]) && array_key_exists($data["idCurso"], $listaCursos->toArray()))) {
            $reglasValidacion['cursoNoValido'] = 'required';
        }

        if (!ReglasValidacion::validarHorario($data["horario"])) {
            $reglasValidacion["horarioNoValido"] = "required";
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
            'tipoDocumenoNoValido.required' => 'El tipo de documento seleccionado no es válido',
            'ubigeoNoValido.required' => 'Los datos de dirección ingresados no son válidos.',
            'nivelInglesNoValido.required' => 'El nivel de inglés seleccionado no es válido',
            'cursoNoValido.required' => 'El curso seleccionado no es válido',
            'horarioNoValido.required' => 'El horario seleccionado no es válido'
        ];
    }

}
