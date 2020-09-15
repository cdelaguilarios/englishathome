<?php

namespace App\Helpers;

use Carbon\Carbon;
use App\Helpers\ReglasValidacion;
use App\Helpers\Enum\TiposBusquedaFecha;

class Util {

  public static function preProcesarFiltrosBusquedaXFechas(&$datos) {
    $datos["tipoBusquedaFecha"] = ReglasValidacion::formatoDato($datos, "tipoBusquedaFecha");
    $datos["fechaDia"] = ReglasValidacion::formatoDato($datos, "fechaDia");
    $datos["fechaMes"] = ReglasValidacion::formatoDato($datos, "fechaMes");
    $datos["fechaAnio"] = ReglasValidacion::formatoDato($datos, "fechaAnio");
    $datos["fechaDiaInicio"] = ReglasValidacion::formatoDato($datos, "fechaDiaInicio");
    $datos["fechaDiaFin"] = ReglasValidacion::formatoDato($datos, "fechaDiaFin");
    $datos["fechaMesInicio"] = ReglasValidacion::formatoDato($datos, "fechaMesInicio");
    $datos["fechaMesFin"] = ReglasValidacion::formatoDato($datos, "fechaMesFin");
    $datos["fechaAnioInicio"] = ReglasValidacion::formatoDato($datos, "fechaAnioInicio");
    $datos["fechaAnioFin"] = ReglasValidacion::formatoDato($datos, "fechaAnioFin");

    if ($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::Dia && !preg_match(ReglasValidacion::RegexFecha, $datos["fechaDia"])) {
      $datos["fechaDia"] = NULL;
    } else if ($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::Mes && !preg_match(ReglasValidacion::RegexFecha, "01/" . $datos["fechaMes"])) {
      $datos["fechaMes"] = NULL;
    } else if ($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::Anio && !preg_match(ReglasValidacion::RegexFecha, "01/01/" . $datos["fechaAnio"])) {
      $datos["fechaAnio"] = NULL;
    } else if ($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::RangoDias) {
      $datos["fechaDiaInicio"] = ((!is_null($datos["fechaDiaInicio"]) && preg_match(ReglasValidacion::RegexFecha, $datos["fechaDiaInicio"])) ? $datos["fechaDiaInicio"] : NULL);
      $datos["fechaDiaFin"] = ((!is_null($datos["fechaDiaFin"]) && preg_match(ReglasValidacion::RegexFecha, $datos["fechaDiaFin"])) ? $datos["fechaDiaFin"] : NULL);
    } else if ($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::RangoMeses) {
      $datos["fechaMesInicio"] = ((!is_null($datos["fechaMesInicio"]) && preg_match(ReglasValidacion::RegexFecha, "01/" . $datos["fechaMesInicio"])) ? $datos["fechaMesInicio"] : NULL);
      $datos["fechaMesFin"] = ((!is_null($datos["fechaMesFin"]) && preg_match(ReglasValidacion::RegexFecha, "01/" . $datos["fechaMesFin"])) ? $datos["fechaMesFin"] : NULL);
    } else if ($datos["tipoBusquedaFecha"] == TiposBusquedaFecha::RangoAnios) {
      $datos["fechaAnioInicio"] = ((!is_null($datos["fechaAnioInicio"]) && preg_match(ReglasValidacion::RegexFecha, "01/01/" . $datos["fechaAnioInicio"])) ? $datos["fechaAnioInicio"] : NULL);
      $datos["fechaAnioFin"] = ((!is_null($datos["fechaAnioFin"]) && preg_match(ReglasValidacion::RegexFecha, "01/01/" . $datos["fechaAnioFin"])) ? $datos["fechaAnioFin"] : NULL);
    }
  }

  public static function aplicarFiltrosBusquedaXFechas(&$elementos, $nombreTabla, $nombreCampoFecha, $datosFiltros) {
    if (isset($datosFiltros["tipoBusquedaFecha"])) {
      $fechaBusIni = new Carbon('first day of this month');
      $fechaBusFin = new Carbon('last day of this month');

      if ($datosFiltros["tipoBusquedaFecha"] == TiposBusquedaFecha::Dia && isset($datosFiltros["fechaDia"])) {
        $fechaBusIni = Carbon::createFromFormat("d/m/Y H:i:s", $datosFiltros["fechaDia"] . " 00:00:00");
        $fechaBusFin = Carbon::createFromFormat("d/m/Y H:i:s", $datosFiltros["fechaDia"] . " 23:59:59");
      } else if ($datosFiltros["tipoBusquedaFecha"] == TiposBusquedaFecha::Mes && isset($datosFiltros["fechaMes"])) {
        $fechaBusIni = Carbon::createFromFormat("d/m/Y H:i:s", "01/" . $datosFiltros["fechaMes"] . " 00:00:00");
        $fechaBusFin = Carbon::createFromFormat("d/m/Y H:i:s", "01/" . $datosFiltros["fechaMes"] . " 23:59:59");
        $fechaBusFin->addMonth();
        $fechaBusFin->day = 0;
      } else if ($datosFiltros["tipoBusquedaFecha"] == TiposBusquedaFecha::Anio && isset($datosFiltros["fechaAnio"])) {
        $fechaBusIni = Carbon::createFromFormat("d/m/Y H:i:s", "01/01/" . $datosFiltros["fechaAnio"] . " 00:00:00");
        $fechaBusFin = Carbon::createFromFormat("d/m/Y H:i:s", "01/12/" . $datosFiltros["fechaAnio"] . " 23:59:59");
        $fechaBusFin->addMonth();
        $fechaBusFin->day = 0;
      } else if ($datosFiltros["tipoBusquedaFecha"] == TiposBusquedaFecha::RangoDias && isset($datosFiltros["fechaDiaInicio"]) && isset($datosFiltros["fechaDiaFin"])) {
        $fechaBusIni = Carbon::createFromFormat("d/m/Y H:i:s", $datosFiltros["fechaDiaInicio"] . " 00:00:00");
        $fechaBusFin = Carbon::createFromFormat("d/m/Y H:i:s", $datosFiltros["fechaDiaFin"] . " 23:59:59");
      } else if ($datosFiltros["tipoBusquedaFecha"] == TiposBusquedaFecha::RangoMeses && isset($datosFiltros["fechaMesInicio"])) {
        $fechaBusIni = Carbon::createFromFormat("d/m/Y H:i:s", "01/" . $datosFiltros["fechaMesInicio"] . " 00:00:00");
        $fechaBusFin = Carbon::createFromFormat("d/m/Y H:i:s", "01/" . (isset($datosFiltros["fechaMesFin"]) ? $datosFiltros["fechaMesFin"] : $datosFiltros["fechaMesInicio"]) . " 23:59:59");
        $fechaBusFin->addMonth();
        $fechaBusFin->day = 0;
      } else if ($datosFiltros["tipoBusquedaFecha"] == TiposBusquedaFecha::RangoAnios && isset($datosFiltros["fechaAnioInicio"])) {
        $fechaBusIni = Carbon::createFromFormat("d/m/Y H:i:s", "01/01/" . $datosFiltros["fechaAnioInicio"] . " 00:00:00");
        $fechaBusFin = Carbon::createFromFormat("d/m/Y H:i:s", "01/12/" . (isset($datosFiltros["fechaAnioFin"]) ? $datosFiltros["fechaAnioFin"] : $datosFiltros["fechaAnioInicio"]) . " 23:59:59");
        $fechaBusFin->addMonth();
        $fechaBusFin->day = 0;
      }
      $elementos->whereBetween($nombreTabla . "." . $nombreCampoFecha, [$fechaBusIni, $fechaBusFin]);
    }
  }

  public static function preProcesarDocumentosDocente(&$datos) {
    $datos["nombresArchivosDocumentoPersonalCv"] = ReglasValidacion::formatoDato($datos, "nombresArchivosDocumentoPersonalCv");
    $datos["nombresOriginalesArchivosDocumentoPersonalCv"] = ReglasValidacion::formatoDato($datos, "nombresOriginalesArchivosDocumentoPersonalCv");
    $datos["nombresArchivosDocumentoPersonalCvEliminados"] = ReglasValidacion::formatoDato($datos, "nombresArchivosDocumentoPersonalCvEliminados");

    $datos["nombresArchivosDocumentoPersonalCertificadoInternacional"] = ReglasValidacion::formatoDato($datos, "nombresArchivosDocumentoPersonalCertificadoInternacional");
    $datos["nombresOriginalesArchivosDocumentoPersonalCertificadoInternacional"] = ReglasValidacion::formatoDato($datos, "nombresOriginalesArchivosDocumentoPersonalCertificadoInternacional");
    $datos["nombresArchivosDocumentoPersonalCertificadoInternacionalEliminados"] = ReglasValidacion::formatoDato($datos, "nombresArchivosDocumentoPersonalCertificadoInternacionalEliminados");

    $datos["nombresArchivosDocumentoPersonalImagenDocumentoIdentidad"] = ReglasValidacion::formatoDato($datos, "nombresArchivosDocumentoPersonalImagenDocumentoIdentidad");
    $datos["nombresOriginalesArchivosDocumentoPersonalImagenDocumentoIdentidad"] = ReglasValidacion::formatoDato($datos, "nombresOriginalesArchivosDocumentoPersonalImagenDocumentoIdentidad");
    $datos["nombresArchivosDocumentoPersonalImagenDocumentoIdentidadEliminados"] = ReglasValidacion::formatoDato($datos, "nombresArchivosDocumentoPersonalImagenDocumentoIdentidadEliminados");
  }

  public static function formatoHora($tiempoSegundos, $incluirSegundos = FALSE) {
    $h = floor($tiempoSegundos / 3600);
    $m = floor($tiempoSegundos % 3600 / 60);
    $s = floor($tiempoSegundos % 3600 % 60);
    return (($h >= 0 ? $h . ":" . ($m < 10 ? "0" : "") : "") . $m . ($incluirSegundos ? ":" . ($s < 10 ? "0" : "") . $s : ""));
  }

  public static function incluirEnlaceWhatsApp($numero) {
    $numeroFinal = ($numero != null ? str_replace([" ", "+"], "", trim($numero)) : "");

    if ($numeroFinal != "" && strlen($numeroFinal) >= 9 && preg_match('/^[0-9]+$/', $numeroFinal)) {
      $numeroFinal = (strlen($numeroFinal) != 9 ? $numeroFinal : "51" . $numeroFinal);
      return '<a href="https://wa.me/' . $numeroFinal . '" target="_blank">' . $numero . '</a>';
    }
    return $numero;
  }

}
