<?php

namespace App\Http\Controllers;

use Log;
use Auth;
use Crypt;
use Input;
use Mensajes;
use Datatables;
use App\Models\Clase;
use App\Models\Alumno;
use App\Models\Docente;
use App\Models\Profesor;
use App\Models\PagoAlumno;
use App\Models\Interesado;
use App\Http\Controllers\Controller;
use App\Http\Requests\Alumno\BusquedaRequest;
use App\Http\Requests\ActualizarHorarioRequest;
use App\Http\Requests\Alumno\FormularioRequest;
use App\Http\Requests\Alumno\Pago as PagoRequest;
use App\Http\Requests\Alumno\Clase as ClaseRequest;
use App\Http\Requests\Alumno\ActualizarEstadoRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\Alumno\MisClases\RegistrarComentariosRequest;

class AlumnoController extends Controller {

  protected $data = array();

  public function __construct()/* - */ {
    $this->data["seccion"] = "alumnos";
  }

  // <editor-fold desc="Alumno">
  public function index()/* - */ {
    return view("alumno.lista", $this->data);
  }

  public function listar(BusquedaRequest $req)/* - */ {
    $datos = $req->all();
    return Datatables::of(Alumno::listar($datos["estado"]))->filterColumn("nombre", function($q, $k) {
              $q->whereRaw('CONCAT(nombre, " ", apellido) like ?', ["%{$k}%"])
                      ->orWhereRaw('telefono like ?', ["%{$k}%"])
                      ->orWhereRaw('distritoAlumno like ?', ["%{$k}%"])
                      ->orWhereRaw('CONCAT(nombreProfesor, " ", apellidoProfesor) like ?', ["%{$k}%"])
                      ->orWhereRaw('distritoProfesor like ?', ["%{$k}%"]);
            })->filterColumn("porcentajeAvanceXClases", function($q, $k) {
              $q->whereRaw("(duracionTotalXClasesRealizadasGlobal*100/duracionTotalXClasesGlobal) like ?", ["%{$k}%"])
                      ->orWhereRaw("SEC_TO_TIME(duracionTotalXClasesGlobal) like ?", ["%{$k}%"])
                      ->orWhereRaw("SEC_TO_TIME(duracionTotalXClasesRealizadasGlobal) like ?", ["%{$k}%"])
                      ->orWhereRaw("(duracionTotalXClasesRealizadas*100/duracionTotalXClases) like ?", ["%{$k}%"])
                      ->orWhereRaw("SEC_TO_TIME(duracionTotalXClases) like ?", ["%{$k}%"])
                      ->orWhereRaw("SEC_TO_TIME(duracionTotalXClasesRealizadas) like ?", ["%{$k}%"])
                      ->orWhereRaw("DATE_FORMAT(ultimaClaseFecha, '%d/%m/%Y') like ?", ["%{$k}%"]);
            })->filterColumn("estado", function($q, $k) {
              $q->whereRaw("estado like ?", ["%{$k}%"])
                      ->orWhereRaw('nivelIngles like ?', ["%{$k}%"]);
            })->filterColumn("montoTotalPagosXBolsaHoras", function($q, $k) {
              $q->whereRaw("montoTotalPagosXBolsaHoras like ?", ["%{$k}%"])
                      ->orWhereRaw("numeroPagosXBolsaHoras like ?", ["%{$k}%"]);
            })->filterColumn("fechaRegistro", function($q, $k) {
              $q->whereRaw("DATE_FORMAT(fechaRegistro, '%d/%m/%Y %H:%i:%s') like ?", ["%{$k}%"])
                      ->orWhereRaw("DATE_FORMAT(fechaInicioClase, '%d/%m/%Y %H:%i:%s') like ?", ["%{$k}%"]);
            })->make(true);
  }

  public function buscar()/* - */ {
    $termino = Input::get("termino");

    $alumnosPro = [];
    $alumnos = Alumno::listarBusqueda($termino["term"]);
    foreach ($alumnos as $id => $nombreCompleto) {
      $alumnosPro[] = ['id' => $id, 'text' => $nombreCompleto];
    }
    return \Response::json(["results" => $alumnosPro]);
  }

  public function crear()/* - */ {
    return view("alumno.crear", $this->data);
  }

  public function registrar(FormularioRequest $req)/* - */ {
    try {
      $idAlumno = Alumno::registrar($req);
      Mensajes::agregarMensajeExitoso("Registro exitoso.");
      return redirect(route("alumnos.perfil", ["id" => $idAlumno]));
    } catch (\Exception $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("Ocurrió un problema durante el registro de datos. Por favor inténtelo nuevamente.");
      return redirect(route("alumnos.crear"));
    }
  }

  public function crearExterno($codigoVerificacion)/* - */ {
    try {
      $nuevoRegistro = Input::get("nr");
      $this->data["nuevoRegistro"] = (isset($nuevoRegistro));
      $this->data["vistaExterna"] = TRUE;
      $this->data["codigoVerificacion"] = $codigoVerificacion;
      $this->data["interesado"] = Interesado::obtenerXId(Crypt::decrypt($codigoVerificacion));
      return view("alumno.crear", $this->data);
    } catch (\Exception $e) {
      Log::error($e);
      abort(404);
    }
  }

  public function registrarExterno(FormularioRequest $req)/* - */ {
    try {
      $datos = $req->all();
      Alumno::registrarExterno($req);
    } catch (\Exception $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("Ocurrió un problema durante el registro de datos. Por favor inténtelo nuevamente.");
    }
    return redirect(route("alumnos.crear.externo", ["codigoVerificacion" => $datos["codigoVerificacion"], "nr" => 1]));
  }

  public function perfil($id)/* - */ {
    try {
      $this->data["alumno"] = Alumno::obtenerXId($id);
    } catch (ModelNotFoundException $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("No se encontraron datos del alumno seleccionado. Es posible que haya sido eliminado.");
      return redirect(route("alumnos"));
    }
    return view("alumno.perfil", $this->data);
  }

  public function ficha($id)/* - */ {
    try {
      $this->data["vistaImpresion"] = TRUE;
      $this->data["impresionDirecta"] = TRUE;
      $this->data["alumno"] = Alumno::obtenerXId($id);
    } catch (ModelNotFoundException $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("No se encontraron datos del alumno seleccionado. Es posible que haya sido eliminado.");
      return redirect(route("alumnos"));
    }
    return view("alumno.ficha", $this->data);
  }

  public function editar($id)/* - */ {
    try {
      $this->data["alumno"] = Alumno::obtenerXId($id);
    } catch (ModelNotFoundException $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("No se encontraron datos del alumno seleccionado. Es posible que haya sido eliminado.");
      return redirect(route("alumnos"));
    }
    return view("alumno.editar", $this->data);
  }

  public function actualizar($id, FormularioRequest $req)/* - */ {
    try {
      Alumno::actualizar($id, $req);
      Mensajes::agregarMensajeExitoso("Actualización exitosa.");
    } catch (\Exception $e) {
      Log::error($e->getMessage());
      Mensajes::agregarMensajeError("Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente.");
    }
    return redirect(route("alumnos.editar", ["id" => $id]));
  }

  public function actualizarEstado($id, ActualizarEstadoRequest $req)/* - */ {
    try {
      $datos = $req->all();
      Alumno::actualizarEstado($id, $datos["estado"]);
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente."], 500);
    }
    return response()->json(["mensaje" => "Actualización exitosa."], 200);
  }

  public function actualizarHorario($id, ActualizarHorarioRequest $req)/* - */ {
    try {
      $datos = $req->all();
      Alumno::actualizarHorario($id, $datos["horario"]);
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente."], 400);
    }
    return response()->json(["mensaje" => "Actualización exitosa."], 200);
  }

  public function actualizarProfesor($id, $idDocente)/* - */ {
    try {
      Alumno::actualizarProfesor($id, $idDocente);
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente."], 400);
    }
    return response()->json(["mensaje" => "Actualización exitosa."], 200);
  }

  public function eliminar($id)/* - */ {
    try {
      Alumno::eliminar($id);
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "No se encontraron datos del alumno seleccionado."], 400);
    }
    return response()->json(["mensaje" => "Eliminación exitosa.", "id" => $id], 200);
  }

  // </editor-fold>
  // <editor-fold desc="Pagos">
  public function listarPagos($id)/* - */ {
    return Datatables::of(PagoAlumno::listar($id))->filterColumn("id", function($q, $k) {
              $q->whereRaw("id like ?", ["%{$k}%"])
                      ->orWhereRaw("motivo like ?", ["%{$k}%"])
                      ->orWhereRaw("cuenta like ?", ["%{$k}%"])
                      ->orWhereRaw("descripcion like ?", ["%{$k}%"])
                      ->orWhereRaw("costoXHoraClase like ?", ["%{$k}%"]);
            })->filterColumn("fecha", function($q, $k) {
              $q->whereRaw("DATE_FORMAT(fecha, '%d/%m/%Y') like ?", ["%{$k}%"])
                      ->orWhereRaw("DATE_FORMAT(fecha, '%d/%m/%Y %H:%i:%s') like ?", ["%{$k}%"]);
            })->filterColumn("monto", function($q, $k) {
              $q->whereRaw("monto like ?", ["%{$k}%"])
                      //Duración total por clases realizadas y canceladas
                      ->orWhereRaw("SEC_TO_TIME(duracionTotalXClasesRealizadas) like ?", ["%{$k}%"])
                      //Monto total por clases realizadas
                      ->orWhereRaw("montoTotalXClasesRealizadas like ?", ["%{$k}%"])
                      //Duración total por clases pendientes
                      ->orWhereRaw("SEC_TO_TIME(CASE WHEN duracionTotalXClases > (duracionTotalXClasesRealizadas + duracionTotalXClasesCanceladas) 
                                        THEN duracionTotalXClases - (duracionTotalXClasesRealizadas + duracionTotalXClasesCanceladas)
                                        ELSE 0
                                      END) like ?", ["%{$k}%"])
                      //Monto total por clases pendientes
                      ->orWhereRaw("(CASE WHEN montoTotalXClases > (montoTotalXClasesRealizadas + montoTotalXClasesCanceladas) 
                                        THEN montoTotalXClases - (montoTotalXClasesRealizadas + montoTotalXClasesCanceladas)
                                        ELSE 0
                                      END) like ?", ["%{$k}%"])
                      //Duración total por clases no pagadas
                      ->orWhereRaw("SEC_TO_TIME(CASE WHEN duracionTotalXClases < (duracionTotalXClasesRealizadas + duracionTotalXClasesCanceladas) 
                                        THEN  duracionTotalXClasesRealizadas + duracionTotalXClasesCanceladas - duracionTotalXClases
                                        ELSE 0
                                      END) like ?", ["%{$k}%"])
                      //Monto total por clases no pagadas
                      ->orWhereRaw("(CASE WHEN montoTotalXClases < (montoTotalXClasesRealizadas + montoTotalXClasesCanceladas) 
                                        THEN montoTotalXClasesRealizadas + montoTotalXClasesCanceladas - montoTotalXClases
                                        ELSE 0
                                      END) like ?", ["%{$k}%"])
                      //Duración total por clases
                      ->orWhereRaw("SEC_TO_TIME(duracionTotalXClases) like ?", ["%{$k}%"])
                      //Monto total por clases
                      ->orWhereRaw("montoTotalXClases like ?", ["%{$k}%"])
                      //Saldo a favor
                      ->orWhereRaw("saldoFavor like ?", ["%{$k}%"]);
            })->make(true);
  }

  public function registrarActualizarPago($id, PagoRequest\FormularioRequest $req)/* - */ {
    try {
      PagoAlumno::registrarActualizar($id, $req);
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "Ocurrió un problema durante el registro y/o actualización de datos. Por favor inténtelo nuevamente."], 500);
    }
    return response()->json(["mensaje" => "Se guardaron los cambios exitosamente."], 200);
  }

  public function obtenerDatosPago($id, $idPago)/* - */ {
    return response()->json(PagoAlumno::obtenerXId($id, $idPago), 200);
  }

  public function actualizarEstadoPago($id, $idPago, PagoRequest\ActualizarEstadoRequest $req)/* - */ {
    try {
      PagoAlumno::actualizarEstado($id, $idPago, $req->all());
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente."], 400);
    }
    return response()->json(["mensaje" => "Actualización exitosa."], 200);
  }

  public function eliminarPago($id, $idPago)/* - */ {
    try {
      PagoAlumno::eliminar($id, $idPago);
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "No se pudo eliminar el registro de datos del pago seleccionado."], 400);
    }
    return response()->json(["mensaje" => "Eliminación exitosa.", "id" => $idPago], 200);
  }

  // </editor-fold>
  // <editor-fold desc="Clases">
  public function listarClases($id)/* - */ {
    return Datatables::of(Clase::listarXAlumnoNUEVO($id))
                    ->filterColumn("fechaConfirmacion", function($q, $k) {
                      $q->whereRaw('numeroPeriodo like ?', ["%{$k}%"])
                      ->orWhereRaw("DATE_FORMAT((CASE WHEN fechaConfirmacion IS NULL 
                                                  THEN fechaFin 
                                                  ELSE fechaConfirmacion 
                                                END), '%d/%m/%Y') like ?", ["%{$k}%"])
                      ->orWhereRaw("DATE_FORMAT(fechaInicio, '%H:%i') like ?", ["%{$k}%"])
                      ->orWhereRaw("DATE_FORMAT(fechaFin, '%H:%i') like ?", ["%{$k}%"])
                      ->orWhereRaw('SEC_TO_TIME(duracion) like ?', ["%{$k}%"])
                      ->orWhereRaw('CONCAT(entidadProfesor.nombre, " ", entidadProfesor.apellido) like ?', ["%{$k}%"])
                      ->orWhereRaw('pagoAlumno.id like ?', ["%{$k}%"]);
                    })
                    ->filterColumn("comentarioAlumno", function($q, $k) {
                      $q->whereRaw('comentarioAlumno like ?', ["%{$k}%"])
                      ->orWhereRaw('comentarioProfesor like ?', ["%{$k}%"])
                      ->orWhereRaw('comentarioParaAlumno like ?', ["%{$k}%"])
                      ->orWhereRaw('comentarioParaProfesor like ?', ["%{$k}%"]);
                    })->make(true);
  }

  public function confirmarClase($id, ClaseRequest\ConfirmarClaseRequest $req)/* - */ {
    try {
      $datos = $req->all();
      $datos["comentario"] = "";
      Profesor::confirmarClase($datos["idProfesor"], $id, $datos);
      Mensajes::agregarMensajeExitoso("Confirmación exitosa.");
    } catch (\Exception $e) {
      Log::error($e->getMessage());
      Mensajes::agregarMensajeError("Ocurrió un problema durante la confirmación de la clase. Por favor inténtelo nuevamente.");
    }
    return redirect(route("alumnos.perfil", ["id" => $id, "seccion" => "clase"]));
  }

  public function obtenerDatosClase($id, $idClase) {
    return response()->json(Clase::obtenerXId($id, $idClase), 200);
  }

  public function eliminarClase($id, $idClase)/* - */ {
    try {
      Clase::eliminar($id, $idClase);
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "No se pudo eliminar el registro de datos de la clase seleccionada."], 400);
    }
    return response()->json(["mensaje" => "Eliminación exitosa", "id" => $idClase], 200);
  }

  public function descargarLista($id) {
    try {
      $this->data["vistaImpresion"] = TRUE;
      $this->data["impresionDirecta"] = TRUE;
      $this->data["alumno"] = Alumno::obtenerXId($id);
      $this->data["clases"] = Clase::listarXAlumnoNUEVO($id);
    } catch (ModelNotFoundException $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("No se encontraron datos del alumno seleccionado. Es posible que haya sido eliminado.");
      return redirect(route("alumnos"));
    }
    return view("alumno.clase.descargarLista", $this->data);
  }

  // </editor-fold>
  // <editor-fold desc="Externo">
  public function misClases()/* - */ {
    return view("externo.alumno.misClases", $this->data);
  }

  public function listarMisClases()/* - */ {
    return Datatables::of(Alumno::listarClases(Auth::user()->idEntidad))
                    ->filterColumn("fechaConfirmacion", function($q, $k) {
                      $q->whereRaw('fechaConfirmacion like ?', ["%{$k}%"])
                      ->orWhereRaw('fechaFin like ?', ["%{$k}%"])
                      ->orWhereRaw('duracion like ?', ["%{$k}%"])
                      ->orWhereRaw('entidadProfesor.nombre like ?', ["%{$k}%"])
                      ->orWhereRaw('entidadProfesor.apellido like ?', ["%{$k}%"])
                      ->orWhereRaw('estado like ?', ["%{$k}%"]);
                    })
                    ->filterColumn("comentarioProfesor", function($q, $k) {
                      $q->whereRaw('comentarioProfesor like ?', ["%{$k}%"]);
                    })
                    ->filterColumn("comentarioParaAlumno", function($q, $k) {
                      $q->whereRaw('comentarioParaAlumno like ?', ["%{$k}%"]);
                    })->make(true);
  }

  public function misClasesRegistrarComentarios(RegistrarComentariosRequest $req)/* - */ {
    try {
      Alumno::registrarComentariosClase(Auth::user()->idEntidad, $req->all());
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "Ocurrió un problema durante el registro de comentarios. Por favor inténtelo nuevamente."], 400);
    }
    return response()->json(["mensaje" => "Gracias por dejarnos tus comentarios."], 200);
  }

  public function misClasesConfirmar($idClase)/* - */ {
    try {
      Alumno::confirmarClase(Auth::user()->idEntidad, $idClase);
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "Ocurrió un problema durante la confirmación de la clase. Por favor inténtelo nuevamente."], 400);
    }
    return response()->json(["mensaje" => "Confirmación exitosa."], 200);
  }

  // </editor-fold>
  // <editor-fold desc="TODO: ELIMINAR">
  public function listarPeriodosClases($id) {
    return Datatables::of(Clase::listarPeriodosXIdAlumno($id))->make(true);
  }

  public function listarClasesXPeriodo($id, $numeroPeriodo) {
    return response()->json(Clase::listarXAlumno($id, $numeroPeriodo), 200);
  }

  public function actualizarEstadoClase($id, ClaseRequest\ActualizarEstadoRequest $req) {
    try {
      Clase::actualizarEstado($id, $req->all());
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente."], 400);
    }
    return response()->json(["mensaje" => "Actualización exitosa."], 200);
  }

  public function listarDocentesDisponiblesXClase($id, ClaseRequest\ListarDocentesDisponiblesRequest $req) {
    return Datatables::of(Docente::listarDisponiblesXDatosClase($req->all()))
                    ->filterColumn('nombreCompleto', function($q, $k) {
                      $q->whereRaw('CONCAT(entidad.nombre, " ", entidad.apellido) like ?', ["%{$k}%"]);
                    })->filterColumn('estado', function($q, $k) {
              $q->whereRaw('entidad.estado like ?', ["%{$k}%"]);
            })->make(true);
  }

  public function registrarActualizarClase($id, ClaseRequest\FormularioRequest $req) {
    try {
      $datos = $req->all();
      $datosClase = Clase::registrarActualizar($id, $datos);
      Mensajes::agregarMensajeExitoso(isset($datos["idClase"]) ? "Actualización exitosa." : "Registro exitoso.");
    } catch (\Exception $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("Ocurrió un problema durante el registro/actualización de datos. Por favor inténtelo nuevamente.");
    }
    return redirect(route("alumnos.perfil", ["id" => $id, "seccion" => "clase", "nrp" => $datosClase["numeroPeriodo"]]));
  }

  public function actualizarClasesGrupo($id, ClaseRequest\FormularioGrupoRequest $req) {
    try {
      $datos = $req->all();
      $nroPeriodo = Clase::actualizarGrupo($id, $datos);
      Mensajes::agregarMensajeExitoso("Actualización exitosa.");
    } catch (\Exception $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente.");
    }
    return redirect(route("alumnos.perfil", ["id" => $id, "seccion" => "clase", "nrp" => $nroPeriodo]));
  }

  public function cancelarClase($id, ClaseRequest\CancelarRequest $req) {
    try {
      $datos = $req->all();
      $nroPeriodo = Clase::cancelar($id, $datos);
      Mensajes::agregarMensajeExitoso("Cancelación exitosa.");
    } catch (\Exception $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("No se pudo cancelar la clase seleccionada.");
    }
    return redirect(route("alumnos.perfil", ["id" => $id, "seccion" => "clase", "nrp" => $nroPeriodo]));
  }

  public function datosClasesGrupo($id, ClaseRequest\DatosGrupoRequest $req) {
    return response()->json(Clase::datosGrupo($id, $req->all()), 200);
  }

  public function totalClasesXHorario($id, ClaseRequest\TotalHorarioRequest $req) {
    return response()->json(Clase::totalXHorario($id, $req->all()), 200);
  }

  // </editor-fold>
}
