<?php

namespace App\Models;

use Log;
use File;
use Storage;
use Intervention\Image\ImageManager;

class Archivo {

  public static function obtener($nombre, $sexoEntidad = NULL, $esAudio = FALSE, $esDocumentoPersonal = FALSE) {
    try {
      $archivo = Storage::get($nombre);
      $tipo = Storage::mimeType($nombre);
      $tamanho = Storage::size($nombre);
    } catch (\Exception $e) {
      if (preg_match("#\.(jpg|jpeg|gif|png)$# i", explode("?", $nombre)[0]) || !$esDocumentoPersonal) {
        $nombreImgAux = public_path() . "/assets/eah/img/" . (!is_null($sexoEntidad) ? "perfil-imagen-" . $sexoEntidad . ".png" : "no-disponible.png");
        $archivo = File::get($nombreImgAux);
        $tipo = File::mimeType($nombreImgAux);
      } else {
        return response("", 404);
      }
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
        $archivoSel = $imagenMod->fit(600, 600)->encode("png", 100);
      }
      Storage::put($nombre, (string) $archivoSel);
      return $nombre;
    } catch (\Exception $e) {
      Log::error($e);
      return NULL;
    }
  }

  public static function eliminar($nombre) {
    Storage::delete($nombre);
  }

  public static function verificarExistencia($nombre) {
    try {
      Storage::get($nombre);
    } catch (\Exception $e) {
      Log::error($e);
      return FALSE;
    }
    return TRUE;
  }

  public static function procesarArchivosSubidos($archivosActuales, $datos, $maxCantidadArchivos, $idCampo) {
    $archivosActualesSel = "";

    //Verificando existencia de archivos actuales    
    $listaArchivosActualesSel = explode(",", (!is_null($archivosActuales) ? $archivosActuales : ""));
    for ($i = 0; $i < count($listaArchivosActualesSel); $i++) {
      $datosArchivoActualSel = explode(":", $listaArchivosActualesSel[$i]);
      if (Archivo::verificarExistencia($datosArchivoActualSel[0])) {
        $archivosActualesSel .= $listaArchivosActualesSel[$i] . ",";
      }
    }

    $variableNombresArchivos = "nombresArchivos" . $idCampo;
    $variableNombresOriginalesArchivos = "nombresOriginalesArchivos" . $idCampo;
    $variableNombresArchivosEliminados = "nombresArchivos" . $idCampo . "Eliminados";

    if (isset($datos[$variableNombresArchivosEliminados])) {
      $nombresArchivosEliminados = explode(",", $datos[$variableNombresArchivosEliminados]);
      for ($i = 0; $i < count($nombresArchivosEliminados); $i++) {
        if (trim($nombresArchivosEliminados[$i]) == "") {
          continue;
        }
        try {
          Archivo::eliminar($nombresArchivosEliminados[$i]);
          $datArchivosActualesSel = explode(",", $archivosActualesSel);
          for ($j = 0; $j < count($datArchivosActualesSel); $j++) {
            if (strpos($datArchivosActualesSel[$j], $nombresArchivosEliminados[$i] . ":") !== false) {
              $archivosActualesSel = str_replace($datArchivosActualesSel[$j] . ",", "", $archivosActualesSel);
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
        if (count(explode(",", $archivosActualesSel)) == ($maxCantidadArchivos + 1)) {
          break;
        }
        if (trim($nombresArchivos[$i]) == "") {
          continue;
        }
        $nombreOriginalArchivo = (array_key_exists($i, $nombresOriginalesArchivos) && $nombresOriginalesArchivos[$i] != "" ? str_replace(",", "", $nombresOriginalesArchivos[$i]) : NULL);
        $archivosActualesSel .= $nombresArchivos[$i] . ":" . (isset($nombreOriginalArchivo) && $nombreOriginalArchivo != "" ? $nombreOriginalArchivo : $nombresArchivos[$i]) . ",";
      }
    }
    return $archivosActualesSel;
  }

}
