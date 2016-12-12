<?php

namespace App\Helpers\Enum;

class EstadosPago {

    const Realizado = "REALIZADO";
    const Pendiente = "PENDIENTE";

    public static function Listar() {
        return [
            EstadosPago::Realizado => ['Realizado', 'label-success'],
            EstadosPago::Pendiente => ['Pendiente', 'label-warning']
        ];
    }

}
