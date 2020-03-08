<?php

namespace App\Http\Controllers;

use Log;
use Auth;
use Input;
use Datatables;
use Carbon\Carbon;
use App\Models\Tarea;
use App\Models\EntidadTarea;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tarea\FormularioRequest;
use App\Http\Requests\Tarea\ListaRequest;

class TareaController extends Controller {

  protected $data = array();

  public function __construct() {
    
  }

  public function listar(ListaRequest $req)/* - */ {
    return Datatables::of(Tarea::listar($req->all()))->make(true);
  }

  public function listarNuevas()/* - */ {
    return response()->json(Tarea::listarNuevas(), 200);
  }

  public function registrarActualizar($idEntidad, FormularioRequest $req)/* - */ {
    try {
      $datos = $req->all();
      $datos["idEntidades"] = [$idEntidad, (Auth::guest() ? NULL : Auth::user()->idEntidad)];
      $datos["fechaProgramada"] = (isset($datos["fechaProgramada"]) ? Carbon::createFromFormat("d/m/Y H:i:s", $datos["fechaProgramada"]) : NULL);

      $cambioAprobado = TRUE;
      if (isset($datos["idTarea"]) && $datos["idTarea"] != "") {
        //Pasado la fecha de notificación de la tarea no se pueden cambiar los datos de programación
        $tarea = Tarea::obtenerXId($datos["idTarea"]);
        $fechaActual = Carbon::now();
        $fechaNotificacion = Carbon::createFromFormat("Y-m-d H:i:s", $tarea->fechaNotificacion);
        if ($fechaActual >= $fechaNotificacion) {
          unset($datos["notificarInmediatamente"]);
          unset($datos["fechaProgramada"]);
          unset($datos["fechaNotificacion"]);
        }
      }

      if ($cambioAprobado) {
        Tarea::registrarActualizar($datos);
      }
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "Ocurrió un problema durante el registro y/o actualización de datos. Por favor inténtelo nuevamente."], 500);
    }
    return response()->json(["mensaje" => "Se guardaron los datos exitosamente."], 200);
  }

  public function obtenerDatos($id)/* - */ {
    return response()->json(Tarea::obtenerXId($id), 200);
  }

  public function actualizarRealizacion($id) {
    try {
      $realizado = (Input::get("realizado") === "true");
      EntidadTarea::actualizarRealizacion($id, $realizado);
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "No se pudo actualizar la realización de la tarea seleccionada."], 400);
    }
    return response()->json(["mensaje" => "Actualización exitosa.", "id" => $id], 200);
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
