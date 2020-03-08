<?php

namespace App\Models;

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

  public static function listarBase()/* - */ {
    $nombreTablaCorreo = Correo::nombreTabla();
    $nombreTablaTareaNotificacion = TareaNotificacion::nombreTabla();

    return Correo::leftJoin($nombreTablaTareaNotificacion . " AS tareaNotificacion", $nombreTablaCorreo . ".id", "=", "tareaNotificacion.id")
                    ->where("tareaNotificacion.eliminado", 0)
                    ->groupBy("tareaNotificacion.id")
                    ->distinct();
    ;
  }

  public static function registrar($datos) {
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
                    ->select(DB::raw("tareaNotificacion.id, 
                                      tareaNotificacion.titulo, 
                                      tareaNotificacion.mensaje, 
                                      tareaNotificacion.adjuntos, 
                                      " . $nombreTablaNotificacion . ".enviarCorreoEntidades"))
                    ->where($nombreTablaNotificacion . ".enviarCorreo", 1)
                    ->whereNull($nombreTablaNotificacion . ".idCorreo")
                    ->whereDate('tareaNotificacion.fechaNotificacion', '<=', Carbon::now())->get();

    foreach ($notificacionesEnviarCorreo as $notificacionEnviarCorreo) {
      $datos = [
          "titulo" => $notificacionEnviarCorreo->titulo,
          "asunto" => $notificacionEnviarCorreo->titulo,
          "mensaje" => $notificacionEnviarCorreo->mensaje,
          "adjuntos" => $notificacionEnviarCorreo->adjuntos,
          "fechaProgramada" => Carbon::now()->toDateTimeString()
      ];

      $idTareaNotificacion = TareaNotificacion::registrar($datos);
      $correo = new Correo($datos);
      $correo->id = $idTareaNotificacion;
      $correo->save();

      Notificacion::whereIn("id", $notificacionEnviarCorreo->id)->update(["idCorreo" => $correo->id]);

      if ($notificacionEnviarCorreo->enviarCorreoEntidades == 1) {
        $entidadesNotificacion = EntidadNotificacion::where("idNotificacion", $notificacionEnviarCorreo->id)->get();
        foreach ($entidadesNotificacion as $entidadNotificacion) {
          $entidadCorreo = new EntidadCorreo([ "idEntidad" => $entidadNotificacion->idEntidad, "idCorreo" => $correo->id]);
          $entidadCorreo->save();
        }
      }
    }
  }

  public static function enviar() {
    Correo::preProcesarNotificaciones();

    $nombreTablaCorreo = Correo::nombreTabla();
    $preCorreos = Correo::listarBase()
                    ->where($nombreTablaCorreo . "enviado", 0)
                    ->where($nombreTablaCorreo . ".envioEnProceso", 0)
                    ->orderBy("tareaNotificacion.fechaProgramada", "ASC")
                    ->skip(0)->take((int) Config::get("eah.numeroCorreosXEnvio"));
    $correos = $preCorreos->get();
    Correos::whereIn("id", $preCorreos->lists("tareaNotificacion.id")->toArray())->update(["envioEnProceso" => 1]);

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

}
