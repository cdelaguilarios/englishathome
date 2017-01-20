<?php

namespace App\Http\Controllers;

use Datatables;
use App\Models\Clase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Clase\BusquedaRequest;

class ClaseController extends Controller {

  protected $data = array();

  public function __construct() {
    $this->data["seccion"] = "clases";
  }

  public function index() {
    return view("clase.lista", $this->data);
  }

  public function listar(BusquedaRequest $req) {
    return Datatables::of(Clase::listar($req->all()))->make(true);
  }

}
