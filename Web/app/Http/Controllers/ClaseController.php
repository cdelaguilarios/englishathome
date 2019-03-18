<?php

namespace App\Http\Controllers;

use Datatables;
use App\Models\Clase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Clase\BusquedaPropiasRequest;

class ClaseController extends Controller {

  protected $data = array();

  public function __construct() {
    $this->data["seccion"] = "clases";
  }

  public function propias() {
    return view("clase.listaPropias", $this->data);
  }

  public function listar(BusquedaPropiasRequest $req) {
    return Datatables::of(Clase::listarPropias($req->all()))->make(true);
  }
  
}
