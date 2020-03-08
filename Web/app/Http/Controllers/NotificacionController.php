<?php

namespace App\Http\Controllers;

use Log;
use Input;
use Datatables;
use Carbon\Carbon;
use App\Models\Notificacion;
use App\Models\EntidadNotificacion;
use App\Http\Controllers\Controller;
use App\Http\Requests\Notificacion\ListaRequest;
use App\Http\Requests\Notificacion\FormularioRequest;
use App\Http\Requests\Notificacion\ListaHistorialRequest;
use App\Http\Requests\Notificacion\ActualizarRevisionRequest;

class NotificacionController extends Controller {

  protected $data = array();

  public function __construct() {
    
  }

  public function listar(ListaRequest $req)/* - */ {
    return Datatables::of(Notificacion::listar($req->all()))->filterColumn("titulo", function($q, $k) {
              $q->whereRaw('titulo like ?', ["%{$k}%"])
                      ->orWhereRaw('mensaje like ?', ["%{$k}%"]);
            })->filterColumn("fechaNotificacion", function($q, $k) {
              $q->whereRaw("DATE_FORMAT(fechaNotificacion, '%d/%m/%Y %H:%i:%s') like ?", ["%{$k}%"]);
            })->make(true);
  }

  public function listarNuevas()/* - */ {
    return response()->json(Notificacion::listarNuevas(), 200);
  }

  public function listarHistorial($idEntidad, ListaHistorialRequest $req) {
    $datos = $req->all();
    $datosHistorial = Notificacion::listarHistorial($idEntidad, $datos["numeroCarga"]);
    return response()->json($datosHistorial, 200);
  }

  public function obtenerDatos($id)/* - */ {
    return response()->json(Notificacion::obtenerXId($id), 200);
  }

  public function registrarActualizar(FormularioRequest $req)/* - */ {
    try {
      $datos = $req->all();
      $datos["idEntidades"] = [$datos["idEntidad"]];
      $datos["fechaProgramada"] = (isset($datos["fechaProgramada"]) ? Carbon::createFromFormat("d/m/Y H:i:s", $datos["fechaProgramada"]) : NULL);

      $cambioAprobado = TRUE;
      if (isset($datos["idNotificacion"]) && $datos["idNotificacion"] != "") {
        //Las notificaciones generadas por el sistema no pueden ser actualizadas
        $notificacion = Notificacion::obtenerXId($datos["idNotificacion"]);
        if ($notificacion->idUsuarioCreador == NULL) {
          $cambioAprobado = FALSE;
        }
      }

      if ($cambioAprobado) {
        Notificacion::registrarActualizar($datos, FALSE);
      }
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "Ocurrió un problema durante el registro y/o actualización de datos. Por favor inténtelo nuevamente."], 500);
    }
    return response()->json(["mensaje" => "Se guardaron los datos exitosamente."], 200);
  }

  public function revisarMultiple(ActualizarRevisionRequest $req) {
    try {
      $datos = $req->all();
      $idsNotificaciones = $datos["idsNotificaciones"];
      EntidadNotificacion::revisarMultiple($idsNotificaciones);
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "No se pudo actualizar la revisión de la notificación seleccionada."], 400);
    }
    return response()->json(["mensaje" => "Actualización exitosa."], 200);
  }

  public function eliminar($id)/* - */ {
    try {
      //Las notificaciones generadas por el sistema no pueden ser eliminadas
      $notificacion = Notificacion::obtenerXId($id);
      if ($notificacion->idUsuarioCreador != NULL) {
        Notificacion::eliminar($id);
      }
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "No se pudo eliminar el registro de datos de la notificación seleccionada."], 400);
    }
    return response()->json(["mensaje" => "Eliminación exitosa.", "id" => $id], 200);
  }

}
