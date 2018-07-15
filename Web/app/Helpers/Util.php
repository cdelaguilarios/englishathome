<?php

namespace App\Helpers;

use Carbon\Carbon;
use App\Helpers\ReglasValidacion;
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
      } else if ($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::Mes && isset($datos["fechaMes"])) {
        $fechaBusIni = Carbon::createFromFormat("d/m/Y H:i:s", "01/" . $datos["fechaMes"] . " 00:00:00");
        $fechaBusFin = Carbon::createFromFormat("d/m/Y H:i:s", "01/" . $datos["fechaMes"] . " 23:59:59");
        $fechaBusFin->addMonth();
        $fechaBusFin->day = 0;
      } else if ($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::Anho && isset($datos["fechaAnho"])) {
        $fechaBusIni = Carbon::createFromFormat("d/m/Y H:i:s", "01/01/" . $datos["fechaAnho"] . " 00:00:00");
        $fechaBusFin = Carbon::createFromFormat("d/m/Y H:i:s", "01/12/" . $datos["fechaAnho"] . " 23:59:59");
        $fechaBusFin->addMonth();
        $fechaBusFin->day = 0;
      } else if ($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::RangoDias && isset($datos["fechaDiaInicio"]) && isset($datos["fechaDiaFin"])) {
        $fechaBusIni = Carbon::createFromFormat("d/m/Y H:i:s", $datos["fechaDiaInicio"] . " 00:00:00");
        $fechaBusFin = Carbon::createFromFormat("d/m/Y H:i:s", $datos["fechaDiaFin"] . " 23:59:59");
      } else if ($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::RangoMeses && isset($datos["fechaMesInicio"])) {
        $fechaBusIni = Carbon::createFromFormat("d/m/Y H:i:s", "01/" . $datos["fechaMesInicio"] . " 00:00:00");
        $fechaBusFin = Carbon::createFromFormat("d/m/Y H:i:s", "01/" . (isset($datos["fechaMesFin"]) ? $datos["fechaMesFin"] : $datos["fechaMesInicio"]) . " 23:59:59");
        $fechaBusFin->addMonth();
        $fechaBusFin->day = 0;
      } else if ($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::RangoAnhos && isset($datos["fechaAnhoInicio"])) {
        $fechaBusIni = Carbon::createFromFormat("d/m/Y H:i:s", "01/01/" . $datos["fechaAnhoInicio"] . " 00:00:00");
        $fechaBusFin = Carbon::createFromFormat("d/m/Y H:i:s", "01/12/" . (isset($datos["fechaAnhoFin"]) ? $datos["fechaAnhoFin"] : $datos["fechaAnhoInicio"]) . " 23:59:59");
        $fechaBusFin->addMonth();
        $fechaBusFin->day = 0;
      }
      $entidades->whereBetween($nombreTabla . "." . $nombreCampoFecha, [$fechaBusIni, $fechaBusFin]);
    }
  }

  public static function preProcesarDocumentosDocente(&$datos) {
    $datos["nombreDocumentoCv"] = ReglasValidacion::formatoDato($datos, "nombreDocumentoCv");
    $datos["nombreOriginalDocumentoCv"] = ReglasValidacion::formatoDato($datos, "nombreOriginalDocumentoCv");
    $datos["nombreDocumentoCvEliminado"] = ReglasValidacion::formatoDato($datos, "nombreDocumentoCvEliminado");

    $datos["nombreDocumentoCertificadoInternacional"] = ReglasValidacion::formatoDato($datos, "nombreDocumentoCertificadoInternacional");
    $datos["nombreOriginalDocumentoCertificadoInternacional"] = ReglasValidacion::formatoDato($datos, "nombreOriginalDocumentoCertificadoInternacional");
    $datos["nombreDocumentoCertificadoInternacionalEliminado"] = ReglasValidacion::formatoDato($datos, "nombreDocumentoCertificadoInternacionalEliminado");

    $datos["nombreImagenDocumentoIdentidad"] = ReglasValidacion::formatoDato($datos, "nombreImagenDocumentoIdentidad");
    $datos["nombreOriginalImagenDocumentoIdentidad"] = ReglasValidacion::formatoDato($datos, "nombreOriginalImagenDocumentoIdentidad");
    $datos["nombreImagenDocumentoIdentidadEliminado"] = ReglasValidacion::formatoDato($datos, "nombreImagenDocumentoIdentidadEliminado");
  }

  public static  function formatoHora($tiempoSegundos, $incluirSegundos = FALSE) {
    $h = floor($tiempoSegundos / 3600);
    $m = floor($tiempoSegundos % 3600 / 60);
    $s = floor($tiempoSegundos % 3600 % 60);
    return (($h >= 0 ? $h + ":" + ($m < 10 ? "0" : "") : "") + $m + ($incluirSegundos ? ":" + ($s < 10 ? "0" : "") + $s : ""));
  }
}
