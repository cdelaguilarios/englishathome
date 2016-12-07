<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Mensajes;

class VerificacionUsuario {

    /**
     * Run the request filter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $roles) {       
        if (isset($roles) && !is_null($roles) && $roles != "" && $roles != "*") {
            $rolUsuario = Auth::user()->rol;
            $roles = array_flip(explode("|", str_replace("[", "", str_replace("]", "", $roles))));
            if (!(isset($rolUsuario) && !is_null($rolUsuario) && array_key_exists($rolUsuario, $roles))) {
                Mensajes::agregarMensajeAdvertencia("No tiene permisos suficientes para ingresar a la sección seleccionada.");
                return redirect(route('/'));
            }
        }
        return $next($request);
    }

}
