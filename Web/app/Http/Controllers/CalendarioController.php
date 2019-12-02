<?php

namespace App\Http\Controllers;

use App\Models\Clase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Calendario\DatosRequest;

class CalendarioController extends Controller/* - */ {

  protected $data = array();

  public function __construct()/* - */ {
    $this->data["seccion"] = "calendario";    
  }

  public function index()/* - */ {
    return view("calendario.index", $this->data);
  }

  public function datos(DatosRequest $req)/* - */ {
    $eventos = Clase::calendario($req->all());
    return response()->json($eventos, 200);
  }

}
