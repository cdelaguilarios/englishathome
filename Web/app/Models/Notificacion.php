<?php

namespace App\Models;

use DB;
use Auth;
use Carbon\Carbon;
use App\Helpers\Util;
use App\Helpers\Enum\TiposEntidad;
use App\Helpers\Enum\EstadosUsuario;
use App\Helpers\Enum\TiposNotificacion;
use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model {

  public $timestamps = false;
  protected $table = "notificacion";
  protected $fillable = [
      "tipo",
      "numeroClase",
      "idClase",
      "idPago",
      "idCorreo",
      "enviarCorreo",
      "enviarCorreoEntidades",
      "mostrarEnPerfil"
  ];

  const numeroNotificacionesXCarga = 10;

  public static function nombreTabla()/* - */ {
    $modeloNotificacion = new Notificacion();
    $nombreTabla = $modeloNotificacion->getTable();
    unset($modeloNotificacion);
    return $nombreTabla;
  }

  public static function listarBase()/* - */ {
    $nombreClase = Clase::nombreTabla();
    $nombreTablaPago = Pago::nombreTabla();
    $nombreTablaEntidad = Entidad::nombreTabla();
    $nombreTablaNotificacion = Notificacion::nombreTabla();
    $nombreTablaTareaNotificacion = TareaNotificacion::nombreTabla();
    $nombreTablaEntidadNotificacion = EntidadNotificacion::nombreTabla();

    $notificaciones = Notificacion::leftJoin($nombreTablaTareaNotificacion . " AS tareaNotificacion", $nombreTablaNotificacion . ".id", "=", "tareaNotificacion.id")
            //Datos de las entidades involucradas
            ->leftJoin($nombreTablaEntidadNotificacion . " as entidadNotificacion", $nombreTablaNotificacion . ".id", "=", "entidadNotificacion.idNotificacion")
            ->leftJoin($nombreTablaEntidad . " AS entidadInvolucradaNotificacion", function ($q) use ($nombreTablaEntidadNotificacion, $nombreTablaNotificacion) {
              $q->on("entidadInvolucradaNotificacion.id", "IN", DB::raw("(SELECT idEntidad 
                                                                            FROM " . $nombreTablaEntidadNotificacion . "
                                                                            WHERE idNotificacion = " . $nombreTablaNotificacion . ".id AND esObservador = 0)"));
            })
            //Datos del usuario creado
            ->leftJoin($nombreTablaEntidad . " as entidadUsuarioCreador", function ($q) {
              $q->on("tareaNotificacion.idUsuarioCreador", "=", "entidadUsuarioCreador.id")
              ->on("entidadUsuarioCreador.eliminado", "=", DB::raw("0"));
            })
            //Pagos y clases asociadas
            ->leftJoin($nombreClase . " as clase", $nombreTablaNotificacion . ".idClase", "=", "clase.id")
            ->leftJoin($nombreTablaPago . " as pago", $nombreTablaNotificacion . ".idPago", "=", "pago.id")
            ->where(function ($q) use ($nombreTablaNotificacion) {
              $q->whereNull($nombreTablaNotificacion . ".idClase")->orWhere("clase.eliminado", 0);
            })
            ->where(function ($q) use ($nombreTablaNotificacion) {
              $q->whereNull($nombreTablaNotificacion . ".idPago")->orWhere("pago.eliminado", 0);
            })
            ->where("tareaNotificacion.eliminado", 0)
            ->groupBy("tareaNotificacion.id")
            ->distinct();

    if (!Auth::guest()) {
      $notificaciones->leftJoin($nombreTablaEntidadNotificacion . " AS entidadNotificacionUsuarioActual", function ($q) {
        $q->on("entidadNotificacionUsuarioActual.idNotificacion", "=", "entidadNotificacion.idNotificacion")
                ->on("entidadNotificacionUsuarioActual.idEntidad", "=", DB::raw(Auth::user()->idEntidad));
      });
    }

    return $notificaciones->select(DB::raw(
                            $nombreTablaNotificacion . ".*,
                            " . (Auth::guest() ? "NULL AS 'fechaRevision'," : "entidadNotificacionUsuarioActual.fechaRevision,") .
                            "tareaNotificacion.idUsuarioCreador, 
                            entidadUsuarioCreador.nombre AS nombreUsuarioCreador, 
                            entidadUsuarioCreador.apellido AS apellidoUsuarioCreador, 
                            tareaNotificacion.titulo, 
                            tareaNotificacion.mensaje, 
                            tareaNotificacion.adjuntos, 
                            tareaNotificacion.fechaProgramada, 
                            tareaNotificacion.fechaNotificacion, 
                            tareaNotificacion.fechaRegistro, 
                            GROUP_CONCAT(
                              DISTINCT CONCAT(entidadInvolucradaNotificacion.tipo, '-', entidadInvolucradaNotificacion.id, ':', entidadInvolucradaNotificacion.nombre, ' ', entidadInvolucradaNotificacion.apellido) 
                              SEPARATOR ';'
                            ) AS entidadesInvolucradas")
    );
  }

  public static function listar($datos)/* - */ {
    $nombreTablaTareaNotificacion = TareaNotificacion::nombreTabla();

    $notificaciones = Notificacion::listarBase()->where("entidadNotificacion.idEntidad", Auth::user()->idEntidad);
    Util::aplicarFiltrosBusquedaXFechas($notificaciones, $nombreTablaTareaNotificacion, "fechaNotificacion", $datos);

    return $notificaciones;
  }

  public static function listarNuevas()/* - */ {
    $fechaActual = Carbon::now();
    $fechaBusIni = Carbon::createFromFormat("d/m/Y H:i:s", $fechaActual->format('d/m/Y') . " 00:00:00");
    $fechaBusFin = Carbon::createFromFormat("d/m/Y H:i:s", $fechaActual->format('d/m/Y') . " 23:59:59");

    return Notificacion::listarBase()
                    ->where("entidadNotificacion.idEntidad", Auth::user()->idEntidad)
                    ->whereBetween("tareaNotificacion.fechaNotificacion", [$fechaBusIni, $fechaBusFin])
                    ->whereNull("entidadNotificacionUsuarioActual.fechaRevision")->get();
  }

  public static function listarHistorial($idEntidad, $numeroCarga)/* - */ {
    $nombreTablaNotificacion = Notificacion::nombreTabla();
    $preNotificaciones = Notificacion::listarBase()->where($nombreTablaNotificacion . ".mostrarEnPerfil", 1);
    $preNotificaciones->where("entidadNotificacion.esObservador", 0);

    $datosEntidadRel = RelacionEntidad::obtenerXIdEntidadA($idEntidad);
    if (count($datosEntidadRel) > 0) {
      $preNotificaciones->where(function($q) use ($idEntidad, $datosEntidadRel) {
        $q->where("entidadNotificacion.idEntidad", $idEntidad)->orWhere("entidadNotificacion.idEntidad", $datosEntidadRel[0]->idEntidadB);
      });
    } else {
      $preNotificaciones->where("entidadNotificacion.idEntidad", $idEntidad);
    }

    $totalNotificaciones = $preNotificaciones->paginate(1)->total();
    $notificaciones = $preNotificaciones->orderBy("tareaNotificacion.fechaNotificacion", "DESC")
                    ->skip(((int) $numeroCarga) * Notificacion::numeroNotificacionesXCarga)
                    ->take(Notificacion::numeroNotificacionesXCarga)->get();

    return [
        "datos" => Notificacion::formatearDatosHistorial($notificaciones),
        "mostrarBotonCargar" => (((((int) $numeroCarga) + 1) * Notificacion::numeroNotificacionesXCarga) < $totalNotificaciones)
    ];
  }

  public static function obtenerXId($id, $simple = FALSE)/* - */ {
    $notificacion = Notificacion::listarBase()->where("tareaNotificacion.id", $id)->firstOrFail();
    if (!$simple) {
      Notificacion::formatearDatos($notificacion);
    }
    return $notificacion;
  }

  public static function obtenerXIdPago($idPago)/* - */ {
    return Notificacion::listarBase()->where("idPago", $idPago)->first();
  }

  private static function formatearDatosHistorial($notificaciones)/* - */ {
    $notificacionesFormateadas = [];
    foreach ($notificaciones as $notificacion) {
      $fechaNotificacion = date("Y-m-d 00:00:00", strtotime($notificacion->fechaNotificacion));
      Notificacion::formatearDatos($notificacion);

      $repetida = false;
      if (isset($notificacionesFormateadas[$fechaNotificacion])) {
        foreach ($notificacionesFormateadas[$fechaNotificacion] as $notificacionFormateada) {
          if (strip_tags($notificacionFormateada->titulo) == strip_tags($notificacionFormateada->titulo) && strip_tags($notificacionFormateada->mensaje) == strip_tags($notificacion->mensaje) && $notificacionFormateada->fechaRegistro == $notificacion->fechaRegistro) {
            $repetida = true;
            break;
          }
        }
      }

      if (!$repetida) {
        $notificacionesFormateadas[$fechaNotificacion] = ((array_key_exists($fechaNotificacion, $notificacionesFormateadas)) ? $notificacionesFormateadas[$fechaNotificacion] : []);
        array_push($notificacionesFormateadas[$fechaNotificacion], $notificacion);
      }
    }
    return $notificacionesFormateadas;
  }

  public static function formatearDatos(&$notificacion, $incluirEnlaces = TRUE)/* - */ {
    $notificacion->tituloOriginal = $notificacion->titulo;
    $notificacion->mensajeOriginal = $notificacion->mensaje;

    $nombreTablaEntidad = Entidad::nombreTabla();
    $nombreTablaEntidadNotificacion = EntidadNotificacion::nombreTabla();

    $entidadesInvolucradas = Entidad::select($nombreTablaEntidad . ".*")
                    ->leftJoin($nombreTablaEntidadNotificacion . " as entidadNotificacion", $nombreTablaEntidad . ".id", "=", "entidadNotificacion.idEntidad")
                    ->where("entidadNotificacion.idNotificacion", $notificacion->id)
                    ->where("entidadNotificacion.esObservador", 0)->get();

    $tiposNotificacion = TiposNotificacion::listar();
    $tiposEntidad = TiposEntidad::listarTiposBase();

    foreach ($entidadesInvolucradas as $entidad) {
      if (!array_key_exists($entidad->tipo, $tiposEntidad)) {
        continue;
      }
      
      $datosEntidad = $entidad->nombre . " " . $entidad->apellido;
      if($incluirEnlaces){
        $datosEntidad = "<a href='" . route($tiposEntidad[$entidad->tipo][4], ['id' => $entidad->id]) . "' target='_blank'>" . $entidad->nombre . " " . $entidad->apellido . "</a>";
      }      
      $notificacion->titulo = str_replace("[" . $entidad->tipo . "]", $datosEntidad, $notificacion->titulo);
      $notificacion->mensaje = str_replace("[" . $entidad->tipo . "]", $datosEntidad, $notificacion->mensaje);
    }

    $notificacion->horaNotificacion = Carbon::createFromFormat("Y-m-d H:i:s", $notificacion->fechaNotificacion)->format("H:i:s");
    $notificacion->icono = (array_key_exists($notificacion->tipo, $tiposNotificacion) ? $tiposNotificacion[$notificacion->tipo][1] : TiposNotificacion::IconoDefecto);
    $notificacion->claseColorIcono = (array_key_exists($notificacion->tipo, $tiposNotificacion) ? $tiposNotificacion[$notificacion->tipo][2] : TiposNotificacion::ClaseColorIconoDefecto);
    $notificacion->claseTextoColorIcono = (array_key_exists($notificacion->tipo, $tiposNotificacion) ? $tiposNotificacion[$notificacion->tipo][3] : TiposNotificacion::ClaseTextoColorIconoDefecto);
  }

  public static function registrarActualizar($datos, $creadoPorElSistema = TRUE) {
    $idEntidadesSel = (is_array($datos["idEntidades"]) ? $datos["idEntidades"] : [$datos["idEntidades"]]);
    if (count($idEntidadesSel) > 0) {
      $datos["tipo"] = (isset($datos["tipo"]) ? $datos["tipo"] : TiposNotificacion::Notificacion);
      $notificarInmediatamente = isset($datos["notificarInmediatamente"]) && $datos["notificarInmediatamente"] == 1;
      if (!isset($datos["fechaProgramada"]) || $notificarInmediatamente) {
        $datos["fechaProgramada"] = Carbon::now()->toDateTimeString();
      }
      $datos["fechaNotificacion"] = $datos["fechaProgramada"];
      //TODO: Revisar esta variable
      $datos["enviarCorreoEntidades"] = (isset($datos["enviarCorreoEntidad"]) ? $datos["enviarCorreoEntidad"] : 0);

      if (!(isset($datos["idNotificacion"]) && $datos["idNotificacion"] != "")) {
        //Registro
        $datos["adjuntos"] = Archivo::procesarArchivosSubidosNUEVO("", $datos, 5, "Adjuntos");

        $idTareaNotificacion = TareaNotificacion::registrar($datos, $creadoPorElSistema);
        $notificacion = new Notificacion($datos);
        $notificacion->id = $idTareaNotificacion;
        $notificacion->save();

        $enviarCorreo = (isset($notificacion["enviarCorreo"]) && ((int) $notificacion["enviarCorreo"] == 1));
        $enviarCorreoEntidades = (isset($notificacion["enviarCorreoEntidades"]) && ((int) $notificacion["enviarCorreoEntidades"] == 1));
        Notificacion::registrarActualizarEntidades($idTareaNotificacion, $idEntidadesSel, $notificarInmediatamente, $creadoPorElSistema, ($enviarCorreo || $enviarCorreoEntidades));
      } else {
        //Actualización
        $idTareaNotificacion = $datos["idNotificacion"];
        $notificacion = Notificacion::obtenerXId($idTareaNotificacion, TRUE);
        $datos["adjuntos"] = Archivo::procesarArchivosSubidosNUEVO($notificacion->adjuntos, $datos, 5, "Adjuntos");

        //Pasado la fecha de notificación no se pueden cambiar los datos de programación
        $fechaActual = Carbon::now();
        $fechaNotificacion = Carbon::createFromFormat("Y-m-d H:i:s", $notificacion->fechaNotificacion);
        if ($fechaActual >= $fechaNotificacion) {
          unset($datos["enviarCorreo"]);
          unset($datos["enviarCorreoEntidades"]);
          unset($datos["mostrarEnPerfil"]);
          unset($datos["notificarInmediatamente"]);
          unset($datos["fechaProgramada"]);
          unset($datos["fechaNotificacion"]);
        }

        TareaNotificacion::actualizar($idTareaNotificacion, $datos, $creadoPorElSistema);
        $notificacion->update($datos);
      }
    }
  }

  private static function registrarActualizarEntidades($idNotificacion, $idEntidades, $notificarInmediatamente, $creadoPorElSistema, $incluirObservadores)/* - */ {
    EntidadNotificacion::where("idNotificacion", $idNotificacion)->delete();
    foreach ($idEntidades as $idEntidad) {
      if (isset($idEntidad)) {
        $entidadNotificacion = new EntidadNotificacion([ "idEntidad" => $idEntidad, "idNotificacion" => $idNotificacion, "esObservador" => 0]);
        $entidadNotificacion->save();
      }
    }

    $entidadesObservadoras = [];
    if ($incluirObservadores) {
      //Se incluye como observadores a todos los usuarios del sistema
      $entidadesObservadoras = Entidad::listar(TiposEntidad::Usuario, EstadosUsuario::Activo, $idEntidades)->get();
    } else if (!($creadoPorElSistema || Auth::guest())) {
      //Se incluye como observador al usuario creador
      $entidadActual = Entidad::ObtenerXId(Auth::user()->idEntidad);
      $entidadesObservadoras = [$entidadActual];
    }

    if (count($entidadesObservadoras) > 0) {
      foreach ($entidadesObservadoras as $entidadObservadora) {
        $entidadHitorial = new EntidadNotificacion([ "idEntidad" => $entidadObservadora->id, "idNotificacion" => $idNotificacion, "esObservador" => 1]);
        if (!Auth::guest() && $notificarInmediatamente && $entidadObservadora->id == Auth::user()->idEntidad) {
          $entidadHitorial->fechaRevision = Carbon::now()->toDateTimeString();
        }
        $entidadHitorial->save();
      }
    }
  }

  public static function eliminar($id) {
    TareaNotificacion::eliminarGrupo([$id]);
  }

  public static function eliminarXIdClase($idClase)/* - */ {
    $idsNotificaciones = Notificacion::listarBase()->where("idClase", $idClase)->lists("id")->toArray();
    TareaNotificacion::eliminarGrupo($idsNotificaciones);
  }

  public static function eliminarXIdPago($idPago)/* - */ {
    $idsNotificaciones = Notificacion::listarBase()->where("idPago", $idPago)->lists("id")->toArray();
    TareaNotificacion::eliminarGrupo($idsNotificaciones);
  }

}
