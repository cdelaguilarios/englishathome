<?php

namespace App\Http\Controllers;

use Datatables;
use App\Models\Profesor;
use App\Http\Controllers\Controller;

class ProfesorController extends Controller {

    protected $data = array();

    public function __construct() {
        $this->data['seccion'] = 'profesores';
    }

    public function index() {
        return view('profesor.lista', $this->data);
    }

    public function listar() {
        return Datatables::of(Profesor::Listar())->make(true);
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

}
