<?php

namespace App\Models;

use DB;
use Log;
use Auth;
use Crypt;
use Carbon\Carbon;
use App\Helpers\Enum\MotivosPago;
use App\Helpers\Enum\EstadosPago;
use App\Helpers\Enum\TiposEntidad;
use App\Helpers\Enum\EstadosClase;
use App\Helpers\Enum\EstadosAlumno;
use App\Helpers\Enum\MensajesHistorial;
use Illuminate\Database\Eloquent\Model;

class Alumno extends Model {

  public $timestamps = false;
  protected $primaryKey = "idEntidad";
  protected $table = "alumno";
  protected $fillable = [
      "inglesLugarEstudio",
      "inglesPracticaComo",
      "inglesObjetivo",
      "conComputadora",
      "conInternet",
      "conPlumonPizarra",
      "conAmbienteClase",
      "numeroHorasClase",
      "fechaInicioClase",
      "comentarioAdicional",
      "costoXHoraClase"
  ];

  public static function nombreTabla()/* - */ {
    $modeloAlumno = new Alumno();
    $nombreTabla = $modeloAlumno->getTable();
    unset($modeloAlumno);
    return $nombreTabla;
  }

  public static function listarBase($estado = NULL)/* - */ {
    $alumnos = Alumno::leftJoin(Entidad::nombreTabla() . " AS entidad", Alumno::nombreTabla() . ".idEntidad", "=", "entidad.id")
            ->where("entidad.eliminado", 0)
            ->groupBy("entidad.id")
            ->distinct();

    if (isset($estado)) {
      if ($estado == EstadosAlumno::Activo) {
        $alumnos->where(function ($q) use($estado) {
          $q->where("entidad.estado", $estado)->orWhere('entidad.estado', EstadosAlumno::PeriodoConcluido);
        });
      } else {
        $alumnos->where("entidad.estado", $estado);
      }
    }

    return $alumnos;
  }

  public static function listar($estado)/* - */ {
    $alumnos = Alumno::listarBase($estado);

    //Datos de la última clase realizada
    $nombreTablaClase = Clase::nombreTabla();
    $alumnos->leftJoin($nombreTablaClase . " AS ultimaClase", function ($q) use ($nombreTablaClase) {
      $q->on("ultimaClase.idAlumno", "=", "entidad.id")
              ->on("ultimaClase.id", "=", DB::raw("(SELECT id 
                                                        FROM " . $nombreTablaClase . "
                                                        WHERE idAlumno = entidad.id 
                                                          AND estado IN ('" . EstadosClase::Realizada . "','" . EstadosClase::ConfirmadaProfesorAlumno . "')
                                                          AND eliminado=0 
                                                        ORDER BY fechaConfirmacion DESC 
                                                        LIMIT 1)"));
    });

    //Datos del último pago por clases
    $nombreTablaPago = Pago::nombreTabla();
    $alumnos->leftJoin($nombreTablaPago . " AS ultimoPago", function ($q) use ($nombreTablaPago) {
      $nombreTablaPagoAlumno = PagoAlumno::nombreTabla();
      $q->on("ultimoPago.id", "=", DB::raw("(SELECT id 
                                                        FROM " . $nombreTablaPago . "
                                                        WHERE id IN (SELECT idPago FROM " . $nombreTablaPagoAlumno . " WHERE idAlumno = entidad.id)
                                                          AND motivo = '" . MotivosPago::Clases . "'
                                                          AND estado = '" . EstadosPago::Realizado . "'
                                                          AND eliminado = 0
                                                        ORDER BY fechaRegistro DESC 
                                                        LIMIT 1)"));
    });

    //Clases asociadas al último pago por clases
    $alumnos->leftJoin($nombreTablaClase . " AS claseXUltimoPago", function ($q) {
      $nombreTablaPagoClase = PagoClase::nombreTabla();
      $q->on("claseXUltimoPago.id", "IN", DB::raw("(SELECT idClase 
                                                      FROM " . $nombreTablaPagoClase . "
                                                      WHERE idPago = ultimoPago.id)"))
              ->whereIn("claseXUltimoPago.estado", [EstadosClase::ConfirmadaProfesorAlumno, EstadosClase::Realizada])
              ->where("claseXUltimoPago.eliminado", "=", 0);
    });

    //Datos del profesor asociado al último pago por clases
    $alumnos->leftJoin(Entidad::nombreTabla() . " AS profesor", function ($q) {
      $q->on("profesor.id", "=", "ultimoPago.idProfesorClases")
              ->where("profesor.eliminado", "=", 0);
    });
    $alumnos->leftJoin("distrito AS distritoProfesor", function ($q) {
      $q->on("distritoProfesor.codigo", "=", "profesor.codigoUbigeo");
    });

    //Otros datos
    $alumnos->leftJoin(NivelIngles::nombreTabla() . " AS nivelIngles", function ($q) {
      $q->on("nivelIngles.id", "=", DB::raw("(SELECT idNivelIngles 
                                                  FROM " . EntidadNivelIngles::nombreTabla() . " 
                                                  WHERE idEntidad = entidad.id)"))
              ->where("nivelIngles.activo", "=", 1)
              ->where("nivelIngles.eliminado", "=", 0);
    });
    $alumnos->join(EntidadCurso::nombreTabla() . " AS entidadCurso", function ($q) {
      $q->on("entidadCurso.idEntidad", "=", "entidad.id");
    });
    $alumnos->join(Curso::nombreTabla() . " AS curso", function ($q) {
      $q->on("curso.id", "=", "entidadCurso.idCurso");
    });
    $alumnos->leftJoin("distrito AS distritoAlumno", function ($q) {
      $q->on("distritoAlumno.codigo", "=", "entidad.codigoUbigeo");
    });

    $alumnos->select(DB::raw(
                    Alumno::nombreTabla() . ".*, 
                      entidad.*, 
                      distritoAlumno.distrito AS distritoAlumno,          
                      curso.nombre AS curso, 
                      nivelIngles.nombre AS nivelIngles,              
                      profesor.id AS idProfesor, 
                      profesor.nombre AS nombreProfesor, 
                      profesor.apellido AS apellidoProfesor, 
                      distritoProfesor.distrito AS distritoProfesor, 
                      ultimaClase.fechaFin AS ultimaClaseFechaFin, 
                      ultimaClase.fechaConfirmacion AS ultimaClaseFechaConfirmacion, 
                      (CASE WHEN ultimaClase.fechaConfirmacion IS NOT NULL
                        THEN ultimaClase.fechaConfirmacion
                        ELSE ultimaClase.fechaFin
                      END) AS ultimaClaseFecha,
                      ultimoPago.monto AS ultimoPagoMonto,
                      ultimoPago.saldoFavor AS ultimoPagoSaldoFavor,
                      ultimoPago.costoXHoraClase AS ultimoPagoCostoXHoraClase,
                      (CASE WHEN IFNULL(ultimoPago.costoXHoraClase, 0) > 0 
			THEN ((IFNULL(ultimoPago.monto, 0) - IFNULL(ultimoPago.saldoFavor, 0)) * 3600 / (ultimoPago.costoXHoraClase))
                        ELSE 0
                      END) AS ultimoPagoDuracionTotalXClases,
                      SUM(claseXUltimoPago.duracion) AS ultimoPagoDuracionTotalXClasesRealizadas,
                      (IFNULL(ultimoPago.monto, 0) - IFNULL(ultimoPago.saldoFavor, 0)) AS ultimoPagoMontoTotalXClases,
                      ((SUM(claseXUltimoPago.duracion)/3600) * (SUM(claseXUltimoPago.costoHora)/COUNT(*))) AS ultimoPagoMontoTotalXClasesRealizadas")
    );

    return DB::table(DB::raw("({$alumnos->toSql()}) AS T"))
                    ->mergeBindings($alumnos->getQuery())
                    ->select(DB::raw(
                                    "T.*,
                                      (CASE WHEN ultimoPagoDuracionTotalXClases > ultimoPagoDuracionTotalXClasesRealizadas 
                                        THEN ultimoPagoDuracionTotalXClases - ultimoPagoDuracionTotalXClasesRealizadas
                                        ELSE 0
                                      END) AS ultimoPagoDuracionTotalXClasesPendientes,
                                      (CASE WHEN ultimoPagoDuracionTotalXClases < ultimoPagoDuracionTotalXClasesRealizadas 
                                        THEN ultimoPagoDuracionTotalXClasesRealizadas - ultimoPagoDuracionTotalXClases
                                        ELSE 0
                                      END) AS ultimoPagoDuracionTotalXClasesNoPagadas,
                                      (ultimoPagoDuracionTotalXClasesRealizadas*100/ultimoPagoDuracionTotalXClases) AS ultimoPagoPorcentajeAvanceXClases,
                                      (CASE WHEN ultimoPagoMontoTotalXClases > ultimoPagoMontoTotalXClasesRealizadas 
                                        THEN ultimoPagoMontoTotalXClases - ultimoPagoMontoTotalXClasesRealizadas
                                        ELSE 0
                                      END) AS ultimoPagoMontoTotalXClasesPendientes,
                                      (CASE WHEN ultimoPagoMontoTotalXClases < ultimoPagoMontoTotalXClasesRealizadas 
                                        THEN ultimoPagoMontoTotalXClasesRealizadas - ultimoPagoMontoTotalXClases
                                        ELSE 0
                                      END) AS ultimoPagoMontoTotalXClasesNoPagadas")
    );
  }

  public static function listarBusqueda($terminoBus = NULL)/* - */ {
    $alumnos = Alumno::listarBase()->select("entidad.id", DB::raw('CONCAT(entidad.nombre, " ", entidad.apellido) AS nombreCompleto'));
    if (isset($terminoBus)) {
      $alumnos->whereRaw('CONCAT(entidad.nombre, " ", entidad.apellido) like ?', ["%{$terminoBus}%"]);
    }
    return $alumnos->lists("nombreCompleto", "entidad.id");
  }

  public static function obtenerXId($id, $simple = FALSE)/* - */ {
    $alumno = Alumno::listarBase()->where("entidad.id", $id)->firstOrFail();

    if (!$simple) {
      $alumno->horario = Horario::obtenerJsonXIdEntidad($id);

      $entidadCurso = EntidadCurso::obtenerXIdEntidad($id);
      $alumno->idCurso = (isset($entidadCurso) ? $entidadCurso->idCurso : NULL);
      $alumno->ultimoPago = PagoAlumno::obtenerUltimoXClases($id); //TODO: el último pago por clases debe ser el último que tiene clases pendientes
      $alumno->direccionUbicacion = Ubigeo::obtenerTextoUbigeo($alumno->codigoUbigeo);
      $alumno->interesadoRelacionado = Interesado::obtenerXIdAlumno($id);

      $entidadNivelIngles = EntidadNivelIngles::obtenerXIdEntidad($id);
      $alumno->idNivelIngles = (isset($entidadNivelIngles) ? $entidadNivelIngles->idNivelIngles : NULL);

      $alumno->numeroPeriodos = Clase::totalPeriodosXIdAlumno($id);

      $datosIdsAntSig = Entidad::ObtenerIdsAnteriorSiguienteXEntidad(TiposEntidad::Alumno, $alumno);
      $alumno->idAlumnoAnterior = $datosIdsAntSig["idEntidadAnterior"];
      $alumno->idAlumnoSiguiente = $datosIdsAntSig["idEntidadSiguiente"];

      //TODO: simplificar ya que en la función Profesor::obtenerAlumno() es muy parecida
      $ultimoPago = NULL;
      $duracionTotalXClases = 0;
      $duracionTotalXClasesRealizadas = 0;
      $duracionTotalXClasesPendientes = 0;
      $pagos = Pago::listarNUEVO()->where("pagoAlumno.idAlumno", $id);
      if ($pagos->count() > 0) {
        $ultimoPago = $pagos->first();
        $pagosXClasesVigentes = Pago::listarNUEVO(TRUE)->where("pagoAlumno.idAlumno", $id);

        if ($pagosXClasesVigentes->count() > 0) {
          $ultimoPago = $pagosXClasesVigentes->first();
          $preHorasPagadas = ((float) $ultimoPago->monto / (float) $ultimoPago->costoXHoraClase);
          $horasPagadas = ($preHorasPagadas - fmod($preHorasPagadas, 0.5));

          $duracionTotalXClases += $horasPagadas * 3600;
          $duracionTotalXClasesRealizadas += (isset($ultimoPago->duracionXClasesRealizadas) ? $ultimoPago->duracionXClasesRealizadas : 0);
          $duracionTotalXClasesPendientes += ($duracionTotalXClases > $duracionTotalXClasesRealizadas ? $duracionTotalXClases - $duracionTotalXClasesRealizadas : 0);
        }
      }
      $alumno->duracionTotalXClasesPendientes = $duracionTotalXClasesPendientes;
      //-----
    }
    return $alumno;
  }

  public static function registrar($req)/* - */ {
    $datos = $req->all();
    if (isset($datos["fechaNacimiento"])) {
      $datos["fechaNacimiento"] = Carbon::createFromFormat("d/m/Y H:i:s", $datos["fechaNacimiento"] . " 00:00:00")->toDateTimeString();
    }
    $datos["fechaInicioClase"] = Carbon::createFromFormat("d/m/Y H:i:s", $datos["fechaInicioClase"] . " 00:00:00")->toDateTimeString();

    $idEntidad = Entidad::registrar($datos, TiposEntidad::Alumno, EstadosAlumno::PorConfirmar);
    Entidad::registrarActualizarImagenPerfil($idEntidad, $req->file("imagenPerfil"));
    EntidadNivelIngles::registrarActualizar($idEntidad, $datos["idNivelIngles"]);
    EntidadCurso::registrarActualizar($idEntidad, $datos["idCurso"]);
    Horario::registrarActualizar($idEntidad, $datos["horario"]);

    $alumno = new Alumno($datos);
    $alumno->idEntidad = $idEntidad;
    $alumno->save();

    Historial::registrar([
        "idEntidades" => [$idEntidad, (Auth::guest() ? NULL : Auth::user()->idEntidad)],
        "titulo" => (Auth::guest() ? MensajesHistorial::TituloAlumnoRegistro : MensajesHistorial::TituloAlumnoRegistroXUsuario),
        "enviarCorreo" => (Auth::guest() ? 1 : 0),
        "mensaje" => ""
    ]);
    return $idEntidad;
  }

  public static function registrarExterno($req)/* - */ {
    $datos = $req->all();
    $interesado = Interesado::obtenerXId(Crypt::decrypt($datos["codigoVerificacion"]), TRUE);
    if ($interesado->idEntidad == $datos["idInteresado"] && Interesado::obtenerIdAlumno($datos["idInteresado"]) == 0) {
      $idEntidad = Alumno::registrar($req);
      Interesado::registrarAlumno($datos["idInteresado"], $idEntidad);
    }
  }

  public static function actualizar($id, $req)/* - */ {
    $datos = $req->all();
    if (isset($datos["fechaNacimiento"])) {
      $datos["fechaNacimiento"] = Carbon::createFromFormat("d/m/Y H:i:s", $datos["fechaNacimiento"] . " 00:00:00")->toDateTimeString();
    }
    $datos["fechaInicioClase"] = Carbon::createFromFormat("d/m/Y H:i:s", $datos["fechaInicioClase"] . " 00:00:00")->toDateTimeString();

    Entidad::actualizar($id, $datos, TiposEntidad::Alumno, $datos["estado"]);
    EntidadNivelIngles::registrarActualizar($id, $datos["idNivelIngles"]);
    Entidad::registrarActualizarImagenPerfil($id, $req->file("imagenPerfil"));
    EntidadCurso::registrarActualizar($id, $datos["idCurso"]);
    Horario::registrarActualizar($id, $datos["horario"]);
    $alumno = Alumno::obtenerXId($id, TRUE);
    $alumno->update($datos);

    if (Usuario::verificarExistencia($id)) {
      $usuario = Usuario::obtenerXId($id);
      $usuario->email = $datos["correoElectronico"];
      $usuario->update();
    }
  }

  public static function actualizarEstado($id, $estado)/* - */ {
    Alumno::obtenerXId($id, TRUE);
    Entidad::actualizarEstado($id, $estado);
  }

  public static function actualizarHorario($id, $horario)/* - */ {
    Alumno::obtenerXId($id, TRUE);
    Horario::registrarActualizar($id, $horario);
  }

  public static function eliminar($id)/* - */ {
    Alumno::obtenerXId($id, TRUE);
    Entidad::eliminar($id);
    Clase::eliminadXIdAdlumno($id); //TODO: Todas las funciones de clase y pagos se tienen que revisar bien
  }

  public static function sincronizarEstados() {
    //TODO: corregir
    /* Clase::sincronizarEstados();
      $alumnos = Alumno::listarBase()
      ->whereIn("entidad.id", Clase::where("eliminado", 0)->groupBy("idAlumno")->lists("idAlumno"))//TODO:Cambiar
      ->whereNotIn("entidad.id", Clase::listarXEstados([EstadosClase::Programada, EstadosClase::PendienteConfirmar])->groupBy("idAlumno")->lists("idAlumno"))
      ->whereNotIn("entidad.estado", [EstadosAlumno::PorConfirmar, EstadosAlumno::StandBy, EstadosAlumno::Inactivo])
      ->get();
      foreach ($alumnos as $alumno) {
      Alumno::actualizarEstado($alumno->idEntidad, EstadosAlumno::CuotaProgramada);
      } */
  }

  public static function verificarExistencia($id)/* - */ {
    try {
      Alumno::obtenerXId($id, TRUE);
    } catch (\Exception $e) {
      Log::error($e);
      return FALSE;
    }
    return TRUE;
  }

  // <editor-fold desc="TODO: ELIMINAR">
  //REPORTE
  public static function listarCampos() {
    return [
        "inglesLugarEstudio" => ["titulo" => "Ingles - Lugar de estudio"],
        "inglesPracticaComo" => ["titulo" => "Ingles - Como practica"],
        "inglesObjetivo" => ["titulo" => "Ingles - Objetivo"],
        "conComputadora" => ["titulo" => "Con computadora"],
        "conInternet" => ["titulo" => "Con internet"],
        "conPlumonPizarra" => ["titulo" => "Con plumon y pizarra"],
        "conAmbienteClase" => ["titulo" => "Con ambiente adecuado para clases"],
        "numeroHorasClase" => ["titulo" => "Número de horas por clase"],
        "fechaInicioClase" => ["titulo" => "Fecha de inicio de clases"],
        "comentarioAdicional" => ["titulo" => "Comentario adicional"],
        "costoXHoraClase" => ["titulo" => "Costo por hora de clase"]
    ];
  }

  public static function listarEntidadesRelacionadas() {
    return [];
  }

  // </editor-fold>
}
