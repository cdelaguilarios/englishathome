<?php

namespace App\Helpers\Enum;

class TiposHistorial {

  const Notificacion = "NOTIFICACION";
  const Pago = "PAGO";
  const Correo = "CORREO";
  const IconoDefecto = "fa-bullhorn";
  const ClaseColorIconoDefecto = "bg-yellow";
  const ClaseTextoColorIconoDefecto = "text-yellow";

  public static function listar() {
    return [
        TiposHistorial::Notificacion => ["Notificación", "fa-bullhorn", "bg-yellow", "text-yellow"],
        TiposHistorial::Pago => ["Pago", "fa-dollar", "bg-green", "text-green"],
        TiposHistorial::Correo => ["Correo", "fa-envelope", "bg-blue", "text-blue"]
    ];
  }

}
