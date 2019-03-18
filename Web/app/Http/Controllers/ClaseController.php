<?php

namespace App\Http\Controllers;

use Auth;
use Datatables;
use App\Models\Clase;
use App\Helpers\Enum\RolesUsuario;
use App\Http\Controllers\Controller;
use App\Http\Requests\Clase\BusquedaPropiasRequest;

class ClaseController extends Controller {

  protected $data = array();

  public function __construct() {
    $this->data["seccion"] = "clases";
  }

  public function propias() {
    return view("clase.listarPropias", $this->data);
  }

  public function listarPropias(BusquedaPropiasRequest $req) {
    return Datatables::of(Clase::listarPropias($req->all()))
                    ->filterColumn("fechaInicio", function($q, $k) {
                      $q->whereRaw('fechaInicio like ?', ["%{$k}%"])
                      ->orWhereRaw('duracion like ?', ["%{$k}%"])
                      ->orWhereRaw('CONCAT(' . (Auth::user()->rol == RolesUsuario::Alumno ? 'entidadProfesor.nombre, " ", entidadProfesor.apellido' : 'entidadAlumno.nombre, " ", entidadAlumno.apellido') . ') like ?', ["%{$k}%"]);
                    })
                    ->filterColumn("comentarioEntidad", function($q, $k) {
                      $q->whereRaw(Clase::nombreTabla() . (Auth::user()->rol == RolesUsuario::Alumno ? ".comentarioAlumno" : ".comentarioProfesor") . ' like ?', ["%{$k}%"]);
                    })
                    ->filterColumn("comentarioAdministrador", function($q, $k) {
                      $q->whereRaw(Clase::nombreTabla() . (Auth::user()->rol == RolesUsuario::Alumno ? ".comentarioAdministradorParaAlumno" : ".comentarioAdministradorParaProfesor") . ' like ?', ["%{$k}%"]);
                    })->make(true);
  }

}
