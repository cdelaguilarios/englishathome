<?php

namespace App\Models;

use File;
use Storage;
use Intervention\Image\ImageManager;

class Archivo {

  public static function obtener($nombre, $esAudio = FALSE, $tipoImagenPerfil = FALSE) {
    try {
      $archivo = Storage::get($nombre);
      $tipo = Storage::mimeType($nombre);
      $tamanho = Storage::size($nombre);
    } catch (\Exception $e) {
      $nombreImgAux = public_path() . "/assets/eah/img/" . (isset($tipoImagenPerfil) ? "perfil-imagen-" . $tipoImagenPerfil . ".png" : "no-disponible.png");
      $archivo = File::get($nombreImgAux);
      $tipo = File::mimeType($nombreImgAux);
    }

    if ($esAudio) {
      $storagePath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
      return response()->file($storagePath . "/" . $nombre, [
                  'Accept-Ranges' => "bytes",
                  'Content-Length' => $tamanho,
                  'Content-Type' => $tipo,
                  'Content-Range' => 'bytes 0-' . ($tamanho - 1) . '/' . $tamanho]);
    } else {
      return response($archivo, 200)->header("Content-Type", $tipo);
    }
  }

  public static function registrar($identificador, $archivo, $reajustar = FALSE) {
    try {
      $nombre = $identificador . time() . "." . $archivo->getClientOriginalExtension();
      $archivoSel = file_get_contents($archivo->getRealPath());
      if ($reajustar) {
        $manager = new ImageManager();
        $imagenMod = $manager->make($archivo->getRealPath());
        $archivoSel = $imagenMod->fit(100, 100)->encode("png", 100);
      }
      Storage::put($nombre, (string) $archivoSel);
      return $nombre;
    } catch (\Exception $e) {
      return NULL;
    }
  }

  public static function eliminar($nombre) {
    Storage::delete($nombre);
  }

  public static function procesarArchivosSubidos($archivosActuales, $datos, $maxCantidadArchivos, $variableNombresArchivos, $variableNombresOriginalesArchivos, $variableNombresArchivosEliminados = "") {
    if (isset($datos[$variableNombresArchivosEliminados])) {
      $nombresArchivosEliminados = explode(",", $datos[$variableNombresArchivosEliminados]);
      for ($i = 0; $i < count($nombresArchivosEliminados); $i++) {
        if (trim($nombresArchivosEliminados[$i]) == "") {
          continue;
        }
        try {
          Archivo::eliminar($nombresArchivosEliminados[$i]);
          $archivosActualesSel = explode(",", $archivosActuales);
          for ($j = 0; $j < count($archivosActualesSel); $j++) {
            if (strpos($archivosActualesSel[$j], $nombresArchivosEliminados[$i] . ":") !== false) {
              $archivosActuales = str_replace($archivosActualesSel[$j] . ",", "", $archivosActuales);
              break;
            }
          }
        } catch (\Exception $e) {
          Log::error($e);
        }
      }
    }
    if (isset($datos[$variableNombresArchivos]) && isset($datos[$variableNombresOriginalesArchivos])) {
      $nombresArchivos = explode(",", $datos[$variableNombresArchivos]);
      $nombresOriginalesArchivos = explode(",", $datos[$variableNombresOriginalesArchivos]);
      for ($i = 0; $i < count($nombresArchivos); $i++) {
        if (count(explode(",", $archivosActuales)) == ($maxCantidadArchivos + 1)) {
          break;
        }
        if (trim($nombresArchivos[$i]) == "") {
          continue;
        }
        $archivosActuales .= $nombresArchivos[$i] . ":" . (array_key_exists($i, $nombresOriginalesArchivos) && $nombresOriginalesArchivos[$i] != "" ? $nombresOriginalesArchivos[$i] : $nombresArchivos[$i]) . ",";
      }
    }
    return $archivosActuales;
  }

}
