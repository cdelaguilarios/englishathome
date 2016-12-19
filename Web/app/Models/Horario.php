<?php

namespace App\Models;

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

  protected static function obtener($idEntidad) {
    return Horario::where("idEntidad", $idEntidad)->orderBy("numeroDiaSemana", "asc")->get();
  }

  protected static function obtenerFormatoJson($idEntidad) {
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

  protected static function registrarActualizar($idEntidad, $datosJsonHorario) {
    $datosHorario = json_decode($datosJsonHorario);

    Horario::where("idEntidad", $idEntidad)->delete();
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
          $horario->save();
        }
      }
    }
  }

  protected static function listarIdsEntidadesXRangoFecha($numeroDiaSemana, $horaInicio, $horaFin, $tipoEntidad) {
    $nombreTabla = Horario::nombreTabla();
    return Horario::leftJoin(Entidad::nombreTabla() . " as entidad", $nombreTabla . ".idEntidad", "=", "entidad.id")
                    ->where("entidad.tipo", $tipoEntidad)
                    ->where($nombreTabla . ".numeroDiaSemana", $numeroDiaSemana)
                    ->where($nombreTabla . ".horaInicio", "<=", $horaInicio)
                    ->where($nombreTabla . ".horaFin", ">=", $horaFin)
                    ->lists($nombreTabla . ".idEntidad");
  }

}
