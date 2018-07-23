<?php

namespace App\Http\Controllers;

use Log;
use Input;
use App\Models\Archivo;
use App\Http\Requests\ArchivoRequest;

class ArchivoController extends Controller {

  public function obtener($nombre) {
    $esAudio = Input::get("audio");
    $tipoImagenPerfil = Input::get("tip");
    $esDocumentoPersonal = Input::get("docPer");
    return Archivo::obtener($nombre, (isset($esAudio) && ((int) $esAudio) == 1), (isset($tipoImagenPerfil) ? $tipoImagenPerfil : NULL), (isset($esDocumentoPersonal) && ((int) $esDocumentoPersonal) == 1));
  }

  public function registrar(ArchivoRequest $req) {
    $datos = $req->all();
    $archivo = $req->file('archivo');
    $nombre = Archivo::registrar("arch_" . rand(1000000, 9999999) . "_", $archivo);
    return response()->json(["idElemento" => $datos["idElemento"], "nombre" => $nombre, "nombreOriginal" => $archivo->getClientOriginalName()], 200);
  }

  public function eliminar(ArchivoRequest $req) {
    try {
      $datos = $req->all();
      Archivo::eliminar($datos["nombre"]);
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(['mensaje' => 'No se pudo eliminar el archivo seleccionado.'], 400);
    }
    return response()->json(['mensaje' => 'Eliminación exitosa'], 200);
  }

}
