<?php

namespace App\Models;

use DB;
use Log;
use Auth;
use Carbon\Carbon;
use App\Helpers\Enum\EstadosClase;
use App\Helpers\Enum\TiposEntidad;
use App\Helpers\Enum\EstadosAlumno;
use App\Helpers\Enum\EstadosProfesor;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Enum\MensajesNotificacion;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Profesor extends Model {

  public $timestamps = false;
  protected $primaryKey = "idEntidad";
  protected $table = "profesor";
  protected $fillable = [
      "ultimosTrabajos",
      "experienciaOtrosIdiomas",
      "descripcionPropia",
      "ensayo",
      "cv",
      "certificadoInternacional",
      "imagenDocumentoIdentidad",
      "audio",
      "comentarioPerfil"
  ];

  public static function nombreTabla()/* - */ {
    $modeloProfesor = new Profesor();
    $nombreTabla = $modeloProfesor->getTable();
    unset($modeloProfesor);
    return $nombreTabla;
  }

  public static function listar($datos = NULL)/* - */ {
    $nombreTabla = Profesor::nombreTabla();
    $profesores = Profesor::leftJoin(Entidad::nombreTabla() . " as entidad", $nombreTabla . ".idEntidad", "=", "entidad.id")
            ->leftJoin(EntidadCurso::nombreTabla() . " as entidadCurso", $nombreTabla . ".idEntidad", "=", "entidadCurso.idEntidad")
            ->leftJoin(EntidadCuentaBancaria::nombreTabla() . " as cuentaBancaria", $nombreTabla . ".idEntidad", "=", "cuentaBancaria.idEntidad")
            ->where("entidad.eliminado", 0)
            ->groupBy("entidad.id")
            ->distinct();

    if (isset($datos["estado"])) {
      $profesores->where("entidad.estado", $datos["estado"]);
    }

    return $profesores->select(DB::raw(
                            $nombreTabla . ".*, 
                        entidad.*, 
                        GROUP_CONCAT(
                          DISTINCT CONCAT(cuentaBancaria.banco, '|', cuentaBancaria.numeroCuenta) 
                          SEPARATOR ';'
                        ) AS cuentasBancarias,
                        CONCAT(entidad.nombre, ' ', entidad.apellido) AS nombreCompleto"));
  }

  public static function listarBusqueda($terminoBus = NULL)/* - */ {
    $profesores = Profesor::listar()->select("entidad.id", DB::raw('CONCAT(entidad.nombre, " ", entidad.apellido) AS nombreCompleto'));
    if (isset($terminoBus)) {
      $profesores->whereRaw('CONCAT(entidad.nombre, " ", entidad.apellido) like ?', ["%{$terminoBus}%"]);
    }
    return $profesores->lists("nombreCompleto", "entidad.id");
  }

  public static function obtenerXId($id, $simple = FALSE)/* - */ {
    $profesor = Profesor::listar()->where("entidad.id", $id)->firstOrFail();

    if (!$simple) {
      $profesor->horario = Horario::obtenerJsonXIdEntidad($id);
      $profesor->direccionUbicacion = Ubigeo::obtenerTextoUbigeo($profesor->codigoUbigeo);
      $profesor->cursos = EntidadCurso::obtenerXIdEntidad($id, FALSE);
      $profesor->cuentasBancarias = EntidadCuentaBancaria::obtenerXIdEntidad($id);

      $datosIdsAntSig = Entidad::ObtenerIdsAnteriorSiguienteXEntidad(TiposEntidad::Profesor, $profesor);
      $profesor->idProfesorAnterior = $datosIdsAntSig["idEntidadAnterior"];
      $profesor->idProfesorSiguiente = $datosIdsAntSig["idEntidadSiguiente"];
    }
    return $profesor;
  }

  public static function registrar($req)/* - */ {
    $datos = $req->all();
    if (isset($datos["fechaNacimiento"])) {
      $datos["fechaNacimiento"] = Carbon::createFromFormat("d/m/Y H:i:s", $datos["fechaNacimiento"] . " 00:00:00")->toDateTimeString();
    }

    $idEntidad = Entidad::registrar($datos, TiposEntidad::Profesor, EstadosProfesor::Registrado);
    Entidad::registrarActualizarImagenPerfil($idEntidad, $req->file("imagenPerfil"));
    EntidadCurso::registrarActualizar($idEntidad, $datos["idCursos"]);
    Horario::registrarActualizar($idEntidad, $datos["horario"]);
    EntidadCuentaBancaria::registrarActualizar($idEntidad, $datos["cuentasBancarias"]);

    $datos["cv"] = Archivo::procesarArchivosSubidosNUEVO("", $datos, 1, "DocumentoPersonalCv");
    $datos["certificadoInternacional"] = Archivo::procesarArchivosSubidosNUEVO("", $datos, 1, "DocumentoPersonalCertificadoInternacional");
    $datos["imagenDocumentoIdentidad"] = Archivo::procesarArchivosSubidosNUEVO("", $datos, 1, "DocumentoPersonalImagenDocumentoIdentidad");

    $profesor = new Profesor($datos);
    $profesor->idEntidad = $idEntidad;
    $profesor->save();

    Docente::registrarActualizarAudio($idEntidad, $req->file("audio"));
    Notificacion::registrar([
        "idEntidades" => [$idEntidad, Auth::user()->idEntidad],
        "titulo" => MensajesNotificacion::TituloProfesorRegistroXUsuario
    ]);
    return $idEntidad;
  }

  public static function actualizar($id, $req)/* - */ {
    $datos = $req->all();
    if (isset($datos["fechaNacimiento"])) {
      $datos["fechaNacimiento"] = Carbon::createFromFormat("d/m/Y H:i:s", $datos["fechaNacimiento"] . " 00:00:00")->toDateTimeString();
    }

    Entidad::actualizar($id, $datos, TiposEntidad::Profesor, $datos["estado"]);
    Entidad::registrarActualizarImagenPerfil($id, $req->file("imagenPerfil"));
    EntidadCurso::registrarActualizar($id, $datos["idCursos"]);
    Horario::registrarActualizar($id, $datos["horario"]);
    Docente::registrarActualizarAudio($id, $req->file("audio"));
    unset($datos["audio"]);
    EntidadCuentaBancaria::registrarActualizar($id, $datos["cuentasBancarias"]);

    $profesor = Profesor::obtenerXId($id, TRUE);
    $datos["cv"] = Archivo::procesarArchivosSubidosNUEVO($profesor->cv, $datos, 1, "DocumentoPersonalCv");
    $datos["certificadoInternacional"] = Archivo::procesarArchivosSubidosNUEVO($profesor->certificadoInternacional, $datos, 1, "DocumentoPersonalCertificadoInternacional");
    $datos["imagenDocumentoIdentidad"] = Archivo::procesarArchivosSubidosNUEVO($profesor->imagenDocumentoIdentidad, $datos, 1, "DocumentoPersonalImagenDocumentoIdentidad");
    $profesor->update($datos);

    if (Usuario::verificarExistencia($id)) {
      $usuario = Usuario::obtenerXId($id);
      $usuario->email = $datos["correoElectronico"];
      $usuario->update();
    }
  }

  public static function actualizarEstado($id, $estado)/* - */ {
    Profesor::obtenerXId($id, TRUE);
    Entidad::actualizarEstado($id, $estado);
  }

  public static function actualizarHorario($id, $horario)/* - */ {
    Profesor::obtenerXId($id, TRUE);
    Horario::registrarActualizar($id, $horario);
  }

  public static function actualizarComentariosPerfil($id, $datos)/* - */ {
    $profesor = Profesor::ObtenerXId($id, TRUE);
    $profesor->comentarioPerfil = $datos["comentarioPerfil"];
    $profesor->save();
  }

  public static function eliminar($id)/* - */ {
    Profesor::obtenerXId($id, TRUE);
    Entidad::eliminar($id);
  }

  public static function verificarExistencia($id)/* - */ {
    try {
      Profesor::obtenerXId($id, TRUE);
    } catch (\Exception $e) {
      Log::error($e);
      return FALSE;
    }
    return TRUE;
  }

  public static function listarAlumnos($id, $soloVigentes = FALSE, $soloAntiguos = FALSE)/* - */ {
    $idsAlumnosVigentes = Alumno::listarXIdProfesorActual($id)
                    ->where("entidad.estado", EstadosAlumno::Activo)
                    ->lists("entidad.id")->toArray();
    if ($soloVigentes) {
      $idsAlumnos = $idsAlumnosVigentes;
    } else {
      $nombreTablaClase = Clase::nombreTabla();
      $idsAlumnos = Clase::listarBase()
                      ->where($nombreTablaClase . ".idProfesor", $id)
                      ->whereIn($nombreTablaClase . ".estado", [EstadosClase::ConfirmadaProfesor, EstadosClase::ConfirmadaProfesorAlumno, EstadosClase::Realizada])
                      ->lists($nombreTablaClase . ".idAlumno")->toArray();

      if ($soloAntiguos) {
        $idsAlumnos = array_diff($idsAlumnos, $idsAlumnosVigentes);
      } else {
        $idsAlumnos = array_merge($idsAlumnos, $idsAlumnosVigentes);
      }
    }
    return (count($idsAlumnos) > 0 ? Alumno::listarBase()->whereIn("entidad.id", array_unique($idsAlumnos))->get() : null);
  }

  public static function obtenerAlumno($id, $idAlumno, $incluirDatosPagosXBolsaHoras = FALSE)/* - */ {
    $alumnosProfesor = Profesor::listarAlumnos($id)->toArray();
    $alumnosProfesorFil = array_filter($alumnosProfesor, function($alumnoProfesor) use ($idAlumno) {
      return $alumnoProfesor["id"] == $idAlumno;
    });
    if (!(isset($alumnosProfesorFil) && count($alumnosProfesorFil) > 0)) {
      throw new ModelNotFoundException;
    }

    $alumno = Alumno::obtenerXId($idAlumno, TRUE);
    $alumno->horario = Horario::obtenerJsonXIdEntidad($idAlumno);

    if ($alumno->idProfesorActual == $id) {
      $ultimoPago = null;
      $duracionTotalXClases = 0;
      $duracionTotalXClasesRealizadas = 0;
      $duracionTotalXClasesPendientes = 0;
      $pagosXBolsaHoras = PagoAlumno::listarXBolsaHoras($idAlumno)->get();
      foreach ($pagosXBolsaHoras as $pagoXBolsaHoras) {
        $ultimoPago = $pagoXBolsaHoras;
        $duracionTotalXClases += $pagoXBolsaHoras->duracionTotalXClases;
        $duracionTotalXClasesRealizadas += $pagoXBolsaHoras->duracionTotalXClasesRealizadas;
        $duracionTotalXClasesPendientes += $pagoXBolsaHoras->duracionTotalXClasesPendientes;
      }

      $alumno->duracionTotalXClases = $duracionTotalXClases;
      $alumno->duracionTotalXClasesRealizadas = $duracionTotalXClasesRealizadas;
      $alumno->duracionTotalXClasesPendientes = $duracionTotalXClasesPendientes;
      $alumno->porcentajeAvance = ($duracionTotalXClases > 0 ? ($duracionTotalXClasesRealizadas * 100 / $duracionTotalXClases) : 100);
      if ($incluirDatosPagosXBolsaHoras) {
        $alumno->pagosXBolsaHoras = $pagosXBolsaHoras;
        $alumno->ultimoPagoXBolsaHoras = $ultimoPago;
      }
    }
    return $alumno;
  }

  public static function listarClasesAlumno($id, $idAlumno)/* - */ {
    $alumnosProfesor = Profesor::listarAlumnos($id)->toArray();
    $alumnosProfesorFil = array_filter($alumnosProfesor, function($alumnoProfesor) use ($idAlumno) {
      return $alumnoProfesor["id"] == $idAlumno;
    });
    if (!(isset($alumnosProfesorFil) && count($alumnosProfesorFil) > 0)) {
      throw new ModelNotFoundException;
    }

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
                                    $nombreTablaClase . ".comentarioParaProfesor"))
                    ->where($nombreTablaClase . ".idProfesor", $id)
                    ->where($nombreTablaClase . ".idAlumno", $idAlumno)
                    ->whereIn($nombreTablaClase . ".estado", [EstadosClase::ConfirmadaProfesor, EstadosClase::ConfirmadaProfesorAlumno, EstadosClase::Realizada])
                    ->whereRaw($nombreTablaClase . ".id IN (SELECT idClase 
                                                              FROM " . $nombreTablaPagoClase . " 
                                                              WHERE idPago IN (SELECT idPago FROM " . $nombreTablaAlumnoBolsaHoras . "
                                                                                  WHERE idAlumno = " . $idAlumno . "))");
  }

  public static function registrarAvanceClase($id, $idAlumno, $datos)/* - */ {
    $nombreTablaClase = Clase::nombreTabla();
    $idClases = Profesor::listarClasesAlumno($id, $idAlumno)->lists($nombreTablaClase . ".id")->toArray();
    if (in_array($datos["idClase"], $idClases)) {
      $datos["tipo"] = 2;
      $datos["idAlumno"] = $idAlumno;
      Clase::actualizarComentarios($datos["idClase"], $datos);
    }
  }

  public static function confirmarClase($id, $idAlumno, $datos)/* - */ {
    $duracion = (int) $datos["duracion"];
    $alumno = Profesor::obtenerAlumno($id, $idAlumno, TRUE);

    //1 - Verificación de tiempo disponible de la bolsa de horas del alumno
    if ($alumno->duracionTotalXClasesPendientes < $duracion) {
      throw new Exception("La duración de la clase confirmada es superior a las horas que le quedan al alumno");
    }

    //2 - Registro de datos de la clase
    if (isset($datos["fecha"]) && isset($datos["horaInicio"])) {
      $fechaConfirmacion = Carbon::createFromFormat("d/m/Y H:i:s", $datos["fecha"] . " 00:00:00")->addSeconds($datos["horaInicio"])->addSeconds($duracion);
    } else {
      $fechaConfirmacion = Carbon::now();
    }

    $fechaInicio = clone $fechaConfirmacion;
    $ultimoPagoXBolsaHoras = $alumno->ultimoPagoXBolsaHoras;
    $datosClase = [
        "idAlumno" => $idAlumno,
        "idProfesor" => $id,
        "numeroPeriodo" => $ultimoPagoXBolsaHoras->periodoClases,
        "duracion" => $duracion,
        "fechaInicio" => $fechaInicio->subSeconds($duracion)->toDateTimeString(),
        "fechaFin" => $fechaConfirmacion->toDateTimeString(),
        "fechaConfirmacion" => $fechaConfirmacion->toDateTimeString(),
        "estado" => EstadosClase::ConfirmadaProfesor
    ];

    $clase = new Clase($datosClase);
    $clase->fechaRegistro = Carbon::now()->toDateTimeString();
    $clase->save();

    //3 - Asociación de la clase con uno o más pagos de la bolsa de horas
    $duracionRestante = $duracion;
    foreach ($alumno->pagosXBolsaHoras as $pagoXBolsaHoras) {
      if ($duracionRestante <= 0) {
        break;
      }
      if ($pagoXBolsaHoras->duracionTotalXClasesPendientes <= 0) {
        continue;
      }

      $duracionCubierta = $duracionRestante;
      if ($pagoXBolsaHoras->duracionTotalXClasesPendientes < $duracionRestante) {
        $duracionCubierta = $pagoXBolsaHoras->duracionTotalXClasesPendientes;
      }

      $pagoClase = new PagoClase([
          "idPago" => $pagoXBolsaHoras->id,
          "idClase" => $clase->id,
          "duracionCubierta" => $duracionCubierta
      ]);
      $pagoClase->save();

      $duracionRestante -= $duracionCubierta;
    }

    //4 - Registro del avance de clases ingresado por el profesor
    $datos["idClase"] = $clase->id;
    Profesor::registrarAvanceClase($id, $idAlumno, $datos);

    //5 - Actualización de la "fecha de inicio" de clases del alumno en el caso que no se haya utilizado la bolsa de horas
    if ($alumno->duracionTotalXClasesRealizadas == 0) {
      $alumno = Alumno::obtenerXId($idAlumno, TRUE);
      $alumno->fechaInicioClase = $fechaConfirmacion;
      $alumno->save();
    }

    //6 - En el caso que se haya utilizado todo el tiempo disponible de la bolsa de horas esta debe ser "limpiada"
    if ($alumno->duracionTotalXClasesPendientes == $duracion) {
      AlumnoBolsaHoras::where("idAlumno", $idAlumno)->delete();
    }

    //TODO: revisar
    /* Notificacion::registrar([
      "idEntidades" => [$id, $idAlumno],
      "titulo" => "[" . TiposEntidad::Profesor . "] confirmó una clase del alumno(a) [" . TiposEntidad::Alumno . "]"
      ]); */
  }

  // <editor-fold desc="TODO: ELIMINAR">
  //REPORTE
  public static function listarCampos() {
    return [
        "ultimosTrabajos" => ["titulo" => "Últimos trabajos"],
        "experienciaOtrosIdiomas" => ["titulo" => "Experencia en otros idiomas"],
        "descripcionPropia" => ["titulo" => "Descripción propia"],
        "ensayo" => ["titulo" => "Ensayo"],
        "cv" => ["titulo" => "CV"],
        "certificadoInternacional" => ["titulo" => "Certificado internacional"],
        "imagenDocumentoIdentidad" => ["titulo" => "Imagen documento de identidad"],
        "audio" => ["titulo" => "Audio"]
    ];
  }

  public static function obtenerProximaClase($id, $idAlumno) {
    return Profesor::listarClasesBase($id, $idAlumno, TRUE)
                    ->orderBy(Clase::nombreTabla() . ".fechaInicio", "ASC")
                    ->first();
  }

  public static function listarClasesBase($id, $idAlumno = NULL, $soloVigentes = FALSE, $soloConfirmadasORealizadas = FALSE) {
    $nombreTabla = Clase::nombreTabla();
    $clases = Clase::listarBase()->where($nombreTabla . ".idProfesor", $id);
    if (!is_null($idAlumno))
      $clases->where($nombreTabla . ".idAlumno", $idAlumno);
    if ($soloVigentes)
      $clases->whereIn($nombreTabla . ".estado", [EstadosClase::Programada, EstadosClase::PendienteConfirmar]);
    else if ($soloConfirmadasORealizadas)
      $clases->whereIn($nombreTabla . ".estado", [EstadosClase::ConfirmadaProfesor, EstadosClase::ConfirmadaProfesorAlumno, EstadosClase::Realizada]);
    return $clases;
  }

  // </editor-fold>
}
