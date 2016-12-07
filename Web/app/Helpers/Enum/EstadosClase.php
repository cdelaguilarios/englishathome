<?php

namespace App\Helpers\Enum;

class EstadosClase {

    const Programada = "PROGRAMADA";

    public static function Listar() {
        return [
            EstadosClase::Programada => ['Programada', 'label-primary']
        ];
    }

}
