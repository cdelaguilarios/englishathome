<?php

use App\Helpers\Enum\RolesUsuario;

Route::get("iniciar_sesion", ["uses" => "Auth\AuthController@getLogin", "as" => "auth.login"]);
Route::post("iniciar_sesion", ["uses" => "Auth\AuthController@postLogin", "as" => "auth.login"]);
Route::get("cerrar_sesion", ["uses" => "Auth\AuthController@getLogout", "as" => "auth.logout"]);

Route::post("interesados/registroExterno", ["uses" => "InteresadoController@registroExterno", "as" => "interesados.regstro.externo"]);
Route::get("alumno/registroExterno/{codigoVerificacion}", ["uses" => "AlumnoController@registroExterno", "as" => "usuarios.crear.externo"]);

Route::post("ubigeo/listarDepartamentos", ["uses" => "UbigeoController@listarDepartamentos", "as" => "ubigeo.listarDepartamentos"]);
Route::post("ubigeo/listarProvincias/{codigoDepartamento}", ["uses" => "UbigeoController@listarProvincias", "as" => "ubigeo.listarProvincias"]);
Route::post("ubigeo/listarDistritos/{codigoProvincia}", ["uses" => "UbigeoController@listarDistritos", "as" => "ubigeo.listarDistritos"]);

  Route::resource("alumnos", "AlumnoController");
Route::group(["middleware" => "auth"], function() {
  Route::get("/", ["uses" => "InicioController@inicio", "as" => "/"]);
  Route::get("imagenes/{rutaImagen}", ["uses" => "InicioController@obtenerImagen", "as" => "imagenes"]);



  Route::group(["middleware" => "verificacion.usuario:,"], function() {
    // <editor-fold desc="Usuarios">
    Route::group(["middleware" => "verificacion.usuario:[" . RolesUsuario::Principal . "],true"], function() {
      Route::get("usuarios", ["uses" => "UsuarioController@index", "as" => "usuarios"]);
      Route::post("usuarios/listar", ["uses" => "UsuarioController@listar", "as" => "usuarios.listar"]);
      Route::get("usuario/nuevo", ["uses" => "UsuarioController@create", "as" => "usuarios.nuevo"]);
      Route::post("usuarios", ["uses" => "UsuarioController@store", "as" => "usuarios.store"]);
      Route::delete("usuario/{id}/destroy", ["uses" => "UsuarioController@destroy", "as" => "usuarios.destroy"]);
    });
    Route::get("usuario/{id}/editar", ["uses" => "UsuarioController@edit", "as" => "usuarios.editar"]);
    Route::patch("usuario/{id}/update", ["uses" => "UsuarioController@update", "as" => "usuarios.update"]);
    // </editor-fold>
    // <editor-fold desc="Interesados">
    Route::resource("interesados", "InteresadoController", ["except" => ["show"]]);
    Route::get("interesados", ["uses" => "InteresadoController@index", "as" => "interesados"]);
    Route::post("interesados/listar", ["uses" => "InteresadoController@listar", "as" => "interesados.listar"]);
    Route::get("interesado/nuevo", ["uses" => "InteresadoController@create", "as" => "interesados.nuevo"]);
    Route::get("interesado/{id}/editar", ["uses" => "InteresadoController@edit", "as" => "interesados.editar"]);
    Route::post("interesado/{id}/actualizarEstado", ["uses" => "InteresadoController@actualizarEstado", "as" => "interesados.actualizar.estado"]);
    Route::get("interesado/{id}/cotizacion", ["uses" => "InteresadoController@cotizacion", "as" => "interesados.cotizacion"]);
    Route::post("interesado/{id}/cotizacion", ["uses" => "InteresadoController@envioCotizacion", "as" => "interesados.cotizacion"]);
    // </editor-fold>
    // <editor-fold desc="Alumnos">
    Route::get("alumnos", ["uses" => "AlumnoController@index", "as" => "alumnos"]);
    Route::post("alumnos/listar", ["uses" => "AlumnoController@listar", "as" => "alumnos.listar"]);
    Route::get("alumno/nuevo", ["uses" => "AlumnoController@create", "as" => "alumnos.nuevo"]);
    Route::get("alumno/{id}/editar", ["uses" => "AlumnoController@edit", "as" => "alumnos.editar"]);
    Route::get("alumno/{id}/perfil", ["uses" => "AlumnoController@show", "as" => "alumnos.perfil"]);
    Route::post("alumno/{id}/pagos", ["uses" => "AlumnoController@listarPagos", "as" => "alumnos.pagos.listar"]);
    Route::post("alumno/{id}/pago/generarClases", ["uses" => "AlumnoController@generarClasesXPago", "as" => "alumnos.pagos.generarClases"]);
    Route::post("alumno/{id}/pago/docentesDisponibles", ["uses" => "AlumnoController@listarDocentesDisponiblesXPago", "as" => "alumnos.pagos.docentesDisponibles.listar"]);
    Route::post("alumno/{id}/pago/registrar", ["uses" => "AlumnoController@registrarPago", "as" => "alumnos.pagos.registrar"]);
    Route::post("alumno/{id}/pago/actualizarEstado", ["uses" => "AlumnoController@actualizarEstadoPago", "as" => "alumnos.pagos.actualizar.estado"]);
    Route::post("alumno/{id}/pago/{idPago}/datos", ["uses" => "AlumnoController@datosPago", "as" => "alumnos.pagos.datos"]);
    Route::delete("alumno/{id}/pago/{idPago}/eliminar", ["uses" => "AlumnoController@eliminarPago", "as" => "alumnos.pagos.eliminar"]);
    Route::post("alumno/{id}/periodosClases", ["uses" => "AlumnoController@listarPeriodosClases", "as" => "alumnos.periodosClases.listar"]);
    Route::post("alumno/{id}/periodo/{numeroPeriodo}/clases", ["uses" => "AlumnoController@listarClases", "as" => "alumnos.periodo.clases.listar"]);
    Route::post("alumno/{id}/clase/docentesDisponibles", ["uses" => "AlumnoController@listarDocentesDisponiblesXClase", "as" => "alumnos.clases.docentesDisponibles.listar"]);
    Route::post("alumno/{id}/clase/registrarActualizar", ["uses" => "AlumnoController@registrarActualizarClase", "as" => "alumnos.clases.registrar.actualizar"]);
    Route::post("alumno/{id}/clase/actualizarEstado", ["uses" => "AlumnoController@actualizarEstadoClase", "as" => "alumnos.clases.actualizar.estado"]);
    Route::post("alumno/{id}/clase/{idClase}/datos", ["uses" => "AlumnoController@datosClase", "as" => "alumnos.clases.datos"]);
    Route::post("alumno/{id}/clase/cancelar", ["uses" => "AlumnoController@cancelarClase", "as" => "alumnos.clases.cancelar"]);
    Route::delete("alumno/{id}/clase/{idClase}/eliminar", ["uses" => "AlumnoController@eliminarClase", "as" => "alumnos.clases.eliminar"]);
    // </editor-fold>
    // <editor-fold desc="Profesores">
    Route::resource("profesores", "ProfesorController");
    Route::get("profesores", ["uses" => "ProfesorController@index", "as" => "profesores"]);
    Route::post("profesores/listar", ["uses" => "ProfesorController@listar", "as" => "profesores.listar"]);
    Route::get("profesor/nuevo", ["uses" => "ProfesorController@create", "as" => "profesores.nuevo"]);
    Route::get("profesor/{id}/perfil", ["uses" => "ProfesorController@show", "as" => "profesores.perfil"]);
    Route::get("profesor/{id}/editar", ["uses" => "ProfesorController@edit", "as" => "profesores.editar"]);
    Route::post("profesor/{id}/pagos", ["uses" => "ProfesorController@listarPagos", "as" => "profesores.pagos.listar"]);
    Route::post("profesor/{id}/pago/registrar", ["uses" => "ProfesorController@registrarPago", "as" => "profesores.pagos.registrar"]);
    Route::post("profesor/{id}/pago/actualizarEstado", ["uses" => "ProfesorController@actualizarEstadoPago", "as" => "profesores.pagos.actualizar.estado"]);
    Route::post("profesor/{id}/pago/{idPago}/datos", ["uses" => "ProfesorController@datosPago", "as" => "profesores.pagos.datos"]);
    Route::delete("profesor/{id}/pago/{idPago}/eliminar", ["uses" => "ProfesorController@eliminarPago", "as" => "profesores.pagos.eliminar"]);
    Route::post("profesor/{id}/clases", ["uses" => "ProfesorController@listarClases", "as" => "profesores.clases.listar"]);
    Route::post("profesor/{id}/clases/pago/registrar", ["uses" => "ProfesorController@registrarPagoXClases", "as" => "profesores.clases.pagos.registrar"]);
    // </editor-fold>
    // <editor-fold desc="Historial">
    Route::post("historial/{id}", ["uses" => "HistorialController@historial", "as" => "historial"]);
    // </editor-fold>
    // <editor-fold desc="Curso">
    Route::post("curso/{id}/datos", ["uses" => "CursoController@datos", "as" => "cursos.datos"]);
    // </editor-fold>
  });
});
