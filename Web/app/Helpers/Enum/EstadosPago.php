<?php

namespace App\Helpers\Enum;

class EstadosPago {

    const Realizado = "REALIZADO";

    public static function Listar() {
        return [
            EstadosPago::Realizado => ['Realizado', 'label-success']
        ];
    }

}
