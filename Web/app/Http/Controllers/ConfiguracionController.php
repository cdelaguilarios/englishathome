<?php

namespace App\Http\Controllers;

use Log;
use Mensajes;
use App\Models\VariableSistema;
use App\Http\Controllers\Controller;
use App\Http\Requests\Configuracion\FormularioRequest;

class ConfiguracionController extends Controller/* - */ {

  protected $data = array();

  public function __construct()/* - */ {
    $this->data["seccion"] = "configuracion";
  }

  public function index()/* - */ {
    $this->data["variablesSistema"] = VariableSistema::listar();
    return view("configuracion.index", $this->data);
  }

  public function actualizar(FormularioRequest $req) /* - */{
    try {
      VariableSistema::actualizar($req->all());
      Mensajes::agregarMensajeExitoso("Actualización exitosa.");
    } catch (\Exception $e) {
      Log::error($e->getMessage());
      Mensajes::agregarMensajeError("Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente.");
    }
    return redirect(route("configuracion"));
  }

}
