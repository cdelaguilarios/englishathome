<?php

namespace App\Helpers\Enum;

class EstadosProfesor {

    const Registrado = "REGISTRADO";
    const Activo = "ACTIVO";
    const Inactivo = "INACTIVO";

    public static function Listar() {
        return [
            EstadosProfesor::Registrado => ['Registrado', 'label-info'],
            EstadosProfesor::Activo => ['Activo', 'label-success'],
            EstadosProfesor::Inactivo => ['Inactivo', 'label-warning']
        ];
    }

}
