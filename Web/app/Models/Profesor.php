<?php

namespace App\Models;

use DB;
use Log;
use Auth;
use Carbon\Carbon;
use App\Helpers\Enum\EstadosClase;
use App\Helpers\Enum\TiposEntidad;
use App\Helpers\Enum\EstadosProfesor;
use App\Helpers\Enum\MensajesHistorial;
use Illuminate\Database\Eloquent\Model;

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
            ->select($nombreTabla . ".*", "entidad.*", DB::raw('CONCAT(entidad.nombre, " ", entidad.apellido) AS nombreCompleto'))
            ->where("entidad.eliminado", 0)
            ->groupBy("entidad.id")
            ->distinct();

    if (isset($datos["estado"])) {
      $profesores->where("entidad.estado", $datos["estado"]);
    }
    return $profesores;
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

    $datos["cv"] = Archivo::procesarArchivosSubidosNUEVO("", $datos, 1, "DocumentoPersonalCv");
    $datos["certificadoInternacional"] = Archivo::procesarArchivosSubidosNUEVO("", $datos, 1, "DocumentoPersonalCertificadoInternacional");
    $datos["imagenDocumentoIdentidad"] = Archivo::procesarArchivosSubidosNUEVO("", $datos, 1, "DocumentoPersonalImagenDocumentoIdentidad");

    $profesor = new Profesor($datos);
    $profesor->idEntidad = $idEntidad;
    $profesor->save();

    Docente::registrarActualizarAudio($idEntidad, $req->file("audio"));
    Historial::registrar([
        "idEntidades" => [$idEntidad, Auth::user()->idEntidad],
        "titulo" => MensajesHistorial::TituloProfesorRegistroXUsuario,
        "mensaje" => ""
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
    $idsAlumnosVigentes = Pago::listarXIdProfesorClases($id, TRUE)->lists("pagoAlumno.idAlumno")->toArray();
    if ($soloVigentes) {
      $idsAlumnos = $idsAlumnosVigentes;
    } else {
      $nombreTablaClase = Clase::nombreTabla();
      $idsAlumnos = Clase::listarBase()
                      ->where($nombreTablaClase . ".idProfesor", $id)
                      ->whereIn($nombreTablaClase . ".estado", [EstadosClase::ConfirmadaProfesorAlumno, EstadosClase::Realizada])
                      ->lists($nombreTablaClase . ".idAlumno")->toArray();
      if ($soloAntiguos) {
        $idsAlumnos = array_diff($idsAlumnos, $idsAlumnosVigentes);
      }
    }
    return (count($idsAlumnos) > 0 ? Alumno::listarBase()->whereIn("entidad.id", array_unique($idsAlumnos))->get() : null);
  }

  public static function obtenerAlumno($id, $idAlumno, $incluirDatosUltimoPago = FALSE)/* - */ {
    $ultimoPago = NULL;
    $duracionTotalXClases = 0;
    $duracionTotalXClasesRealizadas = 0;
    $duracionTotalXClasesPendientes = 0;

    $pagos = Pago::listarXIdProfesorClases($id)->where("pagoAlumno.idAlumno", $idAlumno);
    if ($pagos->count() > 0) {
      $ultimoPago = $pagos->first();
      $pagosXClasesVigentes = Pago::listarXIdProfesorClases($id, TRUE)->where("pagoAlumno.idAlumno", $idAlumno);

      if ($pagosXClasesVigentes->count() > 0) {
        $ultimoPago = $pagosXClasesVigentes->first();
        $preHorasPagadas = ((float) $ultimoPago->monto / (float) $ultimoPago->costoXHoraClase);
        $horasPagadas = ($preHorasPagadas - fmod($preHorasPagadas, 0.5));

        $duracionTotalXClases += $horasPagadas * 3600;
        $duracionTotalXClasesRealizadas += (isset($ultimoPago->duracionXClasesRealizadas) ? $ultimoPago->duracionXClasesRealizadas : 0);

        //Restamos duración total por clases que no fueron realizadas por el profesor
        $nombreTablaClase = Clase::nombreTabla();
        $datClases = Clase::listarBase()
                ->select(DB::raw("SUM(" . $nombreTablaClase . ".duracion) AS duracionTotal"))
                ->where($nombreTablaClase . ".idProfesor", '<>', $id)
                ->where($nombreTablaClase . ".idAlumno", $idAlumno)
                ->whereIn($nombreTablaClase . ".estado", [EstadosClase::ConfirmadaProfesorAlumno, EstadosClase::Realizada])
                ->whereRaw($nombreTablaClase . ".id IN (SELECT idClase FROM " . PagoClase::nombreTabla() . " WHERE idPago IN (" . $ultimoPago->id . "))");

        if ($datClases->count() > 0) {
          $duracionTotalXClases -= $datClases->first()->duracionTotal;
          $duracionTotalXClasesRealizadas -= $datClases->first()->duracionTotal;
        }
        $duracionTotalXClasesPendientes += ($duracionTotalXClases > $duracionTotalXClasesRealizadas ? $duracionTotalXClases - $duracionTotalXClasesRealizadas : 0);
      }
    }

    $alumno = Alumno::obtenerXId($idAlumno, TRUE);
    $alumno->horario = Horario::obtenerJsonXIdEntidad($idAlumno);
    $alumno->duracionTotalXClases = $duracionTotalXClases;
    $alumno->duracionTotalXClasesRealizadas = $duracionTotalXClasesRealizadas;
    $alumno->duracionTotalXClasesPendientes = $duracionTotalXClasesPendientes;
    $alumno->porcentajeAvance = ($duracionTotalXClases > 0 ? ($duracionTotalXClasesRealizadas * 100 / $duracionTotalXClases) : 100);
    if ($incluirDatosUltimoPago) {
      $alumno->ultimoPago = $ultimoPago;
    }
    return $alumno;
  }

  public static function listarClasesAlumno($id, $idAlumno)/* - */ {
    $idPagos = NULL;
    $pagos = Pago::listarXIdProfesorClases($id)->where("pagoAlumno.idAlumno", $idAlumno);
    if ($pagos->count() > 0) {
      $pagosXClasesVigentes = Pago::listarXIdProfesorClases($id, TRUE)->where("pagoAlumno.idAlumno", $idAlumno);
      $idPagos = ($pagosXClasesVigentes->count() > 0 ? [$pagosXClasesVigentes->first()->id] : $pagos->lists("id")->toArray());
    }

    $nombreTablaClase = Clase::nombreTabla();
    $clases = Clase::listarBase()->select(DB::raw(
                            $nombreTablaClase . ".id, " .
                            $nombreTablaClase . ".duracion, " .
                            $nombreTablaClase . ".estado, " .
                            $nombreTablaClase . ".fechaInicio, " .
                            $nombreTablaClase . ".fechaFin, " .
                            $nombreTablaClase . ".fechaConfirmacion, " .
                            $nombreTablaClase . ".fechaCancelacion, " .
                            $nombreTablaClase . ".comentarioProfesor, " .
                            $nombreTablaClase . ".comentarioParaProfesor"))
            ->where($nombreTablaClase . ".idProfesor", $id)
            ->where($nombreTablaClase . ".idAlumno", $idAlumno)
            ->whereIn($nombreTablaClase . ".estado", [EstadosClase::ConfirmadaProfesorAlumno, EstadosClase::Realizada]);
    if (isset($idPagos)) {
      $clases->whereRaw($nombreTablaClase . ".id IN (SELECT idClase FROM " . PagoClase::nombreTabla() . " WHERE idPago IN (" . implode(",", $idPagos) . "))");
    }
    return $clases;
  }

  public static function registrarAvanceClase($id, $idAlumno, $datos)/* - */ {
    Pago::listarXIdProfesorClases($id)->where("pagoAlumno.idAlumno", $idAlumno)->firstOrFail();

    $datos["tipo"] = 2;
    $datos["idAlumno"] = $idAlumno;
    Clase::actualizarComentarios($datos["idClase"], $datos);
  }

  public static function confirmarClase($id, $idAlumno, $datos)/* - */ {
    $alumno = Profesor::obtenerAlumno($id, $idAlumno, TRUE);
    $ultimoPago = $alumno->ultimoPago;
    $duracion = (int) $datos["duracion"];

    if ($alumno->duracionTotalXClasesPendientes < $duracion) {
      throw new Exception("La duración de la clase confirmada es superior a las horas que le quedan al alumno");
    }

    if (isset($datos["fecha"]) && isset($datos["horaInicio"])) {
      $fechaConfirmacion = Carbon::createFromFormat("d/m/Y H:i:s", $datos["fecha"] . " 00:00:00")->addSeconds($datos["horaInicio"])->addSeconds($duracion);
    } else {
      $fechaConfirmacion = Carbon::now();
    }

    $fechaInicio = clone $fechaConfirmacion;
    $datosClase = [
        "idAlumno" => $idAlumno,
        "idProfesor" => $id,
        "numeroPeriodo" => $ultimoPago->periodoClases,
        "duracion" => $duracion,
        "costoHora" => $ultimoPago->costoXHoraClase,
        "costoHoraProfesor" => $ultimoPago->pagoXHoraProfesor,
        "pagoTotalProfesor" => $ultimoPago->pagoXHoraProfesor * ($duracion / 3600),
        "fechaInicio" => $fechaInicio->subSeconds($duracion)->toDateTimeString(),
        "fechaFin" => $fechaConfirmacion->toDateTimeString(),
        "fechaConfirmacion" => $fechaConfirmacion->toDateTimeString(),
        "estado" => EstadosClase::Realizada
    ];

    $clase = new Clase($datosClase);
    $clase->fechaRegistro = Carbon::now()->toDateTimeString();
    $clase->save();

    PagoClase::registrarActualizar($ultimoPago->id, $clase->id, $idAlumno);

    $datos["idClase"] = $clase->id;
    Profesor::registrarAvanceClase($id, $idAlumno, $datos);

    //TODO: revisar
    /* Historial::registrar([
      "idEntidades" => [$id, $idAlumno],
      "titulo" => "[" . TiposEntidad::Profesor . "] confirmó una clase del alumno(a) [" . TiposEntidad::Alumno . "]",
      "mensaje" => ""
      ]); */

    if ($alumno->duracionTotalXClasesRealizadas == 0) {
      $alumno = Alumno::obtenerXId($idAlumno, TRUE);
      $alumno->fechaInicioClase = $fechaConfirmacion;
      $alumno->save();
    }
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
      $clases->whereIn($nombreTabla . ".estado", [EstadosClase::ConfirmadaProfesorAlumno, EstadosClase::Realizada]);
    return $clases;
  }
  // </editor-fold>

}
