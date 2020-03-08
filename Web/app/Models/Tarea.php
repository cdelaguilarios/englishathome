<?php

namespace App\Models;

use DB;
use Auth;
use Carbon\Carbon;
use App\Helpers\Util;
use Illuminate\Database\Eloquent\Model;

class Tarea extends Model {

  public $timestamps = false;
  protected $table = "tarea";
  protected $fillable = [
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
    $nombreTablaEntidadTarea = EntidadTarea::nombreTabla();

    $tareas = Tarea::leftJoin($nombreTablaTareaNotificacion . " AS tareaNotificacion", $nombreTablaTarea . ".id", "=", "tareaNotificacion.id")
            ->leftJoin($nombreTablaEntidadTarea . " as entidadTarea", $nombreTablaTarea . ".id", "=", "entidadTarea.idTarea")
            ->leftJoin($nombreTablaEntidad . " AS entidadInvolucradaTarea", function ($q) use ($nombreTablaEntidadTarea, $nombreTablaTarea) {
              $q->on("entidadInvolucradaTarea.id", "IN", DB::raw("(SELECT idEntidad 
                                                                            FROM " . $nombreTablaEntidadTarea . "
                                                                            WHERE idTarea = " . $nombreTablaTarea . ".id)"));
            })
            ->where("tareaNotificacion.eliminado", 0)
            ->groupBy("tareaNotificacion.id")
            ->distinct();

    if (!Auth::guest()) {
      $tareas->leftJoin($nombreTablaEntidadTarea . " AS entidadTareaUsuarioActual", function ($q) {
        $q->on("entidadTareaUsuarioActual.idTarea", "=", "entidadTarea.idTarea")
                ->on("entidadTareaUsuarioActual.idEntidad", "=", DB::raw(Auth::user()->idEntidad));
      });
    }

    return $tareas->select(DB::raw(
                            $nombreTablaTarea . ".*," .
                            (Auth::guest() ? "NULL AS 'fechaRevision'," : "entidadTareaUsuarioActual.fechaRevision,") .
                            (Auth::guest() ? "NULL AS 'fechaRealizada'," : "entidadTareaUsuarioActual.fechaRealizada,") .
                            "tareaNotificacion.idUsuarioCreador, 
                            tareaNotificacion.titulo, 
                            tareaNotificacion.mensaje, 
                            tareaNotificacion.adjuntos, 
                            tareaNotificacion.fechaProgramada, 
                            tareaNotificacion.fechaNotificacion, 
                            tareaNotificacion.fechaRegistro, 
                            GROUP_CONCAT(
                              DISTINCT CONCAT(entidadInvolucradaTarea.tipo, '-', entidadInvolucradaTarea.id, ':', entidadInvolucradaTarea.nombre, ' ', entidadInvolucradaTarea.apellido) 
                              SEPARATOR ';'
                            ) AS entidadesInvolucradas")
    );
  }

  public static function obtenerXId($id)/* - */ {
    return Tarea::listarBase()->where("tareaNotificacion.id", $id)->firstOrFail();
  }

  public static function listar($datos)/* - */ {
    $nombreTablaTareaNotificacion = TareaNotificacion::nombreTabla();

    $tareas = Tarea::listarBase();
    Util::aplicarFiltrosBusquedaXFechas($tareas, $nombreTablaTareaNotificacion, "fechaNotificacion", $datos);

    return $tareas;
  }

  public static function listarNuevas()/* - */ {
    if (!Auth::guest()) {
      $fechaActual = Carbon::now();
      $fechaBusIni = Carbon::createFromFormat("d/m/Y H:i:s", $fechaActual->format('d/m/Y') . " 00:00:00");
      $fechaBusFin = Carbon::createFromFormat("d/m/Y H:i:s", $fechaActual->format('d/m/Y') . " 23:59:59");

      $tareas = Tarea::listarBase();
      $tareas->whereBetween("tareaNotificacion.fechaNotificacion", [$fechaBusIni, $fechaBusFin])
              ->whereNull("entidadTareaUsuarioActual.fechaRealizada");
      return $tareas->get();
    }
    return null;
  }

  public static function registrarActualizar($datos) {
    $idEntidadesSel = (is_array($datos["idEntidades"]) ? $datos["idEntidades"] : [$datos["idEntidades"]]);
    if (count($idEntidadesSel) > 0) {
      if (!isset($datos["fechaProgramada"]) || (isset($datos["notificarInmediatamente"]) && $datos["notificarInmediatamente"] == 1)) {
        $datos["fechaProgramada"] = Carbon::now()->toDateTimeString();
      }
      $datos["fechaNotificacion"] = $datos["fechaProgramada"];

      if (!(isset($datos["idTarea"]) && $datos["idTarea"] != "")) {
        //Registro
        $datos["adjuntos"] = Archivo::procesarArchivosSubidosNUEVO("", $datos, 5, "Adjuntos");

        $idTareaNotificacion = TareaNotificacion::registrar($datos);
        $tarea = new Tarea($datos);
        $tarea->id = $idTareaNotificacion;
        $tarea->save();
      } else {
        //Actualización
        $idTareaNotificacion = $datos["idTarea"];
        $tarea = Tarea::obtenerXId($idTareaNotificacion);
        $datos["adjuntos"] = Archivo::procesarArchivosSubidosNUEVO($tarea->adjuntos, $datos, 5, "Adjuntos");

        TareaNotificacion::actualizar($idTareaNotificacion, $datos);
        $tarea->update($datos);
      }
      Tarea::registrarActualizarEntidades($idTareaNotificacion, $idEntidadesSel);
    }
  }

  private static function registrarActualizarEntidades($idTarea, $idEntidades)/* - */ {
    EntidadTarea::where("idTarea", $idTarea)->delete();
    foreach ($idEntidades as $idEntidad) {
      if (isset($idEntidad)) {
        $entidadTarea = new EntidadTarea([ "idEntidad" => $idEntidad, "idTarea" => $idTarea]);
        $entidadTarea->save();
      }
    }
  }

  public static function eliminar($id) {
    TareaNotificacion::eliminarGrupo([$id]);
  }

}
