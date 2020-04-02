<?php

namespace App\Models;

use DB;
use Auth;
use Carbon\Carbon;
use App\Helpers\Util;
use App\Helpers\Enum\EstadosTarea;
use Illuminate\Database\Eloquent\Model;

class Tarea extends Model {

  public $timestamps = false;
  protected $table = "tarea";
  protected $fillable = [
      "fechaRevision",
      "fechaFinalizacion"
  ];

  public static function nombreTabla()/* - */ {
    $modeloTarea = new Tarea();
    $nombreTabla = $modeloTarea->getTable();
    unset($modeloTarea);
    return $nombreTabla;
  }

  private static function listarBase()/* - */ {
    $nombreTablaTarea = Tarea::nombreTabla();
    $nombreTablaEntidad = Entidad::nombreTabla();
    $nombreTablaTareaNotificacion = TareaNotificacion::nombreTabla();

    $tareas = Tarea::leftJoin($nombreTablaTareaNotificacion . " AS tareaNotificacion", $nombreTablaTarea . ".id", "=", "tareaNotificacion.id")
            //Datos del usuario creado
            ->leftJoin($nombreTablaEntidad . " as entidadUsuarioCreador", function ($q) {
              $q->on("tareaNotificacion.idUsuarioCreador", "=", "entidadUsuarioCreador.id")
              ->on("entidadUsuarioCreador.eliminado", "=", DB::raw("0"));
            })
            //Datos del usuario asignado
            ->leftJoin($nombreTablaEntidad . " as entidadUsuarioAsignado", function ($q) use($nombreTablaTarea) {
              $q->on($nombreTablaTarea . ".idUsuarioAsignado", "=", "entidadUsuarioAsignado.id")
              ->on("entidadUsuarioAsignado.eliminado", "=", DB::raw("0"));
            })
            ->where("tareaNotificacion.eliminado", 0)
            ->groupBy("tareaNotificacion.id")
            ->distinct();

    return $tareas->select(DB::raw(
                            $nombreTablaTarea . ".*," .
                            "tareaNotificacion.mensaje, 
                            tareaNotificacion.adjuntos, 
                            tareaNotificacion.fechaProgramada, 
                            tareaNotificacion.fechaNotificacion, 
                            tareaNotificacion.fechaRegistro, 
                            entidadUsuarioCreador.id AS idUsuarioCreador, 
                            entidadUsuarioCreador.nombre AS nombreUsuarioCreador, 
                            entidadUsuarioCreador.apellido AS apellidoUsuarioCreador, 
                            entidadUsuarioAsignado.nombre AS nombreUsuarioAsignado, 
                            entidadUsuarioAsignado.apellido AS apellidoUsuarioAsignado")
    );
  }

  public static function listar($datos)/* - */ {
    $nombreTablaTareaNotificacion = TareaNotificacion::nombreTabla();
    $tareas = Tarea::listarBase();
    Util::aplicarFiltrosBusquedaXFechas($tareas, $nombreTablaTareaNotificacion, "fechaProgramada", $datos);
    return $tareas;
  }

  public static function listarNoRealizadas($seleccionarMisTareas = TRUE)/* - */ {
    $nombreTablaTarea = Tarea::nombreTabla();

    $tareas = Tarea::listarBase();
    if ($seleccionarMisTareas) {
      $tareas->where("entidadUsuarioAsignado.id", Auth::user()->idEntidad);
    } else {
      $tareas->where("entidadUsuarioCreador.id", Auth::user()->idEntidad);
    }
    return $tareas->whereNull($nombreTablaTarea . ".fechaRealizacion")->get();
  }

  public static function listarParaPanel($seleccionarMisTareas = TRUE)/* - */ {
    $tareasNoRealizadas = Tarea::listarNoRealizadas($seleccionarMisTareas);

    //Tareas recientemente realizadas    
    $nombreTablaTarea = Tarea::nombreTabla();

    $fechaActual = Carbon::now();
    $fechaBusIni = Carbon::createFromFormat("d/m/Y H:i:s", $fechaActual->addDays(-2)->format('d/m/Y') . " 00:00:00");
    $fechaBusFin = Carbon::createFromFormat("d/m/Y H:i:s", $fechaActual->addDays(2)->format('d/m/Y') . " 23:59:59");

    $preTareasRealizadas = Tarea::listarBase();
    if ($seleccionarMisTareas) {
      $preTareasRealizadas->where("entidadUsuarioAsignado.id", Auth::user()->idEntidad);
    } else {
      $preTareasRealizadas->where("entidadUsuarioCreador.id", Auth::user()->idEntidad);
    }
    $tareasRealizadas = $preTareasRealizadas->whereNotNull($nombreTablaTarea . ".fechaRealizacion")
                    ->whereBetween($nombreTablaTarea . ".fechaRealizacion", [$fechaBusIni, $fechaBusFin])->get();

    return $tareasNoRealizadas->merge($tareasRealizadas);
  }

  public static function obtenerXId($id)/* - */ {
    return Tarea::listarBase()->where("tareaNotificacion.id", $id)->firstOrFail();
  }

  public static function registrarActualizar($datos) {
    $datos["fechaProgramada"] = (isset($datos["fechaProgramada"]) ? Carbon::createFromFormat("d/m/Y H:i:s", $datos["fechaProgramada"]) : NULL);
    $datos["fechaFinalizacion"] = (isset($datos["fechaFinalizacion"]) ? Carbon::createFromFormat("d/m/Y H:i:s", $datos["fechaFinalizacion"]) : NULL);

    if (!isset($datos["fechaProgramada"]) || (isset($datos["notificarInmediatamente"]) && $datos["notificarInmediatamente"] == 1)) {
      $datos["fechaProgramada"] = Carbon::now()->toDateTimeString();
    }

    if (!(isset($datos["idTarea"]) && $datos["idTarea"] != "")) {
      //Registro
      $datos["adjuntos"] = Archivo::procesarArchivosSubidosNUEVO("", $datos, 5, "Adjuntos");

      $idTareaNotificacion = TareaNotificacion::registrar($datos, FALSE);
      $tarea = new Tarea($datos);
      $tarea->id = $idTareaNotificacion;
      $tarea->idUsuarioAsignado = $datos["idUsuarioAsignado"];
      $tarea->estado = EstadosTarea::Pendiente;
      $tarea->save();
    } else {
      //Actualización
      $idTareaNotificacion = $datos["idTarea"];
      $tarea = Tarea::obtenerXId($idTareaNotificacion);
      
      //Pasado la fecha programada de la tarea no se pueden cambiar sus datos de programación
      $fechaActual = Carbon::now();
      $fechaProgramada = Carbon::createFromFormat("Y-m-d H:i:s", $tarea->fechaProgramada);
      if ($fechaActual >= $fechaProgramada) {
        unset($datos["notificarInmediatamente"]);
        unset($datos["fechaProgramada"]);
        unset($datos["fechaNotificacion"]);
      }
      
      $datos["adjuntos"] = Archivo::procesarArchivosSubidosNUEVO($tarea->adjuntos, $datos, 5, "Adjuntos");

      TareaNotificacion::actualizar($idTareaNotificacion, $datos, FALSE);
      $tarea->idUsuarioAsignado = $datos["idUsuarioAsignado"];
      $tarea->update($datos);
    }
  }

  public static function revisarMultiple($ids) {
    $idUsuarioActual = Auth::user()->idEntidad;

    foreach ($ids as $id) {
      $tarea = Tarea::where("id", $id)
              ->where("idUsuarioAsignado", $idUsuarioActual)
              ->first();
      if ($tarea != NULL) {
        Tarea::where("id", $id)
                ->where("idUsuarioAsignado", $idUsuarioActual)
                ->update(["fechaRevision" => Carbon::now()->toDateTimeString()]);
      }
    }
  }

  public static function actualizarEstado($id, $estado)/* - */ {
    $tarea = Tarea::obtenerXId($id);
    $tarea->estado = $estado;
    $tarea->fechaRealizacion = NULL;
    if ($estado == EstadosTarea::Realizada) {
      $tarea->fechaRealizacion = Carbon::now()->toDateTimeString();
    }
    $tarea->save();
  }

  public static function eliminar($id) {
    TareaNotificacion::eliminarGrupo([$id]);
  }

}
