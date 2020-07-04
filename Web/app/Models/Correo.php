<?php

namespace App\Models;

use Log;
use Mail;
use Config;
use Storage;
use Carbon\Carbon;
use App\Helpers\Enum\TiposEntidad;
use Illuminate\Database\Eloquent\Model;

class Correo extends Model {

  public $timestamps = false;
  protected $table = "correo";
  protected $fillable = [
      "asunto",
      "correosAdicionales"
  ];

  public static function nombreTabla()/* - */ {
    $modeloCorreo = new Correo();
    $nombreTabla = $modeloCorreo->getTable();
    unset($modeloCorreo);
    return $nombreTabla;
  }

  public static function listarBase()/* - */ {
    $nombreTablaCorreo = Correo::nombreTabla();
    $nombreTablaTareaNotificacion = TareaNotificacion::nombreTabla();

    return Correo::leftJoin($nombreTablaTareaNotificacion . " AS tareaNotificacion", $nombreTablaCorreo . ".id", "=", "tareaNotificacion.id")
                    ->where("tareaNotificacion.eliminado", 0)
                    ->groupBy("tareaNotificacion.id")
                    ->distinct();
  }

  public static function registrar($datos) {
    //TODO: Si se registran muchos destinatarios va a ser mejor separarlos en varios registro-correos (grupos de 10 destinatarios)
    
    //Correos adicionales
    $correosAdicionales = "";
    $correosAdicionalesExcluidos = "";
    if (isset($datos["correosAdicionales"])) {
      $correosAdicionalesArr = explode(",", $datos["correosAdicionales"]);
      foreach ($correosAdicionalesArr as $correoAdicional) {
        if (trim($correoAdicional) != "" && filter_var(trim($correoAdicional), FILTER_VALIDATE_EMAIL)) {
          $correosAdicionales .= trim($correoAdicional) . ",";
        } else if (trim($correoAdicional) != "") {
          $correosAdicionalesExcluidos .= trim($correoAdicional) . ",";
        }
      }
    }
    $datos["correosAdicionales"] = $correosAdicionales;

    $datos["fechaProgramada"] = Carbon::now()->toDateTimeString();
    $datos["adjuntos"] = Archivo::procesarArchivosSubidosNUEVO("", $datos, 5, "Adjuntos");

    $idTareaNotificacion = TareaNotificacion::registrar($datos);
    $correo = new Correo($datos);
    $correo->id = $idTareaNotificacion;
    $correo->save();

    $idsEntidadesSeleccionadas = (isset($datos["idsEntidadesSeleccionadas"]) ? $datos["idsEntidadesSeleccionadas"] : []);
    $idsEntidadesExcluidas = (isset($datos["idsEntidadesExcluidas"]) ? $datos["idsEntidadesExcluidas"] : []);
    if (isset($datos["tipoEntidad"])) {
      if ($datos["tipoEntidad"] == TiposEntidad::Interesado && isset($datos["cursoInteres"])) {
        //Si el tipo de entidad es "INTERESADO" se incluye el filtro por "curso de interes"
        $cursoInteres = ($datos["cursoInteres"] != "Otros" ? $datos["cursoInteres"] : "");
        $preEntidades = Interesado::listar()
                        ->whereNotIn("entidad.id", $idsEntidadesExcluidas)
                        ->where(Interesado::nombreTabla() . ".cursoInteres", $cursoInteres)->get();
        if (isset($datos["estado" . $datos["tipoEntidad"]]) && $datos["estado" . $datos["tipoEntidad"]] != "") {
          $preEntidades->where("estado", $datos["estado" . $datos["tipoEntidad"]]);
        }
        $entidades = $preEntidades->get();
      } else {
        $entidades = Entidad::listar($datos["tipoEntidad"], $datos["estado" . $datos["tipoEntidad"]], $idsEntidadesExcluidas)->get();
      }

      foreach ($entidades as $entidad) {
        $entidadCorreo = new EntidadCorreo([ "idEntidad" => $entidad->id, "idCorreo" => $idTareaNotificacion]);
        $entidadCorreo->save();
      }
    } else if (count($idsEntidadesSeleccionadas) > 0) {
      foreach (array_diff($idsEntidadesSeleccionadas, $idsEntidadesExcluidas) as $idEntidadSeleccionada) {
        $entidadCorreo = new EntidadCorreo([ "idEntidad" => $idEntidadSeleccionada, "idCorreo" => $idTareaNotificacion]);
        $entidadCorreo->save();
      }
    }
    return $correosAdicionalesExcluidos;
  }

  private static function preProcesarNotificaciones() {
    $nombreTablaNotificacion = Notificacion::nombreTabla();
    $notificacionesEnviarCorreo = Notificacion::listarBase()
                    ->where($nombreTablaNotificacion . ".enviarCorreo", 1)
                    ->whereNull($nombreTablaNotificacion . ".idCorreo")
                    ->whereDate('tareaNotificacion.fechaNotificacion', '<=', Carbon::now())->get();


    foreach ($notificacionesEnviarCorreo as $notificacionEnviarCorreo) {
      Notificacion::formatearDatos($notificacionEnviarCorreo, FALSE);

      $datos = [
          "titulo" => $notificacionEnviarCorreo->titulo,
          "mensaje" => $notificacionEnviarCorreo->mensaje,
          "adjuntos" => $notificacionEnviarCorreo->adjuntos,
          "fechaProgramada" => Carbon::now()->toDateTimeString()
      ];

      $idTareaNotificacion = TareaNotificacion::registrar($datos);
      $correo = new Correo($datos);
      $correo->id = $idTareaNotificacion;
      $correo->save();

      Notificacion::whereIn("id", [$notificacionEnviarCorreo->id])->update(["idCorreo" => $idTareaNotificacion]);

      if ($notificacionEnviarCorreo->enviarCorreoEntidades == 1) {
        $entidadesNotificacion = EntidadNotificacion::where("idNotificacion", $notificacionEnviarCorreo->id)->get();
        foreach ($entidadesNotificacion as $entidadNotificacion) {
          $entidadCorreo = new EntidadCorreo([ "idEntidad" => $entidadNotificacion->idEntidad, "idCorreo" => $idTareaNotificacion]);
          $entidadCorreo->save();
        }
      }
    }
  }

  public static function enviar() {
    ini_set('max_execution_time', 900);
    
    Correo::preProcesarNotificaciones();

    $nombreTablaCorreo = Correo::nombreTabla();
    $preCorreos = Correo::listarBase()
            ->where($nombreTablaCorreo . ".enviado", 0)
            ->where($nombreTablaCorreo . ".envioEnProceso", 0)
            ->orderBy("tareaNotificacion.fechaProgramada", "ASC")
            ->skip(0)
            ->take((int) Config::get("eah.numeroCorreosXEnvio"));
    $correos = $preCorreos->get();
    Correo::whereIn("id", $preCorreos->lists("tareaNotificacion.id")->toArray())->update(["envioEnProceso" => 1]);

    Config::set("mail.username", VariableSistema::obtenerXLlave("correo"));
    Config::set("mail.password", VariableSistema::obtenerXLlave("contrasenaCorreo"));
    $correoNotificaciones = VariableSistema::obtenerXLlave("correo");


    foreach ($correos as $correo) {
      $correoEnv = Correo::where("id", $correo->id)->firstOrFail();
      try {
        $entidadesDestinatarias = [];

        $entidadesCorreo = EntidadCorreo::where("idCorreo", $correo->id)->get();
        foreach ($entidadesCorreo as $entidadCorreo) {
          if (Entidad::verificarExistencia($entidadCorreo->idEntidad)) {
            $datosEntidadCorreo = Entidad::ObtenerXId($entidadCorreo->idEntidad);
            $entidadesDestinatarias[] = (object) [
                        "nombreCompleto" => $datosEntidadCorreo->nombre . " " . $datosEntidadCorreo->apellido,
                        "correoElectronico" => $datosEntidadCorreo->correoElectronico
            ];
          }
        }

        if (isset($correo->correosAdicionales)) {
          $correosAdicionalesArr = explode(",", $correo->correosAdicionales);
          foreach ($correosAdicionalesArr as $correoAdicional) {
            if (trim($correoAdicional) != "" && filter_var(trim($correoAdicional), FILTER_VALIDATE_EMAIL)) {
              $entidadesDestinatarias[] = (object) [
                          "nombreCompleto" => "",
                          "correoElectronico" => $correoAdicional
              ];
            }
          }
        }

        if (count($entidadesDestinatarias) == 0) {
          $entidadesDestinatarias[] = (object) [
                      "nombreCompleto" => "Usuario administrador",
                      "correoElectronico" => $correoNotificaciones
          ];
        }

        $adjuntos = ($correo->adjuntos);
        $asunto = (isset($correo->asunto) && trim($correo->asunto) != "" ? $correo->asunto : "English at home - Notificación");
        $mensajeFinal = (isset($correo->titulo) && trim($correo->titulo) != "" ? '<p>' . $correo->titulo . '</p>' : '');
        $mensajeFinal .= (isset($correo->mensaje) && trim($correo->mensaje) != "" ? '<p>' . $correo->mensaje . '</p>' : '');

        $enviosRealizados = 0;
        foreach ($entidadesDestinatarias as $entidadDestinataria) {
          try {
            Mail::send("correos.plantilla", ["nombreCompletoDestinatario" => $entidadDestinataria->nombreCompleto, "mensaje" => $mensajeFinal], function ($m) use ($entidadDestinataria, $asunto, $adjuntos, $correoNotificaciones) {
              $m->to($entidadDestinataria->correoElectronico, $entidadDestinataria->nombreCompleto)->subject($asunto);
              if ($entidadDestinataria->correoElectronico !== $correoNotificaciones) {
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
            $enviosRealizados++;
          } catch (\Exception $e) {
            if ($enviosRealizados == 0) {
              throw $e;
            } else {
              Log::error($e);
            }
          }
        }

        $fechaActual = Carbon::now()->toDateTimeString();
        $tareaNotificacion = TareaNotificacion::ObtenerXId($correo->id);
        $tareaNotificacion->fechaNotificacion = $fechaActual;
        $tareaNotificacion->fechaUltimaActualizacion = $fechaActual;
        $tareaNotificacion->save();

        $correoEnv->enviado = 1;
      } catch (\Exception $e) {
        Log::error($e);
      }
      $correoEnv->envioEnProceso = 0;
      $correoEnv->save();
    }
  }

}
