<?php

namespace App\Http\Controllers;

use Log;
use Mensajes;
use Datatables;
use App\Models\Profesor;
use App\Models\TipoDocumento;
use App\Http\Requests\ProfesorRequest;
use App\Http\Controllers\Controller;

class ProfesorController extends Controller {

    protected $data = array();

    public function __construct() {
        $this->data['seccion'] = 'profesores';
        $this->data['tiposDocumentos'] = TipoDocumento::listarSimple();
    }

    // <editor-fold desc="Profesor">
    public function index() {
        return view('profesor.lista', $this->data);
    }

    public function listar() {
        return Datatables::of(Profesor::listar())->make(true);
    }

    public function create() {
        return view('profesor.crear', $this->data);
    }

    public function store(ProfesorRequest $req) {
            $idProfesor = Profesor::registrar($req);
            Mensajes::agregarMensajeExitoso("Registro exitoso.");
            return redirect(route('profesores.perfil', ['id' => $idProfesor]));
        try {
        } catch (\Exception $e) {
            Log::error($e);
            Mensajes::agregarMensajeError("Ocurrió un problema durante el registro de datos. Por favor inténtelo nuevamente.");
            return redirect(route('profesores.nuevo'));
        }
    }

    public function show($id) {
        try {
            $this->data['profesor'] = Profesor::ObtenerXId($id);
        } catch (ModelNotFoundException $e) {
            Log::error($e);
            Mensajes::agregarMensajeError("No se encontraron datos del profesor seleccionado.");
            return redirect('profesores');
        }
        return view('profesor.perfil', $this->data);
    }

    public function edit($id) {
        try {
            $this->data['profesor'] = Profesor::obtenerXId($id);
        } catch (ModelNotFoundException $e) {
            Log::error($e);
            Mensajes::agregarMensajeError("No se encontraron datos del profesor seleccionado.");
            return redirect('profesores');
        }
        return view('profesor.editar', $this->data);
    }

    public function update($id, ProfesorRequest $req) {
        try {
            Profesor::actualizar($id, $req);
            Mensajes::agregarMensajeExitoso("Actualización exitosa.");
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            Mensajes::agregarMensajeError("Ocurrió un problema durante la actualización de datos. Por favor inténtelo nuevamente.");
        }
        return redirect(route('profesores.editar', ['id' => $id]));
    }

    public function destroy($id) {
        try {
            Profesor::eliminar($id);
        } catch (ModelNotFoundException $e) {
            Log::error($e);
            return response()->json(['mensaje' => 'No se pudo eliminar el registro de datos del profesor seleccionado.'], 400);
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
}
