<?php

namespace App\Helpers\Enum;

class TiposBusquedaFecha {

  const Dia = "DIA";
  const Mes = "MES";
  const Anio = "ANIO";
  const RangoDias = "RANGO_DIAS";
  const RangoMeses = "RANGO_MESES";
  const RangoAnios = "RANGO_ANIOS";

  public static function listar() {
    return [
        TiposBusquedaFecha::Dia => "Día",
        TiposBusquedaFecha::Mes => "Mes",
        TiposBusquedaFecha::Anio => "Año",
        TiposBusquedaFecha::RangoDias => "Rango de días",
        TiposBusquedaFecha::RangoMeses => "Rango de meses",
        TiposBusquedaFecha::RangoAnios => "Rango de años"
    ];
  }

}
