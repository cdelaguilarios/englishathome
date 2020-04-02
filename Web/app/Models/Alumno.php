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
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Enum\MensajesNotificacion;

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
      "comentarioAdicional",
      "fechaInicioClase",
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
    $nombreTablaAlumno = Alumno::nombreTabla();
    $nombreTablaClase = Clase::nombreTabla();
    $nombreTablaPago = Pago::nombreTabla();
    $nombreTablaPagoClase = PagoClase::nombreTabla();

    $alumnos = Alumno::listarBase($estado);

    //Datos del profesor actual
    $alumnos->leftJoin(Entidad::nombreTabla() . " AS profesor", function ($q) use ($nombreTablaAlumno) {
      $q->on("profesor.id", "=", $nombreTablaAlumno . ".idProfesorActual")
              ->where("profesor.eliminado", "=", 0);
    });
    $alumnos->leftJoin("distrito AS distritoProfesor", function ($q) {
      $q->on("distritoProfesor.codigo", "=", "profesor.codigoUbigeo");
    });

    //Datos de la última clase realizada
    $alumnos->leftJoin($nombreTablaClase . " AS ultimaClase", function ($q) use ($nombreTablaClase) {
      $q->on("ultimaClase.idAlumno", "=", "entidad.id")
              ->on("ultimaClase.id", "=", DB::raw("(SELECT id 
                                                        FROM " . $nombreTablaClase . "
                                                        WHERE idAlumno = entidad.id 
                                                          AND estado IN ('" . EstadosClase::ConfirmadaProfesor . "','" . EstadosClase::ConfirmadaProfesorAlumno . "','" . EstadosClase::Realizada . "')
                                                          AND eliminado=0 
                                                        ORDER BY fechaConfirmacion DESC 
                                                        LIMIT 1)"));
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


    //Datos de pagos del alumno
    $alumnos->leftJoin($nombreTablaPago . " AS pago", function ($q) {
      $nombreTablaPagoAlumno = PagoAlumno::nombreTabla();
      $q->on("pago.id", "IN", DB::raw("(SELECT idPago 
                                                    FROM " . $nombreTablaPagoAlumno . "
                                                    WHERE idAlumno = entidad.id)"))
              ->where("pago.motivo", "=", MotivosPago::Clases)
              ->whereIn("pago.estado", [EstadosPago::Consumido, EstadosPago::Realizado]);
    });
    $queryDuracionTotalXClasesRealizadasGlobal = "(SELECT SUM(duracionCubierta) 
                                                        FROM " . $nombreTablaPagoClase . " 
                                                        WHERE idPago = pago.id
                                                          AND idClase IN (SELECT id 
                                                                            FROM " . $nombreTablaClase . " 
                                                                            WHERE estado IN ('" . EstadosClase::ConfirmadaProfesor . "', '" . EstadosClase::ConfirmadaProfesorAlumno . "', '" . EstadosClase::Realizada . "')
                                                                              AND eliminado = 0))";

    //Datos de pagos de la bolsa de horas actual del alumno
    $alumnos->leftJoin($nombreTablaPago . " AS pagoXBolsaHoras", function ($q) {
      $nombreTablaAlumnoBolsaHoras = AlumnoBolsaHoras::nombreTabla();
      $q->on("pagoXBolsaHoras.id", "IN", DB::raw("(SELECT idPago 
                                                    FROM " . $nombreTablaAlumnoBolsaHoras . "
                                                    WHERE idAlumno = entidad.id)"))
              ->on("pagoXBolsaHoras.id", "=", "pago.id")
              ->where("pagoXBolsaHoras.estado", "=", EstadosPago::Realizado);
    });
    $queryDuracionTotalXClasesRealizadas = "(SELECT SUM(duracionCubierta) 
                                              FROM " . $nombreTablaPagoClase . " 
                                              WHERE idPago = pagoXBolsaHoras.id
                                                AND idClase IN (SELECT id 
                                                                  FROM " . $nombreTablaClase . " 
                                                                  WHERE estado IN ('" . EstadosClase::ConfirmadaProfesor . "', '" . EstadosClase::ConfirmadaProfesorAlumno . "', '" . EstadosClase::Realizada . "')
                                                                    AND eliminado = 0))";

    $alumnos->select(DB::raw(
                    Alumno::nombreTabla() . ".*, 
                      entidad.*, 
                      distritoAlumno.distrito AS distritoAlumno,          
                      curso.nombre AS curso, 
                      nivelIngles.nombre AS nivelIngles,              
                      profesor.id AS idProfesor, 
                      profesor.nombre AS nombreProfesor, 
                      profesor.apellido AS apellidoProfesor,        
                      profesor.telefono AS telefonoProfesor, 
                      distritoProfesor.distrito AS distritoProfesor, 
                      (CASE WHEN ultimaClase.fechaConfirmacion IS NOT NULL
                        THEN ultimaClase.fechaConfirmacion
                        ELSE ultimaClase.fechaFin
                      END) AS ultimaClaseFecha," .
                    // <editor-fold desc="Datos globales">
                    "SUM(CASE WHEN IFNULL(pago.costoXHoraClase, 0) > 0 
			THEN ((IFNULL(pago.monto, 0) - IFNULL(pago.saldoFavor, 0)) * 3600 / (pago.costoXHoraClase))
                        ELSE 0
                      END) AS duracionTotalXClasesGlobal,
                      SUM(" . $queryDuracionTotalXClasesRealizadasGlobal . ") AS duracionTotalXClasesRealizadasGlobal,
                      COUNT(DISTINCT pago.id) AS numeroPagosXBolsaHorasGlobal,
                      SUM(pago.monto) AS montoTotalPagosXBolsaHorasGlobal," .
                    // </editor-fold>
                    // <editor-fold desc="Datos por bolsa de horas">
                    "SUM(CASE WHEN IFNULL(pagoXBolsaHoras.costoXHoraClase, 0) > 0 
			THEN ((IFNULL(pagoXBolsaHoras.monto, 0) - IFNULL(pagoXBolsaHoras.saldoFavor, 0)) * 3600 / (pagoXBolsaHoras.costoXHoraClase))
                        ELSE 0
                      END) AS duracionTotalXClases,
                      SUM(" . $queryDuracionTotalXClasesRealizadas . ") AS duracionTotalXClasesRealizadas,
                      COUNT(DISTINCT pagoXBolsaHoras.id) AS numeroPagosXBolsaHoras,
                      SUM(pagoXBolsaHoras.monto) AS montoTotalPagosXBolsaHoras"
                    // </editor-fold>  
            )
    );
    return DB::table(DB::raw("({$alumnos->toSql()}) AS T"))
                    ->mergeBindings($alumnos->getQuery())
                    ->select(DB::raw(
                                    "T.*,
                                    (duracionTotalXClasesRealizadas*100/duracionTotalXClases) AS porcentajeAvanceXClases,
                                    (duracionTotalXClasesRealizadasGlobal*100/duracionTotalXClasesGlobal) AS porcentajeAvanceXClasesGlobal"
                            )
    );
  }

  public static function listarBusqueda($terminoBus = NULL)/* - */ {
    $alumnos = Alumno::listarBase()->select("entidad.id", DB::raw('CONCAT(entidad.nombre, " ", entidad.apellido) AS nombreCompleto'));
    if (isset($terminoBus)) {
      $alumnos->whereRaw('CONCAT(entidad.nombre, " ", entidad.apellido) like ?', ["%{$terminoBus}%"]);
    }
    return $alumnos->lists("nombreCompleto", "entidad.id");
  }

  public static function listarXIdProfesorActual($idProfesorActual)/* - */ {
    return Alumno::listarBase()->where(Alumno::nombreTabla() . ".idProfesorActual", $idProfesorActual);
  }

  public static function obtenerXId($id, $simple = FALSE)/* - */ {
    $alumno = Alumno::listarBase()->where("entidad.id", $id)->firstOrFail();

    if (!$simple) {
      $alumno->profesorActual = NULL;
      if (Profesor::verificarExistencia($alumno->idProfesorActual)) {
        $alumno->profesorActual = Profesor::obtenerXId($alumno->idProfesorActual, TRUE);
      }

      $entidadCurso = EntidadCurso::obtenerXIdEntidad($id);
      $entidadNivelIngles = EntidadNivelIngles::obtenerXIdEntidad($id);

      $alumno->horario = Horario::obtenerJsonXIdEntidad($id);
      $alumno->idCurso = (isset($entidadCurso) ? $entidadCurso->idCurso : NULL);
      $alumno->direccionUbicacion = Ubigeo::obtenerTextoUbigeo($alumno->codigoUbigeo);
      $alumno->interesadoRelacionado = Interesado::obtenerXIdAlumno($id);
      $alumno->idNivelIngles = (isset($entidadNivelIngles) ? $entidadNivelIngles->idNivelIngles : NULL);
      $alumno->numeroPeriodos = Clase::totalPeriodosXIdAlumno($id);

      $datosIdsAntSig = Entidad::ObtenerIdsAnteriorSiguienteXEntidad(TiposEntidad::Alumno, $alumno);
      $alumno->idAlumnoAnterior = $datosIdsAntSig["idEntidadAnterior"];
      $alumno->idAlumnoSiguiente = $datosIdsAntSig["idEntidadSiguiente"];

      //Bolsa de horas
      $duracionTotalXClases = 0;
      $duracionTotalXClasesRealizadas = 0;
      $duracionTotalXClasesPendientes = 0;
      $pagosXBolsaHoras = PagoAlumno::listarXBolsaHoras($id)->get();
      foreach ($pagosXBolsaHoras as $pagoXBolsaHoras) {
        $duracionTotalXClases += $pagoXBolsaHoras->duracionTotalXClases;
        $duracionTotalXClasesRealizadas += $pagoXBolsaHoras->duracionTotalXClasesRealizadas;
        $duracionTotalXClasesPendientes += $pagoXBolsaHoras->duracionTotalXClasesPendientes;
      }
      $alumno->duracionTotalXClases = $duracionTotalXClases;
      $alumno->duracionTotalXClasesRealizadas = $duracionTotalXClasesRealizadas;
      $alumno->duracionTotalXClasesPendientes = $duracionTotalXClasesPendientes;
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

    Notificacion::registrarActualizar([
        "idEntidades" => [$idEntidad, (Auth::guest() ? NULL : Auth::user()->idEntidad)],
        "titulo" => (Auth::guest() ? MensajesNotificacion::TituloAlumnoRegistro : MensajesNotificacion::TituloAlumnoRegistroXUsuario),
        "enviarCorreo" => (Auth::guest() ? 1 : 0)
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

  public static function actualizarProfesor($id, $idDocente)/* - */ {
    $idProfesor = $idDocente;
    if (Postulante::verificarExistencia($idDocente)) {
      $idProfesor = Postulante::registrarProfesor($idDocente);
    }

    $alumno = Alumno::obtenerXId($id, TRUE);
    $alumno->idProfesoractual = $idProfesor;
    $alumno->save();
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

  public static function listarClases($id) {
    $nombreTablaClase = Clase::nombreTabla();
    $nombreTablaPagoClase = PagoClase::nombreTabla();
    $nombreTablaAlumnoBolsaHoras = AlumnoBolsaHoras::nombreTabla();

    return Clase::listarBase()->select(DB::raw(
                                    $nombreTablaClase . ".id, " .
                                    $nombreTablaClase . ".duracion, " .
                                    $nombreTablaClase . ".estado, " .
                                    $nombreTablaClase . ".fechaInicio, " .
                                    $nombreTablaClase . ".fechaFin, " .
                                    $nombreTablaClase . ".fechaConfirmacion, " .
                                    $nombreTablaClase . ".comentarioProfesor, " .
                                    $nombreTablaClase . ".comentarioAlumno, " .
                                    $nombreTablaClase . ".comentarioParaAlumno,
                                    entidadProfesor.nombre AS nombreProfesor, 
                                    entidadProfesor.apellido AS apellidoProfesor"))
                    ->where($nombreTablaClase . ".idAlumno", $id)
                    ->whereIn($nombreTablaClase . ".estado", [EstadosClase::ConfirmadaProfesor, EstadosClase::ConfirmadaProfesorAlumno, EstadosClase::Realizada])
                    ->whereRaw($nombreTablaClase . ".id IN (SELECT idClase 
                                                              FROM " . $nombreTablaPagoClase . " 
                                                              WHERE idPago IN (SELECT idPago FROM " . $nombreTablaAlumnoBolsaHoras . "
                                                                                  WHERE idAlumno = " . $id . "))");
  }

  public static function registrarComentariosClase($id, $datos)/* - */ {
    $nombreTablaClase = Clase::nombreTabla();
    $idClases = Alumno::listarClases($id)->lists($nombreTablaClase . ".id")->toArray();
    if (in_array($datos["idClase"], $idClases)) {
      $datos["tipo"] = 1;
      $datos["idAlumno"] = $id;
      Clase::actualizarComentarios($datos["idClase"], $datos);
    }
  }

  public static function confirmarClase($id, $idClase)/* - */ {
    $nombreTablaClase = Clase::nombreTabla();
    $idClases = Alumno::listarClases($id)->lists($nombreTablaClase . ".id")->toArray();
    if (in_array($idClase, $idClases)) {
      $clase = Clase::obtenerXIdNUEVO($idClase, $id);
      if ($clase->estado == EstadosClase::ConfirmadaProfesor) {
        $clase->estado = EstadosClase::Realizada;
        $clase->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
        $clase->save();
      }
    }
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
