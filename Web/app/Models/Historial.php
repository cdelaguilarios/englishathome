<?php

namespace App\Models;

use Carbon\Carbon;
use App\Helpers\Enum\TiposEntidad;
use App\Helpers\Enum\TiposHistorial;
use Illuminate\Database\Eloquent\Model;

class Historial extends Model {

  public $timestamps = false;
  protected $table = "historial";
  protected $fillable = ["idPago", "idClase", "titulo", "mensaje", "rutasImagenes", "enviarCorreo", "mostrarEnPerfil", "fechaNotificacion", "tipo"];

  const numeroMensajesXCarga = 10;

  public static function nombreTabla() {
    $modeloHistorial = new Historial();
    $nombreTabla = $modeloHistorial->getTable();
    unset($modeloHistorial);
    return $nombreTabla;
  }

  public static function obtener($numeroCarga, $idEntidad) {
    if ($idEntidad == NULL) {
      return [];
    }

    $nombreTabla = Historial::nombreTabla();
    $historial = Historial::select($nombreTabla . ".*")
                    ->leftJoin(EntidadHistorial::nombreTabla() . " as entidadHistorial", $nombreTabla . ".id", "=", "entidadHistorial.idHistorial")
                    ->leftJoin(Pago::nombreTabla() . " as pago", $nombreTabla . ".idPago", "=", "pago.id")
                    ->leftJoin(Clase::nombreTabla() . " as clase", $nombreTabla . ".idClase", "=", "clase.id")
                    ->where($nombreTabla . ".eliminado", 0)
                    ->where($nombreTabla . ".mostrarEnPerfil", 1)
                    ->where($nombreTabla . ".fechaNotificacion", "<=", Carbon::now())
                    ->where("entidadHistorial.idEntidad", $idEntidad)
                    ->where(function ($q) use ($nombreTabla) {
                      $q->whereNull($nombreTabla . ".idPago")->orWhere("pago.eliminado", 0);
                    })->where(function ($q) use ($nombreTabla) {
      $q->whereNull($nombreTabla . ".idClase")->orWhere("clase.eliminado", 0);
    });

    $historialTotal = $historial->count();
    $historialSel = $historial->orderBy($nombreTabla . ".fechaNotificacion", "DESC")
                    ->skip(((int) $numeroCarga) * Historial::numeroMensajesXCarga)
                    ->take(Historial::numeroMensajesXCarga)->get();

    return ["datos" => Historial::FormatearDatosHistorial($historialSel), "mostrarBotonCargar" => (((((int) $numeroCarga) + 1) * Historial::numeroMensajesXCarga) < $historialTotal)];
  }

  private static function FormatearDatosHistorial($historial) {
    $datosHistorial = [];
    $tiposNotificacion = TiposHistorial::listar();

    foreach ($historial as $dh) {
      $fNotificacion = date("Y-m-d 00:00:00", strtotime($dh->fechaNotificacion));
      $entidades = Entidad::select(Entidad::nombreTabla() . ".*")
                      ->leftJoin(EntidadHistorial::nombreTabla() . " as entidadHistorial", Entidad::nombreTabla() . ".id", "=", "entidadHistorial.idEntidad")
                      ->where("entidadHistorial.idHistorial", $dh->id)->get();

      $tiposEntidad = TiposEntidad::listar();
      foreach ($entidades as $entidad) {
        if (!array_key_exists($entidad->tipo, $tiposEntidad)) {
          continue;
        }
        $dh->titulo = str_replace("[" . $entidad->tipo . "]", "<a href='" . route($tiposEntidad[$entidad->tipo][1], ['id' => $entidad->id]) . "' target='_blank'>" . $entidad->nombre . " " . $entidad->apellido . "</a>", $dh->titulo);
        $dh->mensaje = str_replace("[" . $entidad->tipo . "]", "<a href='" . route($tiposEntidad[$entidad->tipo][1], ['id' => $entidad->id]) . "' target='_blank'>" . $entidad->nombre . " " . $entidad->apellido . "</a>", $dh->mensaje);
      }
      $dh->fechaNotificacion = Carbon::createFromFormat("Y-m-d H:i:s", $dh->fechaNotificacion)->format("H:i:s");
      $dh->icono = (array_key_exists($dh->tipo, $tiposNotificacion) ? $tiposNotificacion[$dh->tipo][1] : TiposHistorial::IconoDefecto);
      $dh->claseColorIcono = (array_key_exists($dh->tipo, $tiposNotificacion) ? $tiposNotificacion[$dh->tipo][2] : TiposHistorial::ClaseColorIconoDefecto);

      $datosHistorial[$fNotificacion] = ((array_key_exists($fNotificacion, $datosHistorial)) ? $datosHistorial[$fNotificacion] : []);
      array_push($datosHistorial[$fNotificacion], $dh);
    }
    return $datosHistorial;
  }

  public static function registrar($datos) {
    $idEntidadesSel = (is_array($datos["idEntidades"]) ? $datos["idEntidades"] : [$datos["idEntidades"]]);
    if (count($idEntidadesSel) > 0) {
      $datos["fechaNotificacion"] = (isset($datos["fechaNotificacion"]) && !(isset($datos["notificarInmediatamente"]) && $datos["notificarInmediatamente"] == 1) ? $datos["fechaNotificacion"] : Carbon::now()->toDateTimeString());
      $datos["tipo"] = (isset($datos["tipo"]) ? $datos["tipo"] : TiposHistorial::Notificacion);
      
      $historial = new Historial($datos);
      $historial->save();

      foreach ($idEntidadesSel as $idEntidad) {
        if (!is_null($idEntidad)) {
          $entidadHitorial = new EntidadHistorial([ "idEntidad" => $idEntidad, "idHistorial" => $historial["id"]]);
          $entidadHitorial->save();
        }
      }
    }
  }

}
