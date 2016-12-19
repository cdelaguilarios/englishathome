<?php

namespace App\Models;

use Carbon\Carbon;
use App\Helpers\Util;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model {

  public $timestamps = false;
  protected $table = "pago";
  protected $fillable = ["motivo", "descripcion", "monto", "rutasImagenesComprobante", "saldoFavor", "saldoFavorUtilizado", "estado"];

  public static function nombreTabla() {
    $modeloPago = new Pago();
    $nombreTabla = $modeloPago->getTable();
    unset($modeloPago);
    return $nombreTabla;
  }

  protected static function obtenerXId($id) {
    return Pago::findOrFail($id);
  }

  protected static function registrar($datos, $estado, $request) {
    $pago = new Pago($datos);
    $pago->saldoFavorUtilizado = (isset($datos["saldoFavor"]) && $datos["saldoFavor"] != "" ? TRUE : NULL);
    $pago->estado = $estado;
    $pago->save();

    if (isset($request)) {
      $rutaImagenesComprobantes = NULL;
      $imagenComprobantePago = $request->file("imagenComprobante");
      if (isset($imagenComprobantePago) && $imagenComprobantePago != "") {
        $rutaImagenesComprobantes = Util::GuardarImagen($pago["id"] . "_icp_", $imagenComprobantePago, FALSE);
      }
      $imagenDocumentoVerificacion = $request->file("imagenDocumentoVerificacion");
      if (isset($imagenDocumentoVerificacion) && $imagenDocumentoVerificacion != "") {
        $rutaImagenesComprobantes .= "," . Util::GuardarImagen($pago["id"] . "_idv_", $imagenDocumentoVerificacion, FALSE);
      }
      $pago->rutasImagenesComprobante .= $rutaImagenesComprobantes;
      $pago->save();
    }
    return $pago;
  }

  protected static function actualizarEstado($id, $estado) {
    $pago = Pago::obtenerXId($id);
    $pago->estado = $estado;
    $pago->save();
  }

  protected static function eliminar($id) {
    $pago = Pago::obtenerXId($id);
    $pago->eliminado = 1;
    $pago->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
    $pago->save();
  }

}
