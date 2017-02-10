<?php

namespace App\Helpers\Enum;

class TiposBusquedaFecha {

  const Dia = "DIA";
  const Mes = "MES";
  const Anho = "ANHO";
  const RangoFecha = "RANGO_FECHA";

  public static function listar() {
    return [
        TiposBusquedaFecha::Dia => "Día",
        TiposBusquedaFecha::Mes => "Mes",
        TiposBusquedaFecha::Anho => "Año",
        TiposBusquedaFecha::RangoFecha => "Rango de fecha"
    ];
  }

}
