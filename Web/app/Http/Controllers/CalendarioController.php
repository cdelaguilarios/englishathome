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
    $aa = Clase::listarXRangoFecha($idEntidad, $req->all());
    print_r($aa);
    die;
    $datosHistorial = [];
    return response()->json($datosHistorial, 200);
  }
  //https://fullcalendar.io/docs/event_data/events_json_feed/
  //https://fullcalendar.io/docs/event_data/Event_Object/

}
