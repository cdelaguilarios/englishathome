<?php

namespace App\Http\Controllers;

use Log;
use Datatables;
use Carbon\Carbon;
use App\Models\Tarea;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tarea\ListaRequest;
use App\Http\Requests\Tarea\FormularioRequest;
use App\Http\Requests\Tarea\ListaParaPanelRequest;
use App\Http\Requests\Tarea\ActualizarEstadoRequest;
use App\Http\Requests\Tarea\ActualizarRevisionRequest;

class TareaController extends Controller {

  protected $data = array();

  public function __construct() {
    
  }

  public function listar(ListaRequest $req)/* - */ {
    return Datatables::of(Tarea::listar($req->all()))->filterColumn("titulo", function($q, $k) {
              $q->whereRaw('titulo like ?', ["%{$k}%"])
                      ->orWhereRaw('mensaje like ?', ["%{$k}%"]);
            })->filterColumn("fechaNotificacion", function($q, $k) {
              $q->whereRaw("DATE_FORMAT(fechaNotificacion, '%d/%m/%Y %H:%i:%s') like ?", ["%{$k}%"]);
            })->make(true);
  }

  public function listarParaPanel(ListaParaPanelRequest $req)/* - */ {
    $datos = $req->all();
    $seleccionarMisTareas = (isset($datos["seleccionarMisTareas"]) && $datos["seleccionarMisTareas"] != "0");
    return response()->json(Tarea::listarParaPanel($seleccionarMisTareas), 200);
  }

  public function listarNoRealizadas()/* - */ {
    return response()->json(Tarea::listarNoRealizadas(), 200);
  }

  public function obtenerDatos($id)/* - */ {
    return response()->json(Tarea::obtenerXId($id), 200);
  }

  public function registrarActualizar(FormularioRequest $req)/* - */ {
      $datos = $req->all();
      $datos["fechaProgramada"] = (isset($datos["fechaProgramada"]) ? Carbon::createFromFormat("d/m/Y H:i:s", $datos["fechaProgramada"]) : NULL);

      if (isset($datos["idTarea"]) && $datos["idTarea"] != "") {
        //Pasado la fecha programada de la tarea no se pueden cambiar sus datos de programación
        $tarea = Tarea::obtenerXId($datos["idTarea"]);
        $fechaActual = Carbon::now();
        $fechaProgramada = Carbon::createFromFormat("Y-m-d H:i:s", $tarea->fechaProgramada);
        if ($fechaActual >= $fechaProgramada) {
          unset($datos["notificarInmediatamente"]);
          unset($datos["fechaProgramada"]);
          unset($datos["fechaNotificacion"]);
        }
      }

      Tarea::registrarActualizar($datos);
    try {
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "Ocurrió un problema durante el registro y/o actualización de datos. Por favor inténtelo nuevamente."], 500);
    }
    return response()->json(["mensaje" => "Se guardaron los datos exitosamente."], 200);
  }

  public function actualizarEstado($id, ActualizarEstadoRequest $req)/* - */ {
    try {
      $datos = $req->all();
      Tarea::actualizarEstado($id, $datos["estado"]);
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente."], 500);
    }
    return response()->json(["mensaje" => "Actualización exitosa."], 200);
  }

  public function revisarMultiple(ActualizarRevisionRequest $req) {
    try {
      $datos = $req->all();
      $idsTareas = $datos["idsTareas"];
      Tarea::revisarMultiple($idsTareas);
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "No se pudo actualizar la revisión de la tarea seleccionada."], 400);
    }
    return response()->json(["mensaje" => "Actualización exitosa."], 200);
  }

  public function eliminar($id)/* - */ {
    try {
      Tarea::eliminar($id);
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "No se pudo eliminar el registro de datos de la tarea seleccionada."], 400);
    }
    return response()->json(["mensaje" => "Eliminación exitosa.", "id" => $id], 200);
  }

}
