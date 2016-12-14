<?php

namespace App\Helpers\Enum;

class EstadosClase {

    const Programada = "PROGRAMADA";
    const Cancelada = "CANCELADA";
    const PendienteConfirmar = "PENDIENTE_CONFIRMAR";
    const Realizada = "REALIZADA";

    public static function Listar() {
        return [
            EstadosClase::Programada => ['Programada', 'label-primary'],
            EstadosClase::Cancelada => ['Cancelada', 'label-danger'],
            EstadosClase::PendienteConfirmar => ['Pendiente de confirmación', 'label-warning'],
            EstadosClase::Realizada => ['Realizada', 'label-success']
        ];
    }

}
