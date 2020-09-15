<?php

namespace App\Http\Controllers;

use PDF;
use Log;
use Auth;
use Input;
use Config;
use Storage;
use Mensajes;
use Datatables;
use App\Models\Clase;
use App\Models\Profesor;
use App\Models\PagoProfesor;
use App\Http\Controllers\Controller;
use App\Http\Requests\ActualizarHorarioRequest;
use App\Http\Requests\Profesor\BusquedaRequest;
use App\Http\Requests\Profesor\FormularioRequest;
use App\Http\Requests\Profesor\Pago as PagoRequest;
use App\Http\Requests\Profesor\ActualizarEstadoRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\Profesor\MisAlumnos\ConfirmarClaseRequest;
use App\Http\Requests\Profesor\MisAlumnos\RegistrarAvanceRequest;
use App\Http\Requests\Profesor\ActualizarComentariosPerfilRequest;

class ProfesorController extends Controller {

  protected $data = array();
  protected $rutasArchivosEliminar = array();

  public function __construct() {
    $this->data["seccion"] = "docentes";
    $this->data["subSeccion"] = "profesores";
  }

  public function __destruct() {
    foreach ($this->rutasArchivosEliminar as $rutaArchivoEliminar) {
      file_exists($rutaArchivoEliminar) ? unlink($rutaArchivoEliminar) : FALSE;
    }
  }

  // <editor-fold desc="Profesor">
  public function index() {
    return view("profesor.lista", $this->data);
  }

  public function listar(BusquedaRequest $req) {
    return Datatables::of(Profesor::listar($req->all()))->filterColumn("entidad.nombre", function($q, $k) {
              $q->whereRaw('CONCAT(entidad.nombre, " ", entidad.apellido) like ?', ["%{$k}%"]);
            })->filterColumn("entidad.fechaRegistro", function($q, $k) {
              $q->whereRaw("DATE_FORMAT(entidad.fechaRegistro, '%d/%m/%Y %H:%i:%s') like ?", ["%{$k}%"]);
            })->make(true);
  }

  public function buscar() {
    $termino = Input::get("termino");

    $profesoresPro = [];
    $profesores = Profesor::listarBusqueda($termino["term"]);
    foreach ($profesores as $id => $nombreCompleto) {
      $profesoresPro[] = ['id' => $id, 'text' => $nombreCompleto];
    }
    return \Response::json(["results" => $profesoresPro]);
  }

  public function crear() {
    return view("profesor.crear", $this->data);
  }

  public function registrar(FormularioRequest $req) {
    try {
      $idProfesor = Profesor::registrar($req);
      Mensajes::agregarMensajeExitoso("Registro exitoso.");
      return redirect(route("profesores.perfil", ["id" => $idProfesor]));
    } catch (\Exception $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("Ocurrió un problema durante el registro de datos. Por favor inténtelo nuevamente.");
      return redirect(route("profesores.crear"));
    }
  }

  public function perfil($id) {
    try {
      $this->data["profesor"] = Profesor::obtenerXId($id);
    } catch (ModelNotFoundException $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("No se encontraron datos del profesor seleccionado. Es posible que haya sido eliminado.");
      return redirect(route("profesores"));
    }
    return view("profesor.perfil", $this->data);
  }

  public function ficha($id) {
    try {
      $this->data["vistaImpresion"] = TRUE;
      $this->data["impresionDirecta"] = TRUE;
      $this->data["profesor"] = Profesor::obtenerXId($id);
    } catch (ModelNotFoundException $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("No se encontraron datos del profesor seleccionado. Es posible que haya sido eliminado.");
      return redirect(route("profesores"));
    }
    return view("profesor.ficha", $this->data);
  }

  public function fichaAlumno($id) {
    try {
      $this->data["vistaImpresion"] = TRUE;
      $this->data["impresionDirecta"] = TRUE;
      $this->data["profesor"] = Profesor::obtenerXId($id);
    } catch (ModelNotFoundException $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("No se encontraron datos del profesor seleccionado. Es posible que haya sido eliminado.");
      return redirect(route("profesores"));
    }
    return view("profesor.fichaAlumno", $this->data);
  }

  public function descargarFicha($id) {
    try {
      $profesor = Profesor::obtenerXId($id);
      $this->data["vistaImpresion"] = TRUE;
      $this->data["profesor"] = $profesor;
      $pdf = PDF::loadView("profesor.ficha", $this->data);
      $rutaBaseAlmacenamiento = Storage::disk("local")->getDriver()->getAdapter()->getPathPrefix();
      $nombrePdf = str_replace(["á", "é", "í", "ó", "ú"], ["a", "e", "i", "o", "u"], "Ficha " . ($profesor->sexo == "F" ? "de la profesora" : "del profesor" ) . " " . mb_strtolower($profesor->nombre . " " . $profesor->apellido) . ".pdf");
      $this->rutasArchivosEliminar[] = $rutaBaseAlmacenamiento . $nombrePdf;
      $datosPdf = $pdf->setOption("margin-top", "30mm")
              ->setOption("dpi", 108)->setOption("page-size", "A4")
              ->setOption("viewport-size", "1280x1024")
              ->setOption("footer-font-size", "8")
              ->setOption("footer-left", Config::get("eah.nombreComercialEmpresa") . " " . date("Y") . " - Ficha " . ($profesor->sexo == "F" ? "de la profesora" : "del profesor" ) . " " . $profesor->nombre . " " . $profesor->apellido)
              ->setOption("footer-right", '"Pag. [page] de [topage]"')
              ->save($rutaBaseAlmacenamiento . $nombrePdf, true);
    } catch (\Exception $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("Ocurrió un problema durante la descarga de la ficha del profesor.");
      return redirect(route("profesores"));
    }
    return $datosPdf->download($nombrePdf);
  }

  public function editar($id) {
    try {
      $this->data["profesor"] = Profesor::obtenerXId($id);
    } catch (ModelNotFoundException $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("No se encontraron datos del profesor seleccionado. Es posible que haya sido eliminado.");
      return redirect(route("profesores"));
    }
    return view("profesor.editar", $this->data);
  }

  public function actualizar($id, FormularioRequest $req) {
    try {
      Profesor::actualizar($id, $req);
      Mensajes::agregarMensajeExitoso("Actualización exitosa.");
    } catch (\Exception $e) {
      Log::error($e->getMessage());
      Mensajes::agregarMensajeError("Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente.");
    }
    return redirect(route("profesores.editar", ["id" => $id]));
  }

  public function actualizarEstado($id, ActualizarEstadoRequest $req) {
    try {
      $datos = $req->all();
      Profesor::actualizarEstado($id, $datos["estado"]);
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente."], 400);
    }
    return response()->json(["mensaje" => "Actualización exitosa."], 200);
  }

  public function actualizarHorario($id, ActualizarHorarioRequest $req) {
    try {
      $datos = $req->all();
      Profesor::actualizarHorario($id, $datos["horario"]);
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente."], 400);
    }
    return response()->json(["mensaje" => "Actualización exitosa."], 200);
  }

  public function actualizarComentariosPerfil($id, ActualizarComentariosPerfilRequest $req) {
    try {
      Profesor::actualizarComentariosPerfil($id, $req->all());
      Mensajes::agregarMensajeExitoso("Actualización exitosa.");
    } catch (\Exception $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente.");
    }
    return redirect(route("profesores.perfil", ["id" => $id]));
  }

  public function eliminar($id) {
    try {
      Profesor::eliminar($id);
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "No se pudo eliminar el registro de datos del profesor seleccionado."], 400);
    }
    return response()->json(["mensaje" => "Eliminación exitosa.", "id" => $id], 200);
  }

  // </editor-fold>
  // // <editor-fold desc="Pagos">

  public function listarPagos($id) {
    return Datatables::of(PagoProfesor::listar($id))->filterColumn("id", function($q, $k) {
              $q->whereRaw("id like ?", ["%{$k}%"])
                      ->orWhereRaw("motivo like ?", ["%{$k}%"])
                      ->orWhereRaw("SEC_TO_TIME(duracionTotalXClases) like ?", ["%{$k}%"]);
            })->filterColumn("fecha", function($q, $k) {
              $q->whereRaw("DATE_FORMAT(fecha, '%d/%m/%Y') like ?", ["%{$k}%"])
                      ->orWhereRaw("DATE_FORMAT(fecha, '%d/%m/%Y %H:%i:%s') like ?", ["%{$k}%"]);
            })->make(true);
  }

  public function registrarActualizarPagoGeneral($id, PagoRequest\FormularioRequest $req) {
    try {
      PagoProfesor::registrarActualizarGeneral($id, $req);
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "Ocurrió un problema durante el registro y/o actualización de datos. Por favor inténtelo nuevamente."], 500);
    }
    return response()->json(["mensaje" => "Se guardaron los cambios exitosamente."], 200);
  }

  public function obtenerDatosPago($id, $idPago) {
    return response()->json(PagoProfesor::obtenerXId($id, $idPago), 200);
  }

  public function actualizarEstadoPagoGeneral($id, $idPago, PagoRequest\ActualizarEstadoRequest $req) {
    try {
      PagoProfesor::actualizarEstadoGeneral($id, $idPago, $req->all());
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente."], 400);
    }
    return response()->json(["mensaje" => "Actualización exitosa."], 200);
  }

  public function eliminarPago($id, $idPago) {
    try {
      PagoProfesor::eliminar($id, $idPago);
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "No se pudo eliminar el registro de datos del pago seleccionado."], 400);
    }
    return response()->json(["mensaje" => "Eliminación exitosa", "id" => $idPago], 200);
  }

  // </editor-fold>
  // <editor-fold desc="Clases">
  public function listarClases($id) {
    return Datatables::of(Clase::listarXProfesor($id))
                    ->filterColumn("fechaConfirmacion", function($q, $k) {
                      $q->whereRaw('numeroPeriodo like ?', ["%{$k}%"])
                      ->orWhereRaw("DATE_FORMAT((CASE WHEN fechaConfirmacion IS NULL 
                                                  THEN fechaFin 
                                                  ELSE fechaConfirmacion 
                                                END), '%d/%m/%Y') like ?", ["%{$k}%"])
                      ->orWhereRaw("DATE_FORMAT(fechaInicio, '%H:%i') like ?", ["%{$k}%"])
                      ->orWhereRaw("DATE_FORMAT(fechaFin, '%H:%i') like ?", ["%{$k}%"])
                      ->orWhereRaw('SEC_TO_TIME(duracion) like ?', ["%{$k}%"])
                      ->orWhereRaw('CONCAT(entidadAlumno.nombre, " ", entidadAlumno.apellido) like ?', ["%{$k}%"])
                      ->orWhereRaw('pagoProfesor.id like ?', ["%{$k}%"]);
                    })
                    ->filterColumn("comentarioAlumno", function($q, $k) {
                      $q->whereRaw('comentarioAlumno like ?', ["%{$k}%"])
                      ->orWhereRaw('comentarioProfesor like ?', ["%{$k}%"])
                      ->orWhereRaw('comentarioParaAlumno like ?', ["%{$k}%"])
                      ->orWhereRaw('comentarioParaProfesor like ?', ["%{$k}%"]);
                    })->make(true);
  }

  // </editor-fold>
  // <editor-fold desc="Externo">
  public function misAlumnos() {
    $this->data["alumnosVigentes"] = Profesor::listarAlumnos(Auth::user()->idEntidad, TRUE);
    $this->data["alumnosAntiguos"] = Profesor::listarAlumnos(Auth::user()->idEntidad, FALSE, TRUE);
    return view("externo.profesor.misAlumnos", $this->data);
  }

  public function misAlumnosClases($idAlumno) {
    try {
      $this->data["alumno"] = Profesor::obtenerAlumno(Auth::user()->idEntidad, $idAlumno);
    } catch (ModelNotFoundException $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("No se encontraron datos del alumno seleccionado.");
      return redirect(route("/"));
    } catch (\Exception $e) {
      Log::error($e);
      Mensajes::agregarMensajeError("Ocurrió un problema durante la obtención de datos. Por favor inténtelo nuevamente.");
      return redirect(route("/"));
    }
    return view("externo.profesor.misAlumnosClases", $this->data);
  }

  public function misAlumnosListarClases($idAlumno) {
    return Datatables::of(Profesor::listarClasesAlumno(Auth::user()->idEntidad, $idAlumno))
                    ->filterColumn("fechaConfirmacion", function($q, $k) {
                      $q->whereRaw('fechaConfirmacion like ?', ["%{$k}%"])
                      ->orWhereRaw('fechaFin like ?', ["%{$k}%"])
                      ->orWhereRaw('duracion like ?', ["%{$k}%"])
                      ->orWhereRaw('estado like ?', ["%{$k}%"]);
                    })
                    ->filterColumn("comentarioProfesor", function($q, $k) {
                      $q->whereRaw('comentarioProfesor like ?', ["%{$k}%"]);
                    })
                    ->filterColumn("comentarioParaProfesor", function($q, $k) {
                      $q->whereRaw('comentarioParaProfesor like ?', ["%{$k}%"]);
                    })->make(true);
  }

  public function misAlumnosRegistrarAvanceClase($idAlumno, RegistrarAvanceRequest $req) {
    try {
      Profesor::registrarAvanceClase(Auth::user()->idEntidad, $idAlumno, $req->all());
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(["mensaje" => "Ocurrió un problema durante el registro de avance. Por favor inténtelo nuevamente."], 400);
    }
    return response()->json(["mensaje" => "Gracias por registrar tus avances."], 200);
  }

  public function misAlumnosConfirmarClase($idAlumno, ConfirmarClaseRequest $req) {
    try {
      Profesor::confirmarClase(Auth::user()->idEntidad, $idAlumno, $req->all());
      Mensajes::agregarMensajeExitoso("Confirmación exitosa.");
    } catch (\Exception $e) {
      Log::error($e->getMessage());
      Mensajes::agregarMensajeError("Ocurrió un problema durante la confirmación de la clase. Por favor inténtelo nuevamente.");
    }
    return redirect(route("profesores.mis.alumnos.clases", ["id" => $idAlumno]));
  }

  // </editor-fold>
}
