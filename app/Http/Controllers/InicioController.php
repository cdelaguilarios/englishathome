<?php

namespace App\Http\Controllers;

use Auth;
use File;
use Storage;
use Response;
use App\Helpers\Enum\RolesUsuario;

class InicioController extends Controller {

  public function inicio() {
    if (Auth::user()->rol == RolesUsuario::Profesor)
      return redirect(route("profesores.mis.alumnos"));
    else if (Auth::user()->rol == RolesUsuario::Alumno)
      return redirect(route("alumnos.mis.clases"));
    else
      return redirect(route("interesados"));
  }

  public function obtenerImagen($imagen) {
    $rutaBaseAlmacenamiento = Storage::disk("local")->getDriver()->getAdapter()->getPathPrefix();
    $rutaFin = $rutaBaseAlmacenamiento . $imagen;
    if ($imagen == "" || !File::exists($rutaFin))
      $rutaFin = public_path() . "/assets/eah/img/no-disponible.png";
    $archivo = File::get($rutaFin);
    $tipo = File::mimeType($rutaFin);
    $res = Response::make($archivo, 200);
    $res->header("Content-Type", $tipo);
    return $res;
  }

}
