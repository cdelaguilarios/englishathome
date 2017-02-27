<?php

namespace App\Helpers;

use Carbon\Carbon;
use App\Helpers\Enum\TiposBusquedaFecha;

class Util {

  public static function filtrosBusqueda($nombreTabla, &$entidades, $nombreCampoFecha, $datos) {
    if (isset($datos["estado"])) {
      $entidades->where($nombreTabla . ".estado", $datos["estado"]);
    }
    if (isset($datos["tipoBusquedaFecha"])) {
      $fechaBusIni = new Carbon('first day of this month');
      $fechaBusFin = new Carbon('last day of this month');
      if ($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::Dia && isset($datos["fechaDia"])) {
        $fechaBusIni = Carbon::createFromFormat("d/m/Y H:i:s", $datos["fechaDia"] . " 00:00:00");
        $fechaBusFin = Carbon::createFromFormat("d/m/Y H:i:s", $datos["fechaDia"] . " 23:59:59");
      } else if ($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::Mes && isset($datos["fechaMesInicio"])) {
        $fechaBusIni = Carbon::createFromFormat("d/m/Y H:i:s", "01/" . $datos["fechaMesInicio"] . " 00:00:00");
        $fechaBusFin = Carbon::createFromFormat("d/m/Y H:i:s", "01/" . (isset($datos["fechaMesFin"]) ? $datos["fechaMesFin"] : $datos["fechaMesInicio"]) . " 23:59:59");
        $fechaBusFin->addMonth();
        $fechaBusFin->day = 0;
      } else if ($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::Anho && isset($datos["fechaAnhoInicio"])) {
        $fechaBusIni = Carbon::createFromFormat("d/m/Y H:i:s", "01/01/" . $datos["fechaAnhoInicio"] . " 00:00:00");
        $fechaBusFin = Carbon::createFromFormat("d/m/Y H:i:s", "01/12/" . (isset($datos["fechaAnhoFin"]) ? $datos["fechaAnhoFin"] : $datos["fechaAnhoInicio"]) . " 23:59:59");
        $fechaBusFin->addMonth();
        $fechaBusFin->day = 0;
      } else if ($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::RangoFecha && isset($datos["fechaInicio"]) && isset($datos["fechaFin"])) {
        $fechaBusIni = Carbon::createFromFormat("d/m/Y H:i:s", $datos["fechaInicio"] . " 00:00:00");
        $fechaBusFin = Carbon::createFromFormat("d/m/Y H:i:s", $datos["fechaFin"] . " 23:59:59");
      }
      $entidades->whereBetween($nombreTabla . "." . $nombreCampoFecha, [$fechaBusIni, $fechaBusFin]);
    }
  }

}
