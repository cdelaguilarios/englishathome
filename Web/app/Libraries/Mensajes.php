<?php

namespace App\Libraries;

class Mensajes {

    private function obtenerMensaje($tipo = "exitosos") {
        $sessionActual = session('mensajes-' . $tipo);
        if (!isset($sessionActual)) {
            session(['mensajes-' . $tipo => array()]);
        }
        return session('mensajes-' . $tipo);
    }

    private function agregarMensaje($tipo, $mensaje) {
        $mensajes = $this->obtenerMensaje($tipo);
        array_push($mensajes, $mensaje);
        session(['mensajes-' . $tipo => $mensajes]);
    }

    public function agregarMensajeExitoso($mensaje) {
        $this->agregarMensaje("exitosos", $mensaje);
    }

    public function agregarMensajeAdvertencia($mensaje) {
        $this->agregarMensaje("advertencias", $mensaje);
    }

    public function agregarMensajeAlerta($mensaje) {
        $this->agregarMensaje("alertas", $mensaje);
    }

    public function agregarMensajeError($mensaje) {
        $this->agregarMensaje("errores", $mensaje);
    }

    public function obtenerMensajes() {
        return [
            "exitosos" => $this->obtenerMensaje("exitosos"),
            "advertencias" => $this->obtenerMensaje("advertencias"),
            "alertas" => $this->obtenerMensaje("alertas"),
            "errores" => $this->obtenerMensaje("errores")];
    }

    public function limpiar() {
        session(['mensajes-exitosos' => array()]);
        session(['mensajes-advertencias' => array()]);
        session(['mensajes-alertas' => array()]);
        session(['mensajes-errores' => array()]);
    }

}
