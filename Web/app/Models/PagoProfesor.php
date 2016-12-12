<?php

namespace App\Models;

use App\Helpers\Enum\EstadosPago;
use App\Helpers\Enum\TiposHistorial;
use App\Helpers\Enum\MensajesHistorial;
use Illuminate\Database\Eloquent\Model;

class PagoProfesor extends Model {

    public $timestamps = false;
    protected $table = 'pagoProfesor';
    protected $fillable = ['idProfesor', 'idPago'];

    public static function NombreTabla() {
        $modeloPagoProfesor = new PagoProfesor();
        $nombreTabla = $modeloPagoProfesor->getTable();
        unset($modeloPagoProfesor);
        return $nombreTabla;
    }

    protected static function registrarXDatosClaseCancelada($idProfesor, $idClaseCancelada, $monto) {
        $datos = ["motivo" => "Pago por clase cancelada.", "monto" => $monto];
        $datosPago = Pago::registrar($datos, EstadosPago::Pendiente);
        $pagoProfesor = new PagoProfesor([
            'idPago' => $datosPago["id"],
            'idProfesor' => $idProfesor
        ]);
        $pagoProfesor->save();
        PagoClase::registrar($datosPago["id"], $idClaseCancelada);
        $mensajeHistorial = str_replace(["[MOTIVO]", "[DESCRIPCION]", "[MONTO]"], [$datos["motivo"], "", number_format((float) ($datos["monto"]), 2, '.', '')], MensajesHistorial::MensajeProfesorRegistroPago);
        Historial::Registrar([$idProfesor, Auth::user()->idEntidad], MensajesHistorial::TituloProfesorRegistroPago, $mensajeHistorial, NULL, FALSE, TRUE, $datosPago["id"], NULL, NULL, TiposHistorial::Pago);
    }

}
