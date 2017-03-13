<?php

namespace App\Helpers;

use Carbon\Carbon;
use App\Models\Ubigeo;

class ReglasValidacion {

  const RegexDecimal = "/^[\d]{1,14}(\.[\d]{1,4})?$/";
  const RegexDecimalNegativo = "/^-?[\d]{1,14}(\.[\d]{1,4})?$/";
  const RegexAlfanumerico = "/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s]+$/";
  const RegexAlfabetico = "/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/";
  const RegexGeoLatitud = "/^[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?)$/";
  const RegexGeoLongitud = "/^\s*[-+]?(180(\.0+)?|((1[0-7]\d)|([1-9]?\d))(\.\d+)?)$/";
  const RegexTiempo = "/^(?:[01][0-9]|2[0-3]):[0-5][0-9]$/";
  const RegexFecha = '/(^(((0[1-9]|1[0-9]|2[0-8])[\/](0[1-9]|1[012]))|((29|30|31)[\/](0[13578]|1[02]))|((29|30)[\/](0[4,6,9]|11)))[\/](19|[2-9][0-9])\d\d$)|(^29[\/]02[\/](19|[2-9][0-9])(00|04|08|12|16|20|24|28|32|36|40|44|48|52|56|60|64|68|72|76|80|84|88|92|96)$)/'; //dd/MM/yyyy

  public static function validarUbigeo($codigoDepartamento, $codigoProvincia, $codigoDistrito, $codigoUbigeo) {
    $departamentos = Ubigeo::listarDepartamentos();
    if (!array_key_exists($codigoDepartamento, $departamentos)) {
      return false;
    }
    $provincias = Ubigeo::listarProvinciasXCodigoDepartamento($codigoDepartamento);
    if (!array_key_exists($codigoProvincia, $provincias)) {
      return false;
    }
    $distritos = Ubigeo::listarDistritosXCodigoProvincia($codigoProvincia);
    if (!array_key_exists($codigoDistrito, $distritos)) {
      return false;
    }
    return ($codigoDistrito == $codigoUbigeo);
  }

  public static function validarHorario($horario) {
    if (!(!is_null($horario) && $horario != "")) {
      return FALSE;
    }

    $datosHorario = json_decode($horario);
    foreach ($datosHorario as $horario) {
      if (!(isset($horario->dias) && isset($horario->horas))) {
        return FALSE;
      }

      $dias = explode(",", $horario->dias);
      foreach ($dias as $dia) {
        if (!(is_numeric($dia) && (int) $dia >= 1 && (int) $dia <= 7)) {
          return FALSE;
        }
      }

      $horas = $horario->horas;
      foreach ($horas as $rangoHora) {
        $rangoHora = explode("-", $rangoHora);
        if (!(count($rangoHora) == 2)) {
          return FALSE;
        }
        $horaIni = $rangoHora[0];
        $horaFin = $rangoHora[1];

        if (!(preg_match(ReglasValidacion::RegexTiempo, $horaIni) && preg_match(ReglasValidacion::RegexTiempo, $horaFin))) {
          return FALSE;
        }

        $auxFechaIni = Carbon::createFromFormat("d/m/Y H:i:s", "01/01/2000 " . $horaIni . ":00");
        $auxFechaFin = Carbon::createFromFormat("d/m/Y H:i:s", "01/01/2000 " . $horaFin . ":00");
        if (!($auxFechaIni < $auxFechaFin)) {
          return FALSE;
        }
      }
      return TRUE;
    }
  }

  public static function validarDatosNotificacionClasesPago($datosNotificacionClases) {
    if (!(!is_null($datosNotificacionClases) && $datosNotificacionClases != "")) {
      return FALSE;
    }

    $datosNotificacionClasesSel = json_decode($datosNotificacionClases);
    foreach ($datosNotificacionClasesSel as $datosNotificacionClase) {
      if (!(isset($datosNotificacionClase->notificar) && ($datosNotificacionClase->notificar == "" || is_bool($datosNotificacionClase->notificar)))) {
        return FALSE;
      }
    }
    return TRUE;
  }

  public static function formatoDato($datos, $nombreDato, $retornoOpc = NULL) {
    if (isset($datos[$nombreDato])) {
      if ((!is_array($datos[$nombreDato]) && $datos[$nombreDato] != "") || (is_array($datos[$nombreDato]))) {
        return $datos[$nombreDato];
      }
    }
    return $retornoOpc;
  }

}
