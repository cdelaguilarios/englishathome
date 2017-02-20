<?php

namespace App\Http\Controllers;

use Datatables;
use App\Models\Pago;
use App\Models\Clase;
use App\Models\Docente;
use App\Http\Controllers\Controller;
use App\Http\Requests\Util\BusquedaRequest;
use App\Http\Requests\Reporte\BusquedaDocenteRequest;

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
    return response()->json(Clase::reporte($req->all()), 200);
  }

  public function pagos() {
    $this->data["subSeccion"] = "pagos";
    return view("reporte.pagos", $this->data);
  }

  public function listarPagos(BusquedaRequest $req) {
    return response()->json(Pago::reporte($req->all()), 200);
  }

  public function docentesDisponibles() {
    $this->data["subSeccion"] = "docentes-disponibles";
    return view("reporte.docentesDisponibles", $this->data);
  }

  public function listarDocentesDisponibles(BusquedaDocenteRequest $req) {   
    $datos = $req->all();
    $datos["horaInicio"] = 0; 
    $datos["duracion"] = 86400; 
    return Datatables::of(Docente::listarDisponiblesXFecha($datos))
                    ->filterColumn('nombreCompleto', function($q, $k) {
                      $q->whereRaw('CONCAT(entidad.nombre, " ", entidad.apellido) like ?', ["%{$k}%"]);
                    })->make(true);
  }

}
