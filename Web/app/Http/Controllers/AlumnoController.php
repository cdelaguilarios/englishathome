<?php

namespace App\Http\Controllers;

use Log;
use Mensajes;
use Datatables;
use App\Models\Clase;
use App\Models\Alumno;
use App\Models\Docente;
use App\Models\Historial;
use App\Models\PagoAlumno;
use App\Models\NivelIngles;
use App\Models\TipoDocumento;
use App\Helpers\Enum\MotivosPago;
use App\Http\Requests\Alumno\Pago;
use App\Http\Requests\Alumno\Clase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Alumno\AlumnoRequest;
use App\Http\Requests\HistorialRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AlumnoController extends Controller {

    protected $data = array();

    public function __construct() {
        $this->data['seccion'] = 'alumnos';
        $this->data['nivelesIngles'] = NivelIngles::listarSimple();
        $this->data['tiposDocumentos'] = TipoDocumento::listarSimple();
        $this->data['motivosPago'] = MotivosPago::listar();
    }

    // <editor-fold desc="Alumno">
    public function index() {
        return view('alumno.lista', $this->data);
    }

    public function listar() {
        return Datatables::of(Alumno::listar())->make(true);
    }

    public function create() {
        return view('alumno.crear', $this->data);
    }

    public function store(AlumnoRequest $req) {
        try {
            $idAlumno = Alumno::registrar($req);
            Mensajes::agregarMensajeExitoso("Registro exitoso.");
            return redirect(route('alumnos.perfil', ['id' => $idAlumno]));
        } catch (\Exception $e) {
            Log::error($e);
            Mensajes::agregarMensajeError("Ocurrió un problema durante el registro de datos. Por favor inténtelo nuevamente.");
            return redirect(route('alumnos.nuevo'));
        }
    }

    public function show($id) {
        try {
            $this->data['alumno'] = Alumno::obtenerXId($id);
        } catch (ModelNotFoundException $e) {
            Log::error($e);
            Mensajes::agregarMensajeError("No se encontraron datos del alumno seleccionado.");
            return redirect('alumnos');
        }
        return view('alumno.perfil', $this->data);
    }

    public function edit($id) {
        try {
            $this->data['alumno'] = Alumno::obtenerXId($id);
        } catch (ModelNotFoundException $e) {
            Log::error($e);
            Mensajes::agregarMensajeError("No se encontraron datos del alumno seleccionado.");
            return redirect('alumnos');
        }
        return view('alumno.editar', $this->data);
    }

    public function update($id, AlumnoRequest $req) {
        try {
            Alumno::actualizar($id, $req);
            Mensajes::agregarMensajeExitoso("Actualización exitosa.");
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            Mensajes::agregarMensajeError("Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente.");
        }
        return redirect(route('alumnos.editar', ['id' => $id]));
    }

    public function destroy($id) {
        try {
            Alumno::eliminar($id);
        } catch (ModelNotFoundException $e) {
            Log::error($e);
            return response()->json(['mensaje' => 'No se pudo eliminar el registro de datos del alumno seleccionado.'], 400);
        }
        return response()->json(['mensaje' => 'Eliminación exitosa', 'id' => $id], 200);
    }

    // </editor-fold>
    // <editor-fold desc="Historial">
    public function historial($id, HistorialRequest $req) {
        $datos = $req->all();
        $datosHistorial = Historial::obtener($datos["numeroCarga"], $id);
        return response()->json($datosHistorial, 200);
    }

    // </editor-fold>
    // <editor-fold desc="Pagos">
    public function listarPagos($id) {
        return Datatables::of(PagoAlumno::listar($id))->make(true);
    }

    public function generarClasesXPago($id, Pago\PagoRequest $req) {
        return response()->json(Clase::generarXDatosPago($id, $req->all()), 200);
    }

    public function listarDocentesDisponiblesXPago($id, Pago\PagoRequest $req) {
        return Datatables::of(Docente::listarDisponiblesXDatosPago($id, $req->all()))
                        ->filterColumn('nombreCompleto', function($q, $k) {
                            $q->whereRaw('CONCAT(entidad.nombre, " ", entidad.apellido) like ?', ["%{$k}%"]);
                        })->make(true);
    }

    public function registrarPago($id, Pago\PagoRequest $request) {
        try {
            PagoAlumno::registrar($id, $request);
            Mensajes::agregarMensajeExitoso("Registro exitoso.");
        } catch (\Exception $e) {
            Log::error($e);
            Mensajes::agregarMensajeError("Ocurrió un problema durante el registro de datos. Por favor inténtelo nuevamente.");
        }
        return redirect(route('alumnos.perfil', ['id' => $id]));
    }

    public function datosPago($id, $idPago) {
        return response()->json(PagoAlumno::obtenerXId($id, $idPago), 200);
    }

    public function eliminarPago($id, $idPago) {
        try {
            PagoAlumno::eliminar($id, $idPago);
        } catch (ModelNotFoundException $e) {
            Log::error($e);
            return response()->json(['mensaje' => 'No se pudo eliminar el registro de datos del pago seleccionado.'], 400);
        }
        return response()->json(['mensaje' => 'Eliminación exitosa', 'id' => $idPago], 200);
    }

    // </editor-fold>
    // <editor-fold desc="Clases">
    public function listarPeriodosClases($id) {
        return Datatables::of(Clase::listarPeriodos($id))->make(true);
    }

    public function listarClases($id, $numeroPeriodo) {
        return response()->json(Clase::listar($id, $numeroPeriodo), 200);
    }

    public function listarDocentesDisponiblesXClase($id, Clase\DocenteDisponibleRequest $req) {
        return Datatables::of(Docente::listarDisponiblesXDatosClase($req->all()))
                        ->filterColumn('nombreCompleto', function($q, $k) {
                            $q->whereRaw('CONCAT(entidad.nombre, " ", entidad.apellido) like ?', ["%{$k}%"]);
                        })->make(true);
    }

    public function cancelarClase($id, Clase\CancelarRequest $request) {
        try {
            $datos = $request->all();
            Clase::cancelar($id, $datos);
            Mensajes::agregarMensajeExitoso("Cancelación exitosa.");
        } catch (\Exception $e) {
            Log::error($e);
            Mensajes::agregarMensajeError("No se pudo cancelar la clase seleccionada.");
        }
        return redirect(route('alumnos.perfil', ['id' => $id]));
    }

    public function registrarClase($id, Clase\ClaseRequest $request) {
        try {
            $datos = $request->all();
            Clase::registrar($id, $datos);
            Mensajes::agregarMensajeExitoso("Registro exitoso.");
        } catch (\Exception $e) {
            Log::error($e);
            Mensajes::agregarMensajeError("Ocurrió un problema durante el registro de datos. Por favor inténtelo nuevamente.");
        }
        return redirect(route('alumnos.perfil', ['id' => $id]));
    }

    public function eliminarClase($id, $idClase) {
        try {
            Clase::eliminar($id, $idClase);
        } catch (ModelNotFoundException $e) {
            Log::error($e);
            return response()->json(['mensaje' => 'No se pudo eliminar el registro de datos de la clase seleccionada.'], 400);
        }
        return response()->json(['mensaje' => 'Eliminación exitosa', 'id' => $idClase], 200);
    }

    // </editor-fold>

    public function test() {
        return response()->json([], 200);
    }

}
