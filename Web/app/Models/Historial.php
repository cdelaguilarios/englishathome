<?php

namespace App\Models;

use Log;
use Mail;
use Config;
use Storage;
use Carbon\Carbon;
use App\Helpers\Enum\TiposEntidad;
use App\Helpers\Enum\TiposHistorial;
use Illuminate\Database\Eloquent\Model;

class Historial extends Model {

  public $timestamps = false;
  protected $table = "historial";
  protected $fillable = ["idPago", "idClase", "titulo", "asunto", "mensaje", "imagenes", "adjuntos", "idEntidadDestinataria", "correoDestinatario", "enviarCorreo", "mostrarEnPerfil", "fechaNotificacion", "tipo"];

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

  public static function obtenerPerfil($numeroCarga, $idEntidad, $entidadObservadora = FALSE, $nuevasNotificaciones = FALSE, $seccionWidget = FALSE, $idNotificacion = NULL) {
    $nombreTabla = Historial::nombreTabla();
    $historiales = Historial::listarBase()->where($nombreTabla . ".mostrarEnPerfil", 1);

    $datosEntidadRel = RelacionEntidad::obtenerXIdEntidadA($idEntidad);
    if (count($datosEntidadRel) > 0 && !$entidadObservadora) {
      $historiales->where(function($q) use ($idEntidad, $datosEntidadRel) {
        $q->where("entidadHistorial.idEntidad", $idEntidad)->orWhere("entidadHistorial.idEntidad", $datosEntidadRel[0]->idEntidadB);
      });
    } else {
      $historiales->where("entidadHistorial.idEntidad", $idEntidad);
    }

    if ($entidadObservadora) {
      $historiales->where("entidadHistorial.esObservador", 1);
    }
    if ($nuevasNotificaciones) {
      $historiales->where("entidadHistorial.revisado", 0);
    }
    if (!is_null($idNotificacion)) {
      $historiales->where($nombreTabla . ".id", $idNotificacion);
    }

    $historialesTotal = $historiales->count();
    if ($nuevasNotificaciones) {
      $historialesSel = $historiales->orderBy($nombreTabla . ".fechaNotificacion", "DESC")->get();
    } else {
      $historialesSel = $historiales->orderBy($nombreTabla . ".fechaNotificacion", "DESC")
                      ->skip(((int) $numeroCarga) * Historial::numeroMensajesXCarga)
                      ->take(Historial::numeroMensajesXCarga)->get();
    }

    return ["datos" => Historial::formatearDatosHistorialPerfil($historialesSel, $seccionWidget), "mostrarBotonCargar" => (((((int) $numeroCarga) + 1) * Historial::numeroMensajesXCarga) < $historialesTotal)];
  }

  public static function revisarNotificaciones($idEntidadObservadora, $idsNuevasNotificaciones = []) {
    $idsNuevasNotificacionesSel = (is_array($idsNuevasNotificaciones) ? $idsNuevasNotificaciones : [$idsNuevasNotificaciones]);
    $registros = EntidadHistorial::where("idEntidad", $idEntidadObservadora)->where("esObservador", 1);
    if (count($idsNuevasNotificacionesSel) > 0) {
      $registros->whereIn("idHistorial", $idsNuevasNotificacionesSel);
    }
    $registros->update(["revisado" => 1]);
  }

  public static function registrar($datos) {
    $idEntidadesSel = (is_array($datos["idEntidades"]) ? $datos["idEntidades"] : [$datos["idEntidades"]]);
    if (count($idEntidadesSel) > 0) {
      $datos["fechaNotificacion"] = (isset($datos["fechaNotificacion"]) && !(isset($datos["notificarInmediatamente"]) && $datos["notificarInmediatamente"] == 1) ? $datos["fechaNotificacion"] : Carbon::now()->toDateTimeString());
      $datos["tipo"] = (isset($datos["tipo"]) ? $datos["tipo"] : TiposHistorial::Notificacion);
      $datos["adjuntos"] = Archivo::procesarArchivosSubidos("", $datos, 20, "nombresArchivosAdjuntos", "nombresOriginalesArchivosAdjuntos");

      $historial = new Historial($datos);
      $historial->fechaRegistro = Carbon::now()->toDateTimeString();
      $historial->save();
      Historial::registrarActualizarEntidadHistorial($historial["id"], $idEntidadesSel, (isset($historial["enviarCorreo"]) && ((int) $historial["enviarCorreo"] == 1)));
    }
  }

  public static function actualizar($id, $datos) {
    $idEntidadesSel = (is_array($datos["idEntidades"]) ? $datos["idEntidades"] : [$datos["idEntidades"]]);
    if (count($idEntidadesSel) > 0) {
      if (isset($datos["fechaNotificacion"])) {
        $datos["fechaNotificacion"] = (!(isset($datos["notificarInmediatamente"]) && $datos["notificarInmediatamente"] == 1) ? $datos["fechaNotificacion"] : Carbon::now()->toDateTimeString());
      }
      $historial = Historial::obtenerXId($id);
      $datos["adjuntos"] = Archivo::procesarArchivosSubidos($historial->adjuntos, $datos, 20, "nombresArchivosAdjuntos", "nombresOriginalesArchivosAdjuntos", "nombresArchivosAdjuntosEliminados");
      $historial->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
      $historial->update($datos);
      Historial::registrarActualizarEntidadHistorial($historial["id"], $idEntidadesSel, (isset($historial["enviarCorreo"]) && ((int) $historial["enviarCorreo"] == 1)));
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

  public static function registrarCorreos($datos) {
    $datos["enviarCorreo"] = 1;
    $datos["mostrarEnPerfil"] = 0;
    $datos["fechaNotificacion"] = Carbon::now()->toDateTimeString();
    $datos["tipo"] = TiposHistorial::Notificacion;
    $datos["adjuntos"] = Archivo::procesarArchivosSubidos("", $datos, 20, "nombresArchivosAdjuntos", "nombresOriginalesArchivosAdjuntos");
    unset($datos["nombresArchivosAdjuntos"]);
    unset($datos["nombresOriginalesArchivosAdjuntos"]);

    $idsEntidadesExcluidas = (isset($datos["idsEntidadesExcluidas"]) ? $datos["idsEntidadesExcluidas"] : []);
    if (!is_null($datos["tipoEntidad"])) {
      if ($datos["tipoEntidad"] == TiposEntidad::Interesado && !is_null($datos["cursoInteres"])) {
        $cursoInteres = ($datos["cursoInteres"] != "Otros" ? $datos["cursoInteres"] : "");
        $preEntidades = Interesado::listar()->whereNotIn("entidad.id", $idsEntidadesExcluidas)
                        ->where(Interesado::nombreTabla() . ".cursoInteres", $cursoInteres)->get();
        if (isset($datos["estado" . $datos["tipoEntidad"]]) && $datos["estado" . $datos["tipoEntidad"]] != "") {
          $preEntidades->where("estado", $datos["estado" . $datos["tipoEntidad"]]);
        }
        $entidades = $preEntidades->get();
      } else {
        $entidades = Entidad::listar($datos["tipoEntidad"], $datos["estado" . $datos["tipoEntidad"]], $idsEntidadesExcluidas);
      }
      foreach ($entidades as $entidad) {
        $historial = new Historial($datos + ["idEntidadDestinataria" => $entidad->id]);
        $historial->fechaRegistro = Carbon::now()->toDateTimeString();
        $historial->save();
      }
    } else if (!is_null($datos["idsEntidadesSeleccionadas"])) {
      foreach (array_diff($datos["idsEntidadesSeleccionadas"], $idsEntidadesExcluidas) as $idEntidadSeleccionada) {
        $historial = new Historial($datos + ["idEntidadDestinataria" => $idEntidadSeleccionada]);
        $historial->fechaRegistro = Carbon::now()->toDateTimeString();
        $historial->save();
      }
    }
    $correosAdicionalesExcluidos = "";
    if (!is_null($datos["correosAdicionales"])) {
      $correosAdicionales = explode(",", $datos["correosAdicionales"]);
      foreach ($correosAdicionales as $correoAdicional) {
        if (trim($correoAdicional) != "" && filter_var(trim($correoAdicional), FILTER_VALIDATE_EMAIL)) {
          $historial = new Historial($datos + ["correoDestinatario" => trim($correoAdicional)]);
          $historial->fechaRegistro = Carbon::now()->toDateTimeString();
          $historial->save();
        } else if (trim($correoAdicional) != "") {
          $correosAdicionalesExcluidos .= trim($correoAdicional) . ",";
        }
      }
    }
    return $correosAdicionalesExcluidos;
  }

  public static function enviarCorreos() {
    $nombreTabla = Historial::nombreTabla();
    $preHistoriales = Historial::listarBase()
                    ->where($nombreTabla . ".enviarCorreo", 1)
                    ->where($nombreTabla . ".correoEnviado", 0)
                    ->where($nombreTabla . ".envioCorreoProceso", 0)
                    ->orderBy($nombreTabla . ".fechaNotificacion", "ASC")
                    ->skip(0)->take((int) Config::get("eah.numeroNotificacionesXLlamada"));
    $historiales = $preHistoriales->get();
    Historial::whereIn("id", $preHistoriales->lists($nombreTabla . ".id")->toArray())->update(["envioCorreoProceso" => 1]);

    Config::set("mail.username", VariableSistema::obtenerXLlave("correo"));
    Config::set("mail.password", VariableSistema::obtenerXLlave("contrasenaCorreo"));
    $correoNotificaciones = VariableSistema::obtenerXLlave("correo");

    foreach ($historiales as $historial) {
      $historialEnv = Historial::obtenerXId($historial->id);
      try {
        Historial::formatearDatosHistorialBase($historial);
        $entidadDestinataria = (isset($historial->idEntidadDestinataria) && Entidad::verificarExistencia($historial->idEntidadDestinataria) ? Entidad::ObtenerXId($historial->idEntidadDestinataria) : NULL);
        $correoDestinatario = (!is_null($entidadDestinataria) ? $entidadDestinataria->correoElectronico : (isset($historial->correoDestinatario) ? $historial->correoDestinatario : $correoNotificaciones));
        $nombreCompletoDestinatario = (!is_null($entidadDestinataria) ? $entidadDestinataria->nombre . " " . $entidadDestinataria->apellido : (isset($historial->correoDestinatario) ? "" : "Usuario administrador"));
        $adjuntos = ($historial->adjuntos);

        $asunto = (isset($historial->asunto) ? $historial->asunto : "English at home - Notificación");
        $mensaje = (isset($historial->titulo) && trim($historial->titulo) != "" ? '<p>' . $historial->titulo . '</p>' : '') . '<p>' . $historial->mensaje . '</p>' . (is_null($entidadDestinataria) ? '<p><b>Fecha notificación:</b> ' . \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $historial->fechaNotificacion)->format("d/m/Y H:i:s") . '</p>' : '');

        Mail::send("notificacion.plantillaCorreo", ["nombreCompletoDestinatario" => $nombreCompletoDestinatario, "mensaje" => $mensaje], function ($m) use ($correoDestinatario, $nombreCompletoDestinatario, $asunto, $adjuntos, $correoNotificaciones) {
          $m->to($correoDestinatario, $nombreCompletoDestinatario)->subject($asunto);
          if ($correoDestinatario !== $correoNotificaciones) {
            $m->bcc($correoNotificaciones);
          }
          if (isset($adjuntos)) {
            $rutaBaseAlmacenamiento = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
            $archivosAdjuntos = explode(",", $adjuntos);
            foreach ($archivosAdjuntos as $archivoAdjunto) {
              $datosArchivoAdjunto = explode(":", $archivoAdjunto);
              if (count($datosArchivoAdjunto) == 2) {
                $m->attach($rutaBaseAlmacenamiento . "/" . $datosArchivoAdjunto[0], ['as' => $datosArchivoAdjunto[1]]);
              }
            }
          }
        });
        $historialEnv->correoEnviado = 1;
      } catch (\Exception $e) {
        Log::error($e);
      }
      $historialEnv->envioCorreoProceso = 0;
      $historialEnv->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
      $historialEnv->save();
    }
  }

  private static function formatearDatosHistorialPerfil($historiales, $seccionWidget = FALSE) {
    $historialesFormateados = [];
    foreach ($historiales as $historial) {
      $fechaNotificacion = date("Y-m-d 00:00:00", strtotime($historial->fechaNotificacion));
      Historial::formatearDatosHistorialBase($historial, $seccionWidget);

      $eventoRepetido = false;
      if (isset($historialesFormateados[$fechaNotificacion])) {
        foreach ($historialesFormateados[$fechaNotificacion] as $historialFor) {
          if (strip_tags($historialFor->titulo) == strip_tags($historial->titulo) && strip_tags($historialFor->mensaje) == strip_tags($historial->mensaje)) {
            $eventoRepetido = true;
            break;
          }
        }
      }
      if (!$eventoRepetido) {
        $historialesFormateados[$fechaNotificacion] = ((array_key_exists($fechaNotificacion, $historialesFormateados)) ? $historialesFormateados[$fechaNotificacion] : []);
        array_push($historialesFormateados[$fechaNotificacion], $historial);
      }
    }
    return $historialesFormateados;
  }

  private static function formatearDatosHistorialBase(&$historial, $seccionWidget = FALSE) {
    $tiposNotificacion = TiposHistorial::listar();
    $tiposEntidad = TiposEntidad::listar();
    $nombreTablaEntidad = Entidad::nombreTabla();
    $entidades = Entidad::select($nombreTablaEntidad . ".*")
                    ->leftJoin(EntidadHistorial::nombreTabla() . " as entidadHistorial", $nombreTablaEntidad . ".id", "=", "entidadHistorial.idEntidad")
                    ->where("entidadHistorial.idHistorial", $historial->id)
                    ->where("entidadHistorial.esObservador", 0)->get();
    foreach ($entidades as $entidad) {
      if (!array_key_exists($entidad->tipo, $tiposEntidad)) {
        continue;
      }
      $historial->titulo = str_replace("[" . $entidad->tipo . "]", ($seccionWidget ? "" : "<a href='" . route($tiposEntidad[$entidad->tipo][2], ['id' => $entidad->id]) . "' target='_blank'>") . $entidad->nombre . " " . $entidad->apellido . ($seccionWidget ? "" : "</a>"), $historial->titulo);
      $historial->mensaje = str_replace("[" . $entidad->tipo . "]", ($seccionWidget ? "" : "<a href='" . route($tiposEntidad[$entidad->tipo][2], ['id' => $entidad->id]) . "' target='_blank'>") . $entidad->nombre . " " . $entidad->apellido . ($seccionWidget ? "" : "</a>"), $historial->mensaje);
    }
    $historial->horaNotificacion = Carbon::createFromFormat("Y-m-d H:i:s", $historial->fechaNotificacion)->format("H:i:s");
    $historial->icono = (array_key_exists($historial->tipo, $tiposNotificacion) ? $tiposNotificacion[$historial->tipo][1] : TiposHistorial::IconoDefecto);
    $historial->claseColorIcono = (array_key_exists($historial->tipo, $tiposNotificacion) ? $tiposNotificacion[$historial->tipo][2] : TiposHistorial::ClaseColorIconoDefecto);
    $historial->claseTextoColorIcono = (array_key_exists($historial->tipo, $tiposNotificacion) ? $tiposNotificacion[$historial->tipo][3] : TiposHistorial::ClaseTextoColorIconoDefecto);
  }

  private static function registrarActualizarEntidadHistorial($idHistorial, $idEntidades, $incluirObservadores = FALSE) {
    EntidadHistorial::where("idHistorial", $idHistorial)->delete();
    foreach ($idEntidades as $idEntidad) {
      if (!is_null($idEntidad)) {
        $entidadHitorial = new EntidadHistorial([ "idEntidad" => $idEntidad, "idHistorial" => $idHistorial]);
        $entidadHitorial->save();
      }
    }

    if ($incluirObservadores) {
      $entidadesUsuarios = Entidad::listar(TiposEntidad::Usuario);
      foreach ($entidadesUsuarios as $entidadUsuario) {
        $entidadHitorial = new EntidadHistorial([ "idEntidad" => $entidadUsuario->id, "idHistorial" => $idHistorial, "esObservador" => 1]);
        $entidadHitorial->save();
      }
    }
  }

}
