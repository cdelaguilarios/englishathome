<?php

namespace App\Http\Controllers;

use Log;
use Input;
use Mensajes;
use App\Models\Correo;
use App\Models\Entidad;
use App\Http\Controllers\Controller;
use App\Http\Requests\Correo\BusquedaEntidadRequest;
use App\Http\Requests\Correo\FormularioRequest;

class CorreoController extends Controller {

  protected $data = array();

  public function __construct() {
    $this->data["seccion"] = "correos";
  }


  public function index() {    
    try {
      $idEntidad = Input::get("id");
      if (isset($idEntidad)) {
        $this->data["entidad"] = Entidad::ObtenerXId($idEntidad);
      }
    } catch (\Exception $e) {
      Log::error($e);
    }
    
    return view("correos.index", $this->data);
  }

  public function listarEntidades(BusquedaEntidadRequest $req) {
    return response()->json(Entidad::buscar($req->all()), 200);
  }

  public function registrarCorreos(FormularioRequest $req) {
    try {
      $correosAdicionalesExcluidos = Correo::registrar($req->all());
      Mensajes::agregarMensajeExitoso("Registro exitoso. Los correos se enviaran progresivamente.");
      if ($correosAdicionalesExcluidos != "") {
        Mensajes::agregarMensajeAdvertencia("Los siguientes correos adicionales han sido excluidos: " . $correosAdicionalesExcluidos . ".");
      }
    } catch (\Exception $e) {
      Log::error($e->getMessage());
      Mensajes::agregarMensajeError("Ocurrió un problema durante el registro de datos de los correos. Por favor inténtelo nuevamente.");
    }
    return redirect(route("correos"));
  }
}
