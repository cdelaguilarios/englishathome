<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Http\Controllers\Controller;

class CursoController extends Controller {

  protected $data = array();

  public function __construct() {
    
  }

  public function datos($id) {
    $datosCurso = Curso::obtenerXId($id);
    return response()->json($datosCurso, 200);
  }

}
