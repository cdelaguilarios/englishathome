<?php

use App\Helpers\Enum\RolesUsuario;

/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register all of the routes for an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the controller to call when that URI is requested.
  |
 */

Route::get('iniciar_sesion', ['uses' => 'Auth\AuthController@getLogin', 'as' => 'auth.login']);
Route::post('iniciar_sesion', ['uses' => 'Auth\AuthController@postLogin', 'as' => 'auth.login']);
Route::get('cerrar_sesion', ['uses' => 'Auth\AuthController@getLogout', 'as' => 'auth.logout']);

Route::group(['middleware' => 'auth'], function() {
    Route::get('/', ['uses' => 'InicioController@inicio', 'as' => '/']);
    Route::get('imagenes/{rutaImagen}', ['uses' => 'InicioController@obtenerImagen', 'as' => 'imagenes']);

    Route::post('ubigeo/listarDepartamentos', ['uses' => 'UbigeoController@listarDepartamentos', 'as' => 'ubigeo.listarDepartamentos']);
    Route::post('ubigeo/listarProvincias/{codigoDepartamento}', ['uses' => 'UbigeoController@listarProvincias', 'as' => 'ubigeo.listarProvincias']);
    Route::post('ubigeo/listarDistritos/{codigoProvincia}', ['uses' => 'UbigeoController@listarDistritos', 'as' => 'ubigeo.listarDistritos']);

    Route::group(['middleware' => 'verificacion.usuario:,'], function() {
        //Usuarios
        Route::group(['middleware' => 'verificacion.usuario:[' . RolesUsuario::Principal . '],true'], function() {
            Route::get('usuarios', ['uses' => 'UsuarioController@index', 'as' => 'usuarios']);
            Route::post('usuarios/listar', ['uses' => 'UsuarioController@listar', 'as' => 'usuarios.listar']);
            Route::get('usuario/nuevo', ['uses' => 'UsuarioController@create', 'as' => 'usuarios.nuevo']);
            Route::post('usuarios', ['uses' => 'UsuarioController@store', 'as' => 'usuarios.store']);
            Route::delete('usuario/{id}/destroy', ['uses' => 'UsuarioController@destroy', 'as' => 'usuarios.destroy']);
        });
        Route::get('usuario/{id}/editar', ['uses' => 'UsuarioController@edit', 'as' => 'usuarios.editar']);
        Route::patch('usuario/{id}/update', ['uses' => 'UsuarioController@update', 'as' => 'usuarios.update']);

        //Alumnos
        Route::resource('alumnos', 'AlumnoController');
        Route::get('alumnos', ['uses' => 'AlumnoController@index', 'as' => 'alumnos']);
        Route::post('alumnos/listar', ['uses' => 'AlumnoController@listar', 'as' => 'alumnos.listar']);    
        Route::get('alumno/nuevo', ['uses' => 'AlumnoController@create', 'as' => 'alumnos.nuevo']);
        Route::get('alumno/{id}/perfil', ['uses' => 'AlumnoController@show', 'as' => 'alumnos.perfil']);
        Route::get('alumno/{id}/editar', ['uses' => 'AlumnoController@edit', 'as' => 'alumnos.editar']);        
        Route::post('alumno/{id}/historial', ['uses' => 'AlumnoController@historial', 'as' => 'alumnos.historial']);
        
        Route::post('alumno/{id}/pagos', ['uses' => 'AlumnoController@listarPagos', 'as' => 'alumnos.pagos.listar']);
        Route::delete('alumno/{id}/pago/{idPago}/eliminar', ['uses' => 'AlumnoController@eliminarPago', 'as' => 'alumnos.pagos.eliminar']);
        Route::post('alumno/{id}/pago/generarClases', ['uses' => 'AlumnoController@generarClasesXPago', 'as' => 'alumnos.pagos.generarClases']);
        Route::post('alumno/{id}/pago/docentesDisponibles', ['uses' => 'AlumnoController@listarDocentesDisponiblesXPago', 'as' => 'alumnos.pagos.docentesDisponibles.listar']);
        Route::post('alumno/{id}/pago/{idPago}/datos', ['uses' => 'AlumnoController@datosPago', 'as' => 'alumnos.pagos.datos']);
        Route::post('alumno/{id}/pago/registrar', ['uses' => 'AlumnoController@registrarPago', 'as' => 'alumnos.pagos.registrar']);
                
        
        Route::post('alumno/{id}/periodosClases', ['uses' => 'AlumnoController@listarPeriodosClases', 'as' => 'alumnos.periodosClases.listar']);      
        Route::post('alumno/{id}/periodo/{numeroPeriodo}/clases', ['uses' => 'AlumnoController@listarClases', 'as' => 'alumnos.periodo.clases.listar']);        
        Route::delete('alumno/{id}/clase/{idClase}/eliminar', ['uses' => 'AlumnoController@eliminarClase', 'as' => 'alumnos.clases.eliminar']);
        Route::post('alumno/{id}/clase/docentesDisponibles', ['uses' => 'AlumnoController@listarDocentesDisponiblesXClase', 'as' => 'alumnos.clases.docentesDisponibles.listar']);
        Route::post('alumno/{id}/clase/cancelar', ['uses' => 'AlumnoController@cancelarClase', 'as' => 'alumnos.clases.cancelar']);
        Route::post('alumno/{id}/clase/registrar', ['uses' => 'AlumnoController@registrarClase', 'as' => 'alumnos.clases.registrar']);
        
        
        Route::get('test', ['uses' => 'AlumnoController@test', 'as' => 'alumnos.test']);
        
        //Interesados
        Route::resource('interesados', 'InteresadoController', ['except' => ['show']]);
        Route::get('interesados', ['uses' => 'InteresadoController@index', 'as' => 'interesados']);
        Route::post('interesados/listar', ['uses' => 'InteresadoController@listar', 'as' => 'interesados.listar']);
        Route::get('interesado/nuevo', ['uses' => 'InteresadoController@create', 'as' => 'interesados.nuevo']);
        Route::get('interesado/{id}/editar', ['uses' => 'InteresadoController@edit', 'as' => 'interesados.editar']);
        
        //Profesores
        Route::resource('profesores', 'AlumnoController');
        Route::get('profesores', ['uses' => 'ProfesorController@index', 'as' => 'profesores']);
        Route::post('profesores/listar', ['uses' => 'ProfesorController@listar', 'as' => 'profesores.listar']);
        Route::get('profesor/{id}/perfil', ['uses' => 'ProfesorController@show', 'as' => 'profesores.perfil']);
        
    });
});
