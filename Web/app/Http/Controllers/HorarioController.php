<?php

namespace App\Http\Controllers;

use App\Models\Horario;
use App\Http\Requests\Horario\HorarioMultipleRequest;

class HorarioController extends Controller {

  protected $data = array();

  public function __construct() {
    
  }

  // <editor-fold desc="Horario">
  public function obtener($idEntidad) {
    $datos = [];
    $datos["idEntidad"] = $idEntidad;
    $datos["datosHorario"] = Horario::obtenerFormatoJson($idEntidad);
    return response()->json($datos, 200);
  }

  public function obtenerMultiple(HorarioMultipleRequest $req) {
    $datos = $req->all();
    return response()->json(Horario::obtenerMultiple($datos), 200);
  }

  // </editor-fold>
}
