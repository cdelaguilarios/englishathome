<?php

namespace App\Http\Controllers;

use Log;
use Input;
use App\Models\Archivo;
use App\Http\Requests\ArchivoRequest;

class ArchivoController extends Controller/* - */ {

  public function registrar(ArchivoRequest $req)/* - */ {
    $datos = $req->all();
    $archivo = $req->file('archivo');
    $nombre = Archivo::registrar("arch_" . rand(1000000, 9999999) . "_", $archivo);
    return response()->json(["idElemento" => $datos["idElemento"], "nombre" => $nombre, "nombreOriginal" => $archivo->getClientOriginalName()], 200);
  }

  public function obtener($nombre)/* - */ {
    $esAudio = Input::get("esAudio");
    $sexoEntidad = Input::get("sexoEntidad");
    $esDocumentoPersonal = Input::get("esDocumentoPersonal");
    return Archivo::obtener($nombre, (isset($sexoEntidad) ? $sexoEntidad : NULL), (isset($esAudio) && ((int) $esAudio) == 1), (isset($esDocumentoPersonal) && ((int) $esDocumentoPersonal) == 1));
  }

  public function eliminar(ArchivoRequest $req)/* - */ {
    try {
      $datos = $req->all();
      Archivo::eliminar($datos["nombre"]);
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(['mensaje' => 'No se pudo eliminar el archivo seleccionado.'], 500);
    }
    return response()->json(['mensaje' => 'Eliminación exitosa'], 200);
  }

}
