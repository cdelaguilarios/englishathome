<?php

namespace App\Models;

use Mail;
use Config;
use Carbon\Carbon;
use App\Helpers\Enum\TiposEntidad;
use App\Helpers\Enum\TiposHistorial;
use Illuminate\Database\Eloquent\Model;

class Historial extends Model {

  public $timestamps = false;
  protected $table = "historial";
  protected $fillable = ["idPago", "idClase", "titulo", "mensaje", "imagenes", "enviarCorreo", "mostrarEnPerfil", "fechaNotificacion", "tipo"];

  const numeroMensajesXCarga = 10;

  public static function nombreTabla() {
    $modeloHistorial = new Historial();
    $nombreTabla = $modeloHistorial->getTable();
    unset($modeloHistorial);
    return $nombreTabla;
  }

  private static function listarBase() {
    $nombreTabla = Historial::nombreTabla();
    return Historial::select($nombreTabla . ".*")
                    ->leftJoin(EntidadHistorial::nombreTabla() . " as entidadHistorial", $nombreTabla . ".id", "=", "entidadHistorial.idHistorial")
                    ->leftJoin(Pago::nombreTabla() . " as pago", $nombreTabla . ".idPago", "=", "pago.id")
                    ->leftJoin(Clase::nombreTabla() . " as clase", $nombreTabla . ".idClase", "=", "clase.id")
                    ->where($nombreTabla . ".eliminado", 0)
                    ->where($nombreTabla . ".fechaNotificacion", "<=", Carbon::now())
                    ->where(function ($q) use ($nombreTabla) {
                      $q->whereNull($nombreTabla . ".idPago")->orWhere("pago.eliminado", 0);
                    })->where(function ($q) use ($nombreTabla) {
                      $q->whereNull($nombreTabla . ".idClase")->orWhere("clase.eliminado", 0);
                    })
                    ->groupBy($nombreTabla . ".id")
                    ->distinct();
  }

  public static function obtenerXId($id) {
    return Historial::where("id", $id)->where("eliminado", 0)->firstOrFail();
  }

  public static function obtenerPerfil($numeroCarga, $idEntidad) {
    $nombreTabla = Historial::nombreTabla();
    $historiales = Historial::listarBase()
            ->where($nombreTabla . ".mostrarEnPerfil", 1)
            ->where("entidadHistorial.idEntidad", $idEntidad);
    $historialesTotal = $historiales->count();
    $historialesSel = $historiales->orderBy($nombreTabla . ".fechaNotificacion", "DESC")
                    ->skip(((int) $numeroCarga) * Historial::numeroMensajesXCarga)
                    ->take(Historial::numeroMensajesXCarga)->get();

    return ["datos" => Historial::formatearDatosHistorialPerfil($historialesSel), "mostrarBotonCargar" => (((((int) $numeroCarga) + 1) * Historial::numeroMensajesXCarga) < $historialesTotal)];
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

  public static function eliminarXIdClase($idClase) {
    $historiales = Historial::where("eliminado", 0)->where("idClase", $idClase)->get();
    foreach ($historiales as $historial) {
      $historial->eliminado = 1;
      $historial->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
      $historial->save();
    }
  }

  public static function enviarCorreosAdministracion() {
    $nombreTabla = Historial::nombreTabla();
    $historiales = Historial::listarBase()
                    ->where($nombreTabla . ".enviarCorreo", 1)
                    ->where($nombreTabla . ".correoEnviado", 0)
                    ->orderBy($nombreTabla . ".fechaNotificacion", "ASC")
                    ->skip(0)->take((int) Config::get("eah.numeroNotificacionesXLlamada"))->get();

    foreach ($historiales as $historial) {
      Historial::formatearDatosHistorialBase($historial);
      $nombreCompletoDestinatario = "usuario administrador";
      $mensaje = '<p>' . $historial->titulo . '</p><p>' . $historial->mensaje . '</p><p><b>Fecha notificación:</b> ' . \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $historial->fechaNotificacion)->format("d/m/Y H:i:s") . '</p>';
      $correo = Config::get("eah.correoNotificaciones");

      Mail::send("notificacion.plantillaCorreo", ["nombreCompletoDestinatario" => $nombreCompletoDestinatario, "mensaje" => $mensaje], function ($m) use ($correo) {
        $m->to($correo, "Administrador - English at home")->bcc("cdelaguilarios@gmail.com")->subject("English at home - Notificación");
      });
      $historialEnv = Historial::obtenerXId($historial->id);
      $historialEnv->correoEnviado = 1;
      $historialEnv->save();
    }
  }

  private static function formatearDatosHistorialPerfil($historiales) {
    $historialesFormateados = [];

    foreach ($historiales as $historial) {
      $fechaNotificacion = date("Y-m-d 00:00:00", strtotime($historial->fechaNotificacion));
      Historial::formatearDatosHistorialBase($historial);
      $historialesFormateados[$fechaNotificacion] = ((array_key_exists($fechaNotificacion, $historialesFormateados)) ? $historialesFormateados[$fechaNotificacion] : []);
      array_push($historialesFormateados[$fechaNotificacion], $historial);
    }
    return $historialesFormateados;
  }

  private static function formatearDatosHistorialBase(&$historial) {
    $tiposNotificacion = TiposHistorial::listar();
    $tiposEntidad = TiposEntidad::listar();
    $nombreTabla = Entidad::nombreTabla();
    $entidades = Entidad::select($nombreTabla . ".*")
                    ->leftJoin(EntidadHistorial::nombreTabla() . " as entidadHistorial", $nombreTabla . ".id", "=", "entidadHistorial.idEntidad")
                    ->where("entidadHistorial.idHistorial", $historial->id)->get();
    foreach ($entidades as $entidad) {
      if (!array_key_exists($entidad->tipo, $tiposEntidad)) {
        continue;
      }
      $historial->titulo = str_replace("[" . $entidad->tipo . "]", "<a href='" . route($tiposEntidad[$entidad->tipo][1], ['id' => $entidad->id]) . "' target='_blank'>" . $entidad->nombre . " " . $entidad->apellido . "</a>", $historial->titulo);
      $historial->mensaje = str_replace("[" . $entidad->tipo . "]", "<a href='" . route($tiposEntidad[$entidad->tipo][1], ['id' => $entidad->id]) . "' target='_blank'>" . $entidad->nombre . " " . $entidad->apellido . "</a>", $historial->mensaje);
    }
    $historial->horaNotificacion = Carbon::createFromFormat("Y-m-d H:i:s", $historial->fechaNotificacion)->format("H:i:s");
    $historial->icono = (array_key_exists($historial->tipo, $tiposNotificacion) ? $tiposNotificacion[$historial->tipo][1] : TiposHistorial::IconoDefecto);
    $historial->claseColorIcono = (array_key_exists($historial->tipo, $tiposNotificacion) ? $tiposNotificacion[$historial->tipo][2] : TiposHistorial::ClaseColorIconoDefecto);
  }

}
