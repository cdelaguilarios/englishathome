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

  const numeroMensajesXCarga = 9;

  public static function NombreTabla() {
    $modeloHistorial = new Historial();
    $nombreTabla = $modeloHistorial->getTable();
    unset($modeloHistorial);
    return $nombreTabla;
  }

  protected static function obtener($numeroCarga, $idEntidad) {
    if ($idEntidad == NULL) {
      return [];
    }

    $nombreTabla = Historial::NombreTabla();
    $historial = Historial::select($nombreTabla . ".*")
            ->leftJoin(EntidadHistorial::NombreTabla() . " as entidadHistorial", $nombreTabla . ".id", "=", "entidadHistorial.idHistorial")
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
            })
            ->where("entidadHistorial.idEntidad", $idEntidad);

    $historialTotal = $historial->count();
    $historialSel = $historial->orderBy($nombreTabla . ".fechaNotificacion", "DESC")
                    ->skip(((int) $numeroCarga) * Historial::numeroMensajesXCarga)
                    ->take(Historial::numeroMensajesXCarga)->get();

    return ["datos" => Historial::FormatearDatosHistorial($historialSel), "mostrarBotonCargar" => (((((int) $numeroCarga) + 1) * Historial::numeroMensajesXCarga) < $historialTotal)];
  }

  protected static function Registrar($idsEntidades, $titulo, $mensaje, $rutasImagenes = NULL, $enviarCorreo = FALSE, $mostrarEnPerfil = TRUE, $idPago = NULL, $idClase = NULL, $fechaNotificacion = NULL, $tipo = TiposHistorial::Notificacion) {
    $idEntidadesSel = (is_array($idsEntidades) ? $idsEntidades : [$idsEntidades]);
    if (count($idEntidadesSel) > 0) {
      $historial = new Historial([
          "idPago" => $idPago,
          "idClase" => $idClase,
          "titulo" => $titulo,
          "mensaje" => $mensaje,
          "rutasImagenes" => $rutasImagenes,
          "enviarCorreo" => ($enviarCorreo ? 1 : 0),
          "mostrarEnPerfil" => ($mostrarEnPerfil ? 1 : 0),
          "fechaNotificacion" => (!is_null($fechaNotificacion) ? $fechaNotificacion : Carbon::now()->toDateTimeString()),
          "tipo" => $tipo
      ]);
      $historial->save();

      foreach ($idEntidadesSel as $idEntidad) {
        if (!is_null($idEntidad)) {
          $entidadHitorial = new EntidadHistorial([ "idEntidad" => $idEntidad, "idHistorial" => $historial["id"]]);
          $entidadHitorial->save();
        }
      }
    }
  }

  private static function FormatearDatosHistorial($historial) {
    $datosHistorial = [];
    $tiposNotificacion = TiposHistorial::Listar();

    foreach ($historial as $dh) {
      $fNotificacion = date("Y-m-d 00:00:00", strtotime($dh->fechaNotificacion));
      $entidades = Entidad::select(Entidad::nombreTabla() . ".*")->leftJoin(EntidadHistorial::NombreTabla() . " as entidadHistorial", Entidad::nombreTabla() . ".id", "=", "entidadHistorial.idEntidad")->where("entidadHistorial.idHistorial", $dh->id)->get();

      $tiposEntidad = TiposEntidad::Listar();
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

}
