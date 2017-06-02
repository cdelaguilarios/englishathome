<?php

namespace App\Http\Controllers;

use Datatables;
use App\Models\Docente;
use App\Http\Controllers\Controller;
use App\Http\Requests\Docente\BusquedaDisponiblesRequest;

class DocenteController extends Controller {

  protected $data = array();

  public function __construct() {
    $this->data["seccion"] = "docentes";
  }

  public function disponibles() {
    $this->data["subSeccion"] = "disponibles";
    return view("docente.listaDisponibles", $this->data);
  }

  public function listarDisponibles(BusquedaDisponiblesRequest $req) {
    return Datatables::of(Docente::listarDisponibles($req->all()))->filterColumn("entidad.nombre", function($q, $k) {
              $q->whereRaw('CONCAT(entidad.nombre, " ", entidad.apellido) like ?', ["%{$k}%"]);
            })->filterColumn("entidad.fechaRegistro", function($q, $k) {
              $q->whereRaw("DATE_FORMAT(entidad.fechaRegistro, '%d/%m/%Y %H:%i:%s') like ?", ["%{$k}%"]);
            })->make(true);
  }

}
