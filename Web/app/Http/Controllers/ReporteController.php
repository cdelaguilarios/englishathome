<?php

namespace App\Http\Controllers;

use Log;
use Mensajes;
use Datatables;
use App\Models\Pago;
use App\Models\Clase;
use App\Models\Reporte;
use App\Http\Controllers\Controller;
use App\Http\Requests\Util\BusquedaRequest;
use App\Http\Requests\Reporte\FormularioRequest;
use App\Http\Requests\Reporte\ListarCamposRequest;
use App\Http\Requests\Reporte\ListarEntidadesRelacionadasRequest;

class ReporteController extends Controller {

  protected $data = array();

  public function __construct() {
    $this->data["seccion"] = "reportes";
  }

  //Motor
  public function index() {
    return view("reporte.motor.lista", $this->data);
  }

  public function listar() {
    return Datatables::of(Reporte::listar())->make(true);
  }

  public function crear() {
    $this->data["subSeccion"] = "motor";
    return view("reporte.motor.crear", $this->data);
  }

  public function registrar(FormularioRequest $req) {
    try {
      Reporte::registrar($req);
      Mensajes::agregarMensajeExitoso("Registro exitoso.");
      return redirect(route("reportes"));
    } catch (\Exception $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("Ocurrió un problema durante el registro de datos. Por favor inténtelo nuevamente.");
      return redirect(route("reportes.crear"));
    }
  }

  public function editar($id) {
    try {
      $this->data["subSeccion"] = "motor";
      $this->data["reporte"] = Reporte::obtenerXId($id);
    } catch (ModelNotFoundException $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("No se encontraron datos del reporte seleccionado. Es posible que haya sido eliminado.");
      return redirect(route("reportes"));
    }
    return view("reporte.motor.editar", $this->data);
  }

  public function actualizar($id, FormularioRequest $req) {
    try {
      Reporte::actualizar($id, $req);
      Mensajes::agregarMensajeExitoso("Actualización exitosa.");
    } catch (\Exception $e) {
      Log::error($e->getMessage());
      Mensajes::agregarMensajeError("Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente.");
    }
    return redirect(route("reportes.editar", ["id" => $id]));
  }

  public function eliminar($id) {
    try {
      Reporte::eliminar($id);
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "No se pudo eliminar el registro de datos del reporte seleccionado."], 400);
    }
    return response()->json(["mensaje" => "Eliminación exitosa.", "id" => $id], 200);
  }

  public function listarCampos(ListarCamposRequest $req) {
    $datos = $req->all();
    return response()->json(Reporte::listarCampos($datos["entidad"]), 200);
  }

  public function listarEntidadesRelacionadas(ListarEntidadesRelacionadasRequest $req) {
    $datos = $req->all();
    return response()->json(Reporte::listarEntidadesRelacionadas($datos["entidad"]), 200);
  }

  //Otros
  public function clases() {
    $this->data["subSeccion"] = "clases";
    return view("reporte.clases", $this->data);
  }

  public function listarClases(BusquedaRequest $req) {
    return Datatables::of(Clase::listar($req->all()))->filterColumn("nombreProfesor", function($q, $k) {
              $q->whereRaw('CONCAT(entidadProfesor.nombre, " ", entidadProfesor.apellido) like ?', ["%{$k}%"]);
            })->filterColumn("nombreAlumno", function($q, $k) {
              $q->whereRaw('CONCAT(entidadAlumno.nombre, " ", entidadAlumno.apellido) like ?', ["%{$k}%"]);
            })->filterColumn("fechaInicio", function($q, $k) {
              $q->whereRaw("DATE_FORMAT(" . Clase::nombreTabla() . ".fechaInicio, '%d/%m/%Y %H:%i:%s') like ?", ["%{$k}%"]);
            })->filterColumn("estado", function($q, $k) {
              $q->whereRaw('CONCAT("Clase - ", ' . Clase::nombreTabla() . '.estado) like ?', ["%{$k}%"]);
            })->filterColumn("duracion", function($q, $k) {
              $q->whereRaw("SEC_TO_TIME(" . Clase::nombreTabla() . ".duracion) like ?", ["%{$k}%"]);
            })->make(true);
  }

  public function listarClasesGrafico(BusquedaRequest $req) {
    return response()->json(Clase::reporte($req->all()), 200);
  }

  public function pagos() {
    $this->data["subSeccion"] = "pagos";
    return view("reporte.pagos", $this->data);
  }

  public function listarPagos(BusquedaRequest $req) {
    return Datatables::of(Pago::listar($req->all()))->filterColumn("nombreEntidad", function($q, $k) {
              $q->whereRaw('CONCAT(entidad.nombre, " ", entidad.apellido) like ?', ["%{$k}%"]);
            })->filterColumn("fechaRegistro", function($q, $k) {
              $q->whereRaw("DATE_FORMAT(" . Pago::nombreTabla() . ".fecha, '%d/%m/%Y %H:%i:%s') like ?", ["%{$k}%"]);
            })->make(true);
  }

  public function listarPagosGrafico(BusquedaRequest $req) {
    return response()->json(Pago::reporte($req->all()), 200);
  }

}
