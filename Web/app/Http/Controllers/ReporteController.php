<?php

namespace App\Http\Controllers;

use Datatables;
use App\Models\Pago;
use App\Models\Clase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Util\BusquedaRequest;

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
    return Datatables::of(Clase::listar($req->all()))->make(true);
  }

  public function listarClasesGrafico(BusquedaRequest $req) {
    return response()->json(Clase::reporte($req->all()), 200);
  }

  public function pagos() {
    $this->data["subSeccion"] = "pagos";
    return view("reporte.pagos", $this->data);
  }

  public function listarPagos(BusquedaRequest $req) {
    return Datatables::of(Pago::listar($req->all()))->make(true);
  }

  public function listarPagosGrafico(BusquedaRequest $req) {
    return response()->json(Pago::reporte($req->all()), 200);
  }

}
