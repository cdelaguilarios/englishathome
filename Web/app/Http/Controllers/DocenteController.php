<?php

namespace App\Http\Controllers;

use Log;
use Mensajes;
use Datatables;
use App\Models\Docente;
use App\Models\Profesor;
use App\Http\Controllers\Controller;
use App\Http\Requests\Docente\BusquedaDisponiblesRequest;
use App\Http\Requests\Docente\FormularioExperienciaLaboralRequest;

class DocenteController extends Controller {

  protected $data = array();

  public function __construct() {
    $this->data["seccion"] = "docentes";
  }

  public function disponibles() {
    $this->data["subSeccion"] = "disponibles";
    return view("docente.listaDisponibles", $this->data);
  }

  public function listarDisponibles(BusquedaDisponiblesRequest $req) {
    return Datatables::of(Docente::listarDisponibles($req->all()))->filterColumn("entidad.nombre", function($q, $k) {
              $q->whereRaw('CONCAT(entidad.nombre, " ", entidad.apellido) like ?', ["%{$k}%"]);
            })->filterColumn("entidad.fechaRegistro", function($q, $k) {
              $q->whereRaw("DATE_FORMAT(entidad.fechaRegistro, '%d/%m/%Y %H:%i:%s') like ?", ["%{$k}%"]);
            })->make(true);
  }

  public function actualizarExperienciaLaboral($id, FormularioExperienciaLaboralRequest $req) {
    try {
      Docente::actualizarExperienciaLaboral($id, $req);
      Mensajes::agregarMensajeExitoso("Actualización exitosa.");
    } catch (\Exception $e) {
      Log::error($e->getMessage());
      Mensajes::agregarMensajeError("Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente.");
    }
    return redirect(route((Profesor::verificarExistencia($id) ? "profesores" : "postulantes") . ".perfil", ["id" => $id, "sec" => "experiencia-laboral"]));
  }

}
