<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Horario extends Model {

  public $timestamps = false;
  protected $table = "horario";
  protected $fillable = ["idEntidad", "numeroDiaSemana", "horaInicio", "horaFin"];

  public static function nombreTabla() {
    $modeloHorario = new Horario();
    $nombreTabla = $modeloHorario->getTable();
    unset($modeloHorario);
    return $nombreTabla;
  }

  public static function listarIdsEntidadesXRangoFecha($numeroDiaSemana, $horaInicio, $horaFin, $tipoEntidad = NULL) {
    $nombreTabla = Horario::nombreTabla();
    $idsEntidades = Horario::leftJoin(Entidad::nombreTabla() . " as entidad", $nombreTabla . ".idEntidad", "=", "entidad.id")
            ->where($nombreTabla . ".numeroDiaSemana", $numeroDiaSemana)
            ->where($nombreTabla . ".horaInicio", "<=", $horaInicio)
            ->where($nombreTabla . ".horaFin", ">=", $horaFin);
    if (!is_null($tipoEntidad)) {
      $idsEntidades->where("entidad.tipo", $tipoEntidad);
    }
    return $idsEntidades->lists($nombreTabla . ".idEntidad");
  }

  public static function listarIdsEntidadesXHorario($datosJsonHorario, $tipoEntidad = NULL) {
    $idsEntidades = [];
    $auxCont = 1;
    $datosHorario = json_decode($datosJsonHorario);
    foreach ($datosHorario as $horario) {
      $dias = explode(",", $horario->dias);
      $horas = $horario->horas;
      foreach ($dias as $dia) {
        foreach ($horas as $rangoHora) {
          $rangoHora = explode("-", $rangoHora);
          $idsEntidadesHorario = Horario::listarIdsEntidadesXRangoFecha($dia, $rangoHora[0] . ":00", $rangoHora[1] . ":00", $tipoEntidad);

          $idsEntidades = ($auxCont == 1 ? $idsEntidadesHorario->toArray() : array_intersect($idsEntidades, $idsEntidadesHorario->toArray()));
          $auxCont++;
        }
      }
    }
    return $idsEntidades;
  }

  public static function obtener($idEntidad) {
    return Horario::where("idEntidad", $idEntidad)->orderBy("numeroDiaSemana", "asc")->get();
  }

  public static function obtenerFormatoJson($idEntidad) {
    $horarioSel = [];
    $horario = Horario::obtener($idEntidad);
    foreach ($horario as $datHorario) {
      $i = count($horarioSel);
      $rangoHoras = date("H:i", strtotime($datHorario->horaInicio)) . "-" . date("H:i", strtotime($datHorario->horaFin));
      $rangoEnc = FALSE;
      foreach ($horarioSel as $k => $v) {
        if (in_array($rangoHoras, $v["horas"])) {
          $i = $k;
          $rangoEnc = TRUE;
          break;
        }
      }
      if (!$rangoEnc) {
        $horarioSel[$i] = ["dias" => "", "horas" => []];
        array_push($horarioSel[$i]["horas"], $rangoHoras);
      }
      $horarioSel[$i]["dias"] .= ($horarioSel[$i]["dias"] != "" ? "," : "") . $datHorario->numeroDiaSemana;
    }
    return json_encode($horarioSel);
  }

  public static function registrarActualizar($idEntidad, $datosJsonHorario) {
    Horario::where("idEntidad", $idEntidad)->delete();
    $datosHorario = json_decode($datosJsonHorario);
    foreach ($datosHorario as $horario) {
      $dias = explode(",", $horario->dias);
      $horas = $horario->horas;
      foreach ($dias as $dia) {
        foreach ($horas as $rangoHora) {
          $rangoHora = explode("-", $rangoHora);
          $horario = new Horario([
              "idEntidad" => $idEntidad,
              "numeroDiaSemana" => $dia,
              "horaInicio" => $rangoHora[0] . ":00",
              "horaFin" => $rangoHora[1] . ":00"
          ]);
          $horario->fechaRegistro = Carbon::now()->toDateTimeString();
          $horario->save();
        }
      }
    }
  }

  public static function copiarHorario($idEntidadOri, $idEntidadDes) {
    Horario::where("idEntidad", $idEntidadDes)->delete();
    $horario = Horario::obtener($idEntidadOri);
    foreach ($horario as $datHorario) {
      $horario = new Horario([
          "idEntidad" => $idEntidadDes,
          "numeroDiaSemana" => $datHorario->numeroDiaSemana,
          "horaInicio" => $datHorario->horaInicio,
          "horaFin" => $datHorario->horaFin
      ]);
      $horario->fechaRegistro = Carbon::now()->toDateTimeString();
      $horario->save();
    }
  }

}
