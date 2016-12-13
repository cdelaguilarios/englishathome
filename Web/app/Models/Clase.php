<?php

namespace App\Models;

use DB;
use Auth;
use Carbon\Carbon;
use App\Models\Horario;
use App\Models\Historial;
use App\Helpers\Enum\EstadosClase;
use App\Helpers\Enum\TiposHistorial;
use App\Helpers\Enum\MensajesHistorial;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Enum\TiposCancelacionClase;

class Clase extends Model {

    public $timestamps = false;
    protected $table = 'clase';
    protected $fillable = ['idAlumno', 'idProfesor', 'numeroPeriodo', 'duracion', 'costoHora', 'costoHoraProfesor', 'fechaInicio', 'fechaFin', 'fechaCancelacion', 'estado'];

    public static function nombreTabla() {
        $modeloClase = new Clase();
        $nombreTabla = $modeloClase->getTable();
        unset($modeloClase);
        return $nombreTabla;
    }

    protected static function obtenerXId($idAlumno, $id) {
        $nombreTabla = Clase::nombreTabla();
        return Clase::select($nombreTabla . '.*', 'entidadProfesor.nombre AS nombreProfesor', 'entidadProfesor.apellido AS apellidoProfesor', 'historial.id AS idHistorial')
                        ->leftJoin(Entidad::nombreTabla() . ' as entidadProfesor', $nombreTabla . '.idProfesor', '=', 'entidadProfesor.id')
                        ->leftJoin(Historial::NombreTabla() . ' as historial', $nombreTabla . '.id', '=', 'historial.idClase')
                        ->where($nombreTabla . '.idAlumno', $idAlumno)
                        ->where($nombreTabla . '.id', $id)
                        ->where(function ($q) {
                            $q->whereNull('historial.id')->orWhere('historial.enviarCorreo', 1);
                        })
                        ->orderBy($nombreTabla . '.fechaInicio', 'ASC')
                        ->firstOrFail();
    }

    protected static function listar($idAlumno, $numeroPeriodo) {
        $nombreTabla = Clase::nombreTabla();
        return Clase::select($nombreTabla . '.*', 'entidadProfesor.nombre AS nombreProfesor', 'entidadProfesor.apellido AS apellidoProfesor', 'historial.id AS idHistorial')
                        ->leftJoin(Entidad::nombreTabla() . ' as entidadProfesor', $nombreTabla . '.idProfesor', '=', 'entidadProfesor.id')
                        ->leftJoin(Historial::NombreTabla() . ' as historial', $nombreTabla . '.id', '=', 'historial.idClase')
                        ->where($nombreTabla . '.idAlumno', $idAlumno)
                        ->where($nombreTabla . '.eliminado', 0)
                        ->where($nombreTabla . '.numeroPeriodo', $numeroPeriodo)
                        ->where(function ($q) {
                            $q->whereNull('historial.id')->orWhere('historial.enviarCorreo', 1);
                        })
                        ->orderBy($nombreTabla . '.fechaInicio', 'ASC')
                        ->get();
    }

    protected static function totalPeriodos($idAlumno) {
        return count(Clase::groupBy('numeroPeriodo')->selectRaw('count(*) as total')->where('idAlumno', $idAlumno)->get()->toArray());
    }

    protected static function listarPeriodos($idAlumno) {
        return Clase::select('numeroPeriodo', DB::raw('min(fechaInicio) AS fechaInicio, max(fechaFin) AS fechaFin, sum(duracion) AS horasTotal'))->where('idAlumno', $idAlumno)->where('eliminado', 0)->groupBy('numeroPeriodo');
    }

    protected static function listarIdsEntidadesXRangoFecha($fechaInicio, $fechaFin, $idsProfesores = FALSE) {
        $clases = Clase::where('fechaInicio', '<=', $fechaInicio)->where('fechaFin', '>=', $fechaFin)->lists('idProfesor');
        return ($idsProfesores ? $clases->lists('idProfesor') : $clases->lists('idAlumno'));
    }

    protected static function generarXDatosPago($idAlumno, $datos) {
        $duracionTotalSeg = 0;
        $clasesGeneradas = [];

        $preHorasPagadas = ((float) $datos["monto"] / (float) $datos["costoHoraClase"]);
        $horasPagadas = ($preHorasPagadas - fmod($preHorasPagadas, 0.5));
        $fechaInicioClase = Carbon::createFromFormat('d/m/Y H:i:s', $datos["fechaInicioClases"] . " 00:00:00");

        while ($duracionTotalSeg < ($horasPagadas * 3600)) {
            foreach (Horario::obtener($idAlumno) as $datHorarioAlumno) {
                if ($fechaInicioClase->dayOfWeek == $datHorarioAlumno->numeroDiaSemana) {
                    $fechaInicio = Carbon::createFromFormat('d/m/Y H:i:s', $fechaInicioClase->format('d/m/Y') . " " . $datHorarioAlumno->horaInicio);
                    $preFechaFin = Carbon::createFromFormat('d/m/Y H:i:s', $fechaInicioClase->format('d/m/Y') . " " . $datHorarioAlumno->horaFin);

                    $tiempoAdicionalSeg = (($duracionTotalSeg + $preFechaFin->diffInSeconds($fechaInicio)) - ($horasPagadas * 3600));
                    $fechaFin = (($tiempoAdicionalSeg > 0) ? $preFechaFin->subSeconds($tiempoAdicionalSeg) : $preFechaFin);
                    $duracion = $fechaFin->diffInSeconds($fechaInicio);
                    $clasesGeneradas[] = ["fechaInicio" => $fechaInicio, "fechaFin" => $fechaFin, "duracion" => $duracion, "tiempoAdicional" => ($tiempoAdicionalSeg > 0 ? $tiempoAdicionalSeg : 0)];

                    $duracionTotalSeg += $duracion;
                }
            }
            $fechaInicioClase = $fechaInicioClase->addDay();
        }
        return $clasesGeneradas + ["montoRestante" => ($datos["monto"] - ($horasPagadas * (float) $datos["costoHoraClase"]))];
    }

    protected static function registrar($idAlumno, $datos) {
        $fechaInicio = Carbon::createFromFormat("d/m/Y H:i:s", $datos["fecha"] . " 00:00:00")->addSeconds($datos["horaInicio"]);
        $fechaFin = clone $fechaInicio;
        $fechaFin->addSeconds($datos["duracion"]);

        $clase = new Clase([
            'idAlumno' => $idAlumno,
            'idProfesor' => $datos["idDocente"],
            'numeroPeriodo' => $datos["numeroPeriodo"],
            'duracion' => $datos["duracion"],
            'costoHora' => $datos["costoHora"],
            'costoHoraProfesor' => $datos["costoHoraDocente"],
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'estado' => EstadosClase::Programada
        ]);
        $clase->save();

        if ($datos["notificar"] == 1) {
            $tituloHistorial = str_replace(["[DIAS]"], ["1 día"], (!is_null($datos["idDocente"]) ? MensajesHistorial::TituloCorreoAlumnoClase : MensajesHistorial::TituloCorreoAlumnoClaseSinProfesor));
            $mensajeHistorial = str_replace(["[FECHA]", "[PERIODO]", "[DURACION]"], [$fechaInicio->format('d/m/Y H:i:s'), $datos["numeroPeriodo"], gmdate("H:i", $datos["duracion"])], (!is_null($datos["idDocente"]) ? MensajesHistorial::MensajeCorreoAlumnoClase : MensajesHistorial::MensajeCorreoAlumnoClaseSinProfesor));
            Historial::Registrar([$idAlumno, $datos["idDocente"], Auth::user()->idEntidad], $tituloHistorial, $mensajeHistorial, NULL, TRUE, FALSE, NULL, $clase["id"], $fechaInicio->subDays(1), TiposHistorial::Correo);
        }
    }

    protected static function registrarXDatosPago($idAlumno, $idPago, $datos) {
        $datosClases = Clase::generarXDatosPago($idAlumno, $datos);
        $datosNotificacionClases = json_decode($datos["datosNotificacionClases"]);

        for ($i = 0; $i < count($datosClases); $i++) {
            if (!isset($datosClases[$i]["duracion"])) {
                continue;
            }
            $clase = new Clase([
                'idAlumno' => $idAlumno,
                'idProfesor' => $datos["idDocente"],
                'numeroPeriodo' => $datos["periodoClases"],
                'duracion' => $datosClases[$i]["duracion"],
                'costoHora' => $datos["costoHoraClase"],
                'costoHoraProfesor' => $datos["costoHoraDocente"],
                'fechaInicio' => $datosClases[$i]["fechaInicio"],
                'fechaFin' => $datosClases[$i]["fechaFin"],
                'estado' => EstadosClase::Programada
            ]);
            $clase->save();

            PagoClase::registrar($idPago, $clase["id"]);
            if ($datosNotificacionClases[$i]->notificar != "" && $datosNotificacionClases[$i]->notificar) {
                $tituloHistorial = str_replace(["[DIAS]"], ["1 día"], (!is_null($datos["idDocente"]) ? MensajesHistorial::TituloCorreoAlumnoClase : MensajesHistorial::TituloCorreoAlumnoClaseSinProfesor));
                $mensajeHistorial = str_replace(["[FECHA]", "[PERIODO]", "[DURACION]"], [$datosClases[$i]["fechaInicio"]->format('d/m/Y H:i:s'), $datos["periodoClases"], gmdate("H:i", $datosClases[$i]["duracion"])], (!is_null($datos["idDocente"]) ? MensajesHistorial::MensajeCorreoAlumnoClase : MensajesHistorial::MensajeCorreoAlumnoClaseSinProfesor));
                Historial::Registrar([$idAlumno, $datos["idDocente"], Auth::user()->idEntidad], $tituloHistorial, $mensajeHistorial, NULL, TRUE, FALSE, NULL, $clase["id"], $datosClases[$i]["fechaInicio"]->subDays(1), TiposHistorial::Correo);
            }
        }
    }

    protected static function cancelar($idAlumno, $datos) {
        $claseCancelada = Clase::obtenerXId($idAlumno, $datos["idClase"]);
        if ($claseCancelada !== EstadosClase::Programada) {
            $claseCancelada->tipoCancelacion = $datos["tipoCancelacion"];
            $claseCancelada->fechaCancelacion = Carbon::now()->toDateTimeString();
            $claseCancelada->estado = EstadosClase::Cancelada;
            $claseCancelada->save();

            if ($datos["tipoCancelacion"] == TiposCancelacionClase::CancelacionAlumno && isset($datos["idProfesor"]) && isset($datos["pagoProfesor"])) {
                PagoProfesor::registrarXDatosClaseCancelada($datos["idProfesor"], $datos["idClase"], $datos["pagoProfesor"]);
            }
            if (($datos["tipoCancelacion"] == TiposCancelacionClase::CancelacionAlumno && $datos["reprogramarCancelacionAlumno"] == 1) || ($datos["tipoCancelacion"] == TiposCancelacionClase::CancelacionProfesor && $datos["reprogramarCancelacionProfesor"] == 1)) {
                $fechaInicio = Carbon::createFromFormat("d/m/Y H:i:s", $datos["fecha"] . " 00:00:00")->addSeconds($datos["horaInicio"]);
                $fechaFin = clone $fechaInicio;
                $fechaFin->addSeconds($datos["duracion"]);

                $clase = new Clase([
                    'idAlumno' => $idAlumno,
                    'idProfesor' => $datos["idDocente"],
                    'numeroPeriodo' => $claseCancelada["numeroPeriodo"],
                    'duracion' => $datos["duracion"],
                    'costoHora' => $claseCancelada["costoHora"],
                    'costoHoraProfesor' => $datos["costoHoraDocente"],
                    'fechaInicio' => $fechaInicio,
                    'fechaFin' => $fechaFin,
                    'estado' => EstadosClase::Programada,
                    'idClaseCancelada' => $claseCancelada["id"]
                ]);
                $clase->save();

                if (isset($claseCancelada["idHistorial"])) {
                    $tituloHistorial = str_replace(["[DIAS]"], ["1 día"], (!is_null($datos["idDocente"]) ? MensajesHistorial::TituloCorreoAlumnoClase : MensajesHistorial::TituloCorreoAlumnoClaseSinProfesor));
                    $mensajeHistorial = str_replace(["[FECHA]", "[PERIODO]", "[DURACION]"], [$fechaInicio->format('d/m/Y H:i:s'), $claseCancelada["numeroPeriodo"], gmdate("H:i", $datos["duracion"])], (!is_null($datos["idDocente"]) ? MensajesHistorial::MensajeCorreoAlumnoClase : MensajesHistorial::MensajeCorreoAlumnoClaseSinProfesor));
                    Historial::Registrar([$idAlumno, $datos["idDocente"], Auth::user()->idEntidad], $tituloHistorial, $mensajeHistorial, NULL, TRUE, FALSE, NULL, $clase["id"], $fechaInicio->subDays(1), TiposHistorial::Correo);
                }
            }
        }
    }

    protected static function eliminar($idAlumno, $id) {
        $clase = Clase::obtenerXId($idAlumno, $id);
        $clase->eliminado = 1;
        $clase->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
        $clase->save();
    }

    protected static function eliminarXPago($idAlumno, $idPago) {
        $clases = Clase::where('idAlumno', $idAlumno)->where('idPago', $idPago)->get();
        foreach ($clases as $clase) {
            Clase::eliminar($idAlumno, $clase->id);
        }
    }

    protected static function verificarExistencia($idAlumno, $id) {
        try {
            Clase::obtenerXId($idAlumno, $id);
        } catch (Exception $ex) {
            return FALSE;
        }
        return TRUE;
    }

}
