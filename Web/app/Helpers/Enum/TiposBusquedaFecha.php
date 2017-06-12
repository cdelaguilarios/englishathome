<?php

namespace App\Helpers\Enum;

class TiposBusquedaFecha {

  const Dia = "DIA";
  const Mes = "MES";
  const Anho = "ANHO";
  const RangoDias = "RANGO_DIAS";
  const RangoMeses = "RANGO_MESES";
  const RangoAnhos = "RANGO_ANHOS";

  public static function listar() {
    $tiposBusquedaFecha = [
        TiposBusquedaFecha::Dia => "Día",
        TiposBusquedaFecha::Mes => "Mes",
        TiposBusquedaFecha::Anho => "Año",
        TiposBusquedaFecha::RangoDias => "Rango de días",
        TiposBusquedaFecha::RangoMeses => "Rango de meses",
        TiposBusquedaFecha::RangoAnhos => "Rango de años"
    ];
    return $tiposBusquedaFecha;
  }

}
