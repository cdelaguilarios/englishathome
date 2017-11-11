<?php

namespace App\Http\Controllers;

use Datatables;
use App\Models\Pago;
use App\Models\Clase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Util\BusquedaRequest;
use App\Helpers\Enum\EntidadesReporte;

class ReporteController extends Controller {

  protected $data = array();

  public function __construct() {
    $this->data["seccion"] = "reportes";
  }

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

  public function motor() {
    $this->data["subSeccion"] = "motor";
    $this->data["entidades"] = EntidadesReporte::listar();
    return view("reporte.motor", $this->data);
  }

  public function listarEntidadesRelacionadas($entidad) {
    
  }

}
