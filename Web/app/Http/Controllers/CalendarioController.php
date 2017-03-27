<?php

namespace App\Http\Controllers;

use App\Models\Historial;
use App\Http\Controllers\Controller;
use App\Http\Requests\Calendario\ListaRequest;

class CalendarioController extends Controller {

  protected $data = array();

  public function __construct() {
    
  }

  public function listar($idEntidad, ListaRequest $req) {
    $datos = $req->all();
    $datosHistorial = Historial::obtenerPerfil($datos["numeroCarga"], $idEntidad);
    return response()->json($datosHistorial, 200);
  }

}
