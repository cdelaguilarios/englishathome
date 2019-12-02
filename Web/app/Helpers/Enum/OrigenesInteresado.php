<?php

namespace App\Helpers\Enum;

class OrigenesInteresado/* - */ {

  const Web = "WEB";
  const Facebook = "FACEBOOK";
  const Correo = "CORREO";
  const WhatsApp = "WHATSAPP";
  const Llamada = "LLAMADA";
  const LinkedIn = "LINKEDIN";

  public static function listar()/* - */ {
    return [
        OrigenesInteresado::Web => "Web",
        OrigenesInteresado::Facebook => "Facebook",
        OrigenesInteresado::Correo => "Correo",
        OrigenesInteresado::WhatsApp => "WhatsApp",
        OrigenesInteresado::Llamada => "Llamada",
        OrigenesInteresado::LinkedIn => "LinkedIn",
    ];
  }

}
