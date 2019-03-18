<?php

namespace App\Http\Controllers;

use Auth;
use File;
use Storage;
use Response;
use App\Helpers\Enum\RolesUsuario;

class InicioController extends Controller {

  public function inicio() {
    if (in_array(Auth::user()->rol, [RolesUsuario::Alumno, RolesUsuario::Profesor])) {
      return redirect(route("clases.propias"));
    } else {
      return redirect(route("interesados"));
    }
  }

  public function obtenerImagen($imagen) {
    $rutaBaseAlmacenamiento = Storage::disk("local")->getDriver()->getAdapter()->getPathPrefix();
    $rutaFin = $rutaBaseAlmacenamiento . $imagen;

    if ($imagen == "" || !File::exists($rutaFin)) {
      $rutaFin = public_path() . "/assets/eah/img/no-disponible.png";
    }
    $archivo = File::get($rutaFin);
    $tipo = File::mimeType($rutaFin);
    $response = Response::make($archivo, 200);
    $response->header("Content-Type", $tipo);
    return $response;
  }

}
