<?php

namespace App\Http\Controllers;

use App\Models\Clase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Calendario\ListaRequest;

class CalendarioController extends Controller {

  protected $data = array();

  public function __construct() {
    
  }

  public function listar($idEntidad, ListaRequest $req) {
    $eventos = Clase::calendario($idEntidad, $req->all());
    return response()->json($eventos, 200);
  }

}
