<?php

namespace App\Models;

use Carbon\Carbon;
use App\Helpers\Util;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model {

    public $timestamps = false;
    protected $table = "pago";
    protected $fillable = ["motivo", "descripcion", "monto", "rutaImagenComprobante", "saldoFavor", "saldoFavorUtilizado", "estado"];

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
            $imagenComprobantePago = $request->file("imagenComprobante");
            $rutaImagenComprobante = NULL;
            if (isset($imagenComprobantePago) && $imagenComprobantePago != "") {
                $rutaImagenComprobante = Util::GuardarImagen($pago["id"] . "_icp_", $imagenComprobantePago, FALSE);
                $pago->rutaImagenComprobante = $rutaImagenComprobante;
                $pago->save();
            }
        }
        return $pago;
    }

    protected static function eliminar($id) {
        $pago = Pago::obtenerXId($id);
        $pago->eliminado = 1;
        $pago->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
        $pago->save();
    }

}
