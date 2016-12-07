<?php

namespace App\Http\Requests;

use App\Models\Usuario;
use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;
use App\Helpers\Enum\RolesUsuario;
use App\Helpers\Enum\EstadosUsuario;

class UsuarioRequest extends Request {

    public function authorize() {
        return true;
    }

    protected function getValidatorInstance() {
        $data = $this->all();

        $data["id"] = (isset($data["id"]) ? $data["id"] : 0);   
        $data["nombre"] = (isset($data["nombre"]) ? $data["nombre"] : "");
        $data["apellido"] = (isset($data["apellido"]) ? $data["apellido"] : "");
        $data["password"] = (isset($data["password"]) ? $data["password"] : NULL);
        $data["password_confirmation"] = (isset($data["password_confirmation"]) ? $data["password_confirmation"] : NULL);
        $data["rol"] = (isset($data["rol"]) ? $data["rol"] : NULL);
        $data["estado"] = (isset($data["estado"]) ? $data["estado"] : NULL);

        $this->getInputSource()->replace($data);
        return parent::getValidatorInstance();
    }

    public function rules() {
        $data = $this->all();
        $modoEdicion = ($this->method() == "PATCH");
        $idUsuario = $data["id"];

        $reglasValidacion = [
            'nombre' => ['max:255', 'regex:' . ReglasValidacion::RegexAlfabetico],
            'apellido' => ['max:255', 'regex:' . ReglasValidacion::RegexAlfabetico],
            'email' => 'required|email|max:245|unique:' . Usuario::NombreTabla() . ',email' . 
            ($modoEdicion && !is_null($idUsuario) && is_numeric($idUsuario) ? ',' . $idUsuario . ',idEntidad' : ''),
            'imagenPerfil' => 'image'
        ];

        $roles = RolesUsuario::Listar();
        if (!array_key_exists($data['rol'], $roles)) {
            $reglasValidacion['rolNoValido'] = 'required';
        }
        $estados = EstadosUsuario::Listar(TRUE);
        if (!array_key_exists($data['estado'], $estados)) {
            $reglasValidacion['estadoNoValido'] = 'required';
        }
        if (!$modoEdicion || (!is_null($data["password"]) && $data["password"] != "")) {
            $reglasValidacion['password'] = 'required|confirmed|min:6|max:30';
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
            'email.unique' => 'El correo electrónico ingresado ya esta siendo utilizado.',
            'rolNoValido.required' => 'El rol seleccionado no es válido',
            'estadoNoValido.required' => 'El estado seleccionado no es válido'
        ];
    }

}
