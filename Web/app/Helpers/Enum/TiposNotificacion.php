<?php

namespace App\Helpers\Enum;

class TiposNotificacion/* - */ {

  const Notificacion = "NOTIFICACION";
  const Pago = "PAGO";
  const Clase = "CLASE";
  
  const IconoDefecto = "fa-bullhorn";
  const ClaseColorIconoDefecto = "bg-yellow";
  const ClaseTextoColorIconoDefecto = "text-yellow";

  public static function listar()/* - */ {
    return [
        TiposNotificacion::Notificacion => ["Notificación", "fa-bullhorn", "bg-yellow", "text-yellow"],
        TiposNotificacion::Pago => ["Pago", "fa-dollar", "bg-green", "text-green"],
        TiposNotificacion::Clase => ["Clase", "flaticon-teach", "bg-blue", "text-blue"]
    ];
  }

}
