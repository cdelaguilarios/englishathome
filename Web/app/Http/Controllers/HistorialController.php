<?php

namespace App\Http\Controllers;

use App\Models\Historial;
use App\Http\Controllers\Controller;
use App\Http\Requests\HistorialRequest;

class HistorialController extends Controller {

  protected $data = array();

  public function __construct() {
    
  }

  public function historial($id, HistorialRequest $req) {
    $datos = $req->all();
    $datosHistorial = Historial::obtener($datos["numeroCarga"], $id);
    return response()->json($datosHistorial, 200);
  }

}
