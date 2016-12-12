<?php

namespace App\Helpers\Enum;

class EstadosClase {

    const Programada = "PROGRAMADA";
    const Cancelada = "CANCELADA";

    public static function Listar() {
        return [
            EstadosClase::Programada => ['Programada', 'label-primary'],
            EstadosClase::Cancelada => ['Cancelada', 'label-danger']
        ];
    }

}
