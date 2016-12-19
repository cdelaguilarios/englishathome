<?php

namespace App\Helpers;

use Storage;
use Intervention\Image\ImageManager;

class Util {

  public static function GuardarImagen($identificador, $imagen, $reajustar = TRUE) {
    try {
      $manager = new ImageManager();
      $nombreArchivo = $identificador . time() . "." . $imagen->getClientOriginalExtension();
      $imagenMod = $manager->make($imagen->getRealPath());
      if ($reajustar) {
        $imagenMod = $imagenMod->fit(100, 100);
      }
      Storage::disk("local")->put($nombreArchivo, (string) $imagenMod->encode("png", 100));
      return $nombreArchivo;
    } catch (Exception $ex) {
      return NULL;
    }
  }

}
