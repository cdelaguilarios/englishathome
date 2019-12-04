<?php

namespace App\Http\Controllers;

use Log;
use Mensajes;
use Datatables;
use App\Models\Clase;
use App\Models\Docente;
use App\Models\Profesor;
use App\Models\PagoProfesor;
use App\Http\Controllers\Controller;
use App\Http\Requests\Docente\BusquedaRequest;
use App\Http\Requests\Docente\Pago as PagoRequest;
use App\Http\Requests\Docente\FormularioExperienciaLaboralRequest;

class DocenteController extends Controller/* - */ {

  protected $data = array();

  public function __construct()/* - */ {
    $this->data["seccion"] = "docentes";
  }

  public function disponibles()/* - */ {
    $this->data["subSeccion"] = "disponibles";
    return view("docente.listaDisponibles", $this->data);
  }

  public function listarDisponibles(BusquedaRequest $req)/* - */ {
    return Datatables::of(Docente::listarDisponibles($req->all()))->filterColumn("nombreCompleto", function($q, $k) {
              $q->whereRaw('CONCAT(entidad.nombre, " ", entidad.apellido) like ?', ["%{$k}%"]);
            })->filterColumn('estado', function($q, $k) {
              $q->whereRaw('entidad.estado like ?', ["%{$k}%"]);
            })->filterColumn("entidad.fechaRegistro", function($q, $k) {
              $q->whereRaw("DATE_FORMAT(entidad.fechaRegistro, '%d/%m/%Y %H:%i:%s') like ?", ["%{$k}%"]);
            })->make(true);
  }

  public function pagosXClases()/* - */ {
    $this->data["subSeccion"] = "pagos";
    return view("docente.pago.principal", $this->data);
  }

  public function listarPagosXClases(PagoRequest\ListarXClasesRequest $req)/* - */ {
    return Datatables::of(PagoProfesor::listarXClases($req->all()))
                    ->filterColumn("estadoPago", function($q, $k) {
                      $q->whereRaw("estadoPago like ?", ["%{$k}%"])
                      ->orWhereRaw("DATE_FORMAT(fechaPago, '%d/%m/%Y') like ?", ["%{$k}%"]);
                    })->make(true);
  }

  public function listarPagosXClasesDetalle($id, PagoRequest\ListarXClasesRequest $req)/* - */ {
    $nombreTablaClase = Clase::nombreTabla();
    return Datatables::of(PagoProfesor::listarXClasesDetalle($id, $req->all()))
                    ->filterColumn("fechaConfirmacion", function($q, $k) use($nombreTablaClase) {
                      $q->whereRaw("DATE_FORMAT(" . $nombreTablaClase . ".fechaConfirmacion, '%d/%m/%Y') like ?", ["%{$k}%"]);
                    })
                    ->filterColumn("duracion", function($q, $k) use($nombreTablaClase) {
                      $q->whereRaw("SEC_TO_TIME(" . $nombreTablaClase . ".duracion) like ?", ["%{$k}%"]);
                    })
                    ->filterColumn("pagoTotalFinalProfesor", function($q, $k) {
                      $q->whereRaw("(T.costoHoraProfesor * (T.duracion/3600)) like ?", ["%{$k}%"]);
                    })->make(true);
  }

  public function registrarActualizarPagoXClases(PagoRequest\FormularioRequest $req)/* - */ {
    try {
      $datos = $req->all();
      PagoProfesor::registrarActualizarXClases($datos["idProfesor"], $req);
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "Ocurrió un problema durante " . (isset($datos["idPago"]) ? "la actualización" : "el registro") . " de datos. Por favor inténtelo nuevamente."], 500);
    }
    return response()->json(["mensaje" => (isset($datos["idPago"]) ? "Actualización exitosa." : "Registro exitoso.")], 200);
  }

  public function eliminarPagoXClases($id, $idPago)/* - */ {
    try {
      PagoProfesor::eliminar($id, $idPago);
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "No se pudo eliminar el registro de datos del pago seleccionado."], 400);
    }
    return response()->json(["mensaje" => "Eliminación exitosa.", "id" => $idPago], 200);
  }

  public function actualizarExperienciaLaboral($id, FormularioExperienciaLaboralRequest $req)/* - */ {
    try {
      Docente::actualizarExperienciaLaboral($id, $req);
      Mensajes::agregarMensajeExitoso("Actualización exitosa.");
    } catch (\Exception $e) {
      Log::error($e->getMessage());
      Mensajes::agregarMensajeError("Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente.");
    }
    return redirect(route((Profesor::verificarExistencia($id) ? "profesores" : "postulantes") . ".perfil", ["id" => $id, "seccion" => "experiencia-laboral"]));
  }

}
