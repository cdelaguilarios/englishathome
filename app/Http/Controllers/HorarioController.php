<?php

namespace App\Http\Controllers;

use App\Models\Horario;
use App\Http\Requests\Horario\HorarioMultipleRequest;

class HorarioController extends Controller {

  protected $data = array();

  public function __construct() {    
  }

  public function obtenerMultiple(HorarioMultipleRequest $req) {
    return response()->json(Horario::obtenerMultiple($req->all()), 200);
  }

}
