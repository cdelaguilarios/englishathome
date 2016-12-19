<?php

namespace App\Helpers\Enum;

class TiposHistorial {

  const Notificacion = "NOTIFICACION";
  const Pago = "PAGO";
  const Correo = "CORREO";
  const IconoDefecto = "fa-bullhorn";
  const ClaseColorIconoDefecto = "bg-yellow";

  public static function Listar() {
    return [
        TiposHistorial::Notificacion => ["Notificación", "fa-bullhorn", "bg-yellow"],
        TiposHistorial::Pago => ["Pago", "fa-dollar", "bg-green"],
        TiposHistorial::Correo => ["Correo", "fa-envelope", "bg-blue"]
    ];
  }

}
