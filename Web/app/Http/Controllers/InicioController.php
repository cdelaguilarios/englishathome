<?php

namespace App\Http\Controllers;

use File;
use Storage;
use Response;

class InicioController extends Controller {

  public function inicio() {
    return redirect(route("usuarios"));
  }

  public function obtenerImagen($rutaImagen) {
    $rutaBaseAlmacenamiento = Storage::disk("local")->getDriver()->getAdapter()->getPathPrefix();
    $rutaFin = $rutaBaseAlmacenamiento . $rutaImagen;

    if ($rutaImagen == "" || !File::exists($rutaFin)) {
      $rutaFin = public_path() . "/assets/eah/img/no-disponible.png";
    }
    $archivo = File::get($rutaFin);
    $tipo = File::mimeType($rutaFin);
    $response = Response::make($archivo, 200);
    $response->header("Content-Type", $tipo);
    return $response;
  }

}
