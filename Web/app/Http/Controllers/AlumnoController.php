<?php

namespace App\Http\Controllers;

use PDF;
use Log;
use Crypt;
use Input;
use Storage;
use Mensajes;
use Datatables;
use App\Models\Clase;
use App\Models\Alumno;
use App\Models\Docente;
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

class AlumnoController extends Controller {

  protected $data = array();
  protected $rutasArchivosEliminar = array();

  public function __construct() {
    $this->data["seccion"] = "alumnos";
  }

  public function __destruct() {
    foreach ($this->rutasArchivosEliminar as $rutaArchivoEliminar) {
      file_exists($rutaArchivoEliminar) ? unlink($rutaArchivoEliminar) : FALSE;
    }
  }

  // <editor-fold desc="Alumno">
  public function index() {
    return view("alumno.lista", $this->data);
  }

  public function listar(BusquedaRequest $req) {
    return Datatables::of(Alumno::listar($req->all()))->filterColumn("entidad.nombre", function($q, $k) {
              $q->whereRaw('CONCAT(entidad.nombre, " ", entidad.apellido) like ?', ["%{$k}%"]);
            })->filterColumn("entidad.fechaRegistro", function($q, $k) {
              $q->whereRaw("DATE_FORMAT(entidad.fechaRegistro, '%d/%m/%Y %H:%i:%s') like ?", ["%{$k}%"]);
            })->make(true);
  }

  public function crear() {
    return view("alumno.crear", $this->data);
  }

  public function registrar(FormularioRequest $req) {
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

  public function crearExterno($codigoVerificacion) {
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

  public function registrarExterno(FormularioRequest $req) {
    try {
      $datos = $req->all();
      Alumno::registrarExterno($req);
    } catch (\Exception $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("Ocurrió un problema durante el registro de datos. Por favor inténtelo nuevamente.");
    }
    return redirect(route("alumnos.crear.externo", ["codigoVerificacion" => $datos["codigoVerificacion"], "nr" => 1]));
  }

  public function perfil($id) {
    try {
      $this->data["alumno"] = Alumno::obtenerXId($id);
    } catch (ModelNotFoundException $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("No se encontraron datos del alumno seleccionado. Es posible que haya sido eliminado.");
      return redirect(route("alumnos"));
    }
    return view("alumno.perfil", $this->data);
  }

  public function ficha($id) {
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

  public function descargarFicha($id) {
    try {
      $alumno = Alumno::obtenerXId($id);
      $this->data["vistaImpresion"] = TRUE;
      $this->data["alumno"] = $alumno;
      $pdf = PDF::loadView("alumno.ficha", $this->data);
      $rutaBaseAlmacenamiento = Storage::disk("local")->getDriver()->getAdapter()->getPathPrefix();
      $nombrePdf = str_replace(["á","é", "í","ó","ú"], ["a","e","i","o","u"], "Ficha " . ($alumno->sexo == "F" ? "de la alumna" : "del alumno" ) . " " . mb_strtolower($alumno->nombre . " " . $alumno->apellido) . ".pdf");
      $this->rutasArchivosEliminar[] = $rutaBaseAlmacenamiento . $nombrePdf;
      $datosPdf = $pdf->setOption("margin-top", "30mm")
              ->setOption("dpi", 108)->setOption("page-size", "A4")
              ->setOption("viewport-size", "1280x1024")
              ->setOption("footer-font-size", "8")
              ->setOption("footer-left", "English at home " . date("Y") . " - Ficha " . ($alumno->sexo == "F" ? "de la alumna" : "del alumno" ) . " " . $alumno->nombre . " " . $alumno->apellido)
              ->setOption("footer-right", '"Pag. [page] de [topage]"')
              ->save($rutaBaseAlmacenamiento . $nombrePdf, true);
    } catch (\Exception $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("Ocurrió un problema durante la descarga de la ficha del alumno.");
      return redirect(route("alumnos"));
    }
    return $datosPdf->download($nombrePdf);
  }

  public function editar($id) {
    try {
      $this->data["alumno"] = Alumno::obtenerXId($id);
    } catch (ModelNotFoundException $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("No se encontraron datos del alumno seleccionado. Es posible que haya sido eliminado.");
      return redirect(route("alumnos"));
    }
    return view("alumno.editar", $this->data);
  }

  public function actualizar($id, FormularioRequest $req) {
    try {
      Alumno::actualizar($id, $req);
      Mensajes::agregarMensajeExitoso("Actualización exitosa.");
    } catch (\Exception $e) {
      Log::error($e->getMessage());
      Mensajes::agregarMensajeError("Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente.");
    }
    return redirect(route("alumnos.editar", ["id" => $id]));
  }

  public function actualizarEstado($id, ActualizarEstadoRequest $req) {
    try {
      $datos = $req->all();
      Alumno::actualizarEstado($id, $datos["estado"]);
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente."], 400);
    }
    return response()->json(["mensaje" => "Actualización exitosa."], 200);
  }

  public function actualizarHorario($id, ActualizarHorarioRequest $req) {
    try {
      $datos = $req->all();
      Alumno::actualizarHorario($id, $datos["horario"]);
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente."], 400);
    }
    return response()->json(["mensaje" => "Actualización exitosa."], 200);
  }

  public function eliminar($id) {
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
  public function listarPagos($id) {
    return Datatables::of(PagoAlumno::listar($id))->filterColumn("pago.fecha", function($q, $k) {
              $q->whereRaw("DATE_FORMAT(pago.fecha, '%d/%m/%Y') like ?", ["%{$k}%"]);
            })->filterColumn("pago.fechaRegistro", function($q, $k) {
              $q->whereRaw("DATE_FORMAT(pago.fechaRegistro, '%d/%m/%Y %H:%i:%s') like ?", ["%{$k}%"]);
            })->make(true);
  }

  public function actualizarEstadoPago($id, PagoRequest\ActualizarEstadoRequest $req) {
    try {
      PagoAlumno::actualizarEstado($id, $req->all());
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente."], 400);
    }
    return response()->json(["mensaje" => "Actualización exitosa."], 200);
  }

  public function generarClasesXPago($id, PagoRequest\GenerarClasesRequest $req) {
    return response()->json(Clase::generarXDatosPago($id, $req->all()), 200);
  }

  public function listarDocentesDisponiblesXPago($id, PagoRequest\ListarDocentesDisponiblesRequest $req) {
    return Datatables::of(Docente::listarDisponiblesXDatosPago($id, $req->all()))
                    ->filterColumn("nombreCompleto", function($q, $k) {
                      $q->whereRaw('CONCAT(entidad.nombre, " ", entidad.apellido) like ?', ["%{$k}%"]);
                    })->make(true);
  }

  public function registrarPago($id, PagoRequest\FormularioRequest $req) {
    try {
      PagoAlumno::registrar($id, $req);
      Mensajes::agregarMensajeExitoso("Registro exitoso.");
    } catch (\Exception $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("Ocurrió un problema durante el registro de datos. Por favor inténtelo nuevamente.");
    }
    return redirect(route("alumnos.perfil", ["id" => $id, "sec" => "pago"]));
  }

  public function actualizarPago($id, PagoRequest\FormularioActualizarRequest $req) {
    try {
      PagoAlumno::actualizar($id, $req);
      Mensajes::agregarMensajeExitoso("Actualización exitosa.");
    } catch (\Exception $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente.");
    }
    return redirect(route("alumnos.perfil", ["id" => $id, "sec" => "pago"]));
  }

  public function datosPago($id, $idPago) {
    return response()->json(PagoAlumno::obtenerXId($id, $idPago), 200);
  }

  public function eliminarPago($id, $idPago) {
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
  public function listarPeriodosClases($id) {
    return Datatables::of(Clase::listarPeriodos($id))->make(true);
  }

  public function listarClases($id, $numeroPeriodo) {
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
    return redirect(route("alumnos.perfil", ["id" => $id, "sec" => "clase", "nrp" => $datosClase["numeroPeriodo"]]));
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
    return redirect(route("alumnos.perfil", ["id" => $id, "sec" => "clase", "nrp" => $nroPeriodo]));
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
    return redirect(route("alumnos.perfil", ["id" => $id, "sec" => "clase", "nrp" => $nroPeriodo]));
  }

  public function datosClase($id, $idClase) {
    return response()->json(Clase::obtenerXId($id, $idClase, TRUE), 200);
  }

  public function datosClasesGrupo($id, ClaseRequest\DatosGrupoRequest $req) {
    return response()->json(Clase::datosGrupo($id, $req->all()), 200);
  }

  public function totalClasesXHorario($id, ClaseRequest\TotalHorarioRequest $req) {
    return response()->json(Clase::totalXHorario($id, $req->all()), 200);
  }

  public function eliminarClase($id, $idClase) {
    try {
      Clase::eliminar($id, $idClase);
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "No se pudo eliminar el registro de datos de la clase seleccionada."], 400);
    }
    return response()->json(["mensaje" => "Eliminación exitosa", "id" => $idClase], 200);
  }

  // </editor-fold>
}
