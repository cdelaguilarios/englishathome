<?php

use App\Helpers\Enum\RolesUsuario;

Route::get("iniciar_sesion", ["uses" => "Auth\AuthController@getLogin", "as" => "auth.login"]);
Route::post("iniciar_sesion", ["uses" => "Auth\AuthController@postLogin", "as" => "auth.login"]);
Route::get("cerrar_sesion", ["uses" => "Auth\AuthController@getLogout", "as" => "auth.logout"]);

Route::post("interesado/registrar/externo", ["uses" => "InteresadoController@registrarExterno", "as" => "interesados.registrar.externo"]);
Route::get("alumno/nuevo/{codigoVerificacion}", ["uses" => "AlumnoController@crearExterno", "as" => "alumnos.crear.externo"]);
Route::post("alumno/registrar/externo", ["uses" => "AlumnoController@registrarExterno", "as" => "alumnos.registrar.externo"]);

Route::post("ubigeo/listarDepartamentos", ["uses" => "UbigeoController@listarDepartamentos", "as" => "ubigeo.listarDepartamentos"]);
Route::post("ubigeo/listarProvincias/{codigoDepartamento}", ["uses" => "UbigeoController@listarProvincias", "as" => "ubigeo.listarProvincias"]);
Route::post("ubigeo/listarDistritos/{codigoProvincia}", ["uses" => "UbigeoController@listarDistritos", "as" => "ubigeo.listarDistritos"]);

Route::get("cron/test", ["uses" => "CronController@test", "as" => "cron.test"]);
Route::get("cron/enviarCorreos", ["uses" => "CronController@enviarCorreos", "as" => "cron.enviar.correos"]);
Route::get("cron/sincronizarEstados", ["uses" => "CronController@sincronizarEstados", "as" => "cron.sincronizar.estados"]);

Route::get("archivos/{nombre}", ["uses" => "ArchivoController@obtener", "as" => "archivos"]);

Route::group(["middleware" => "auth"], function() {
  Route::get("/", ["uses" => "InicioController@inicio", "as" => "/"]);

  // <editor-fold desc="Archivos">
  Route::post("archivos", ["uses" => "ArchivoController@registrar", "as" => "archivos.reqistrar"]);
  Route::delete("archivos/eliminar", ["uses" => "ArchivoController@eliminar", "as" => "archivos.eliminar"]);
  // </editor-fold>  

  Route::group(["middleware" => "verificacion.usuario:,"], function() {
    // <editor-fold desc="Interesados">
    Route::get("interesados", ["uses" => "InteresadoController@index", "as" => "interesados"]);
    Route::post("interesados/listar", ["uses" => "InteresadoController@listar", "as" => "interesados.listar"]);
    Route::get("interesado/nuevo", ["uses" => "InteresadoController@crear", "as" => "interesados.crear"]);
    Route::post("interesado/registrar", ["uses" => "InteresadoController@registrar", "as" => "interesados.registrar"]);
    Route::get("interesado/{id}/editar", ["uses" => "InteresadoController@editar", "as" => "interesados.editar"]);
    Route::patch("interesado/{id}/actualizar", ["uses" => "InteresadoController@actualizar", "as" => "interesados.actualizar"]);
    Route::post("interesado/{id}/actualizarEstado", ["uses" => "InteresadoController@actualizarEstado", "as" => "interesados.actualizar.estado"]);
    Route::get("interesado/{id}/cotizar", ["uses" => "InteresadoController@cotizar", "as" => "interesados.cotizar"]);
    Route::post("interesado/{id}/enviarCotizacion", ["uses" => "InteresadoController@enviarCotizacion", "as" => "interesados.enviar.cotizacion"]);
    Route::get("interesado/{id}/perfilAlumno", ["uses" => "InteresadoController@perfilAlumno", "as" => "interesados.perfil.alumno"]);
    Route::delete("interesado/{id}/eliminar", ["uses" => "InteresadoController@eliminar", "as" => "interesados.eliminar"]);
    // </editor-fold>
    // <editor-fold desc="Alumnos">
    Route::get("alumnos", ["uses" => "AlumnoController@index", "as" => "alumnos"]);
    Route::post("alumnos/listar", ["uses" => "AlumnoController@listar", "as" => "alumnos.listar"]);
    Route::get("alumno/nuevo", ["uses" => "AlumnoController@crear", "as" => "alumnos.crear"]);
    Route::post("alumno/registrar", ["uses" => "AlumnoController@registrar", "as" => "alumnos.registrar"]);
    Route::get("alumno/{id}/editar", ["uses" => "AlumnoController@editar", "as" => "alumnos.editar"]);
    Route::patch("alumno/{id}/actualizar", ["uses" => "AlumnoController@actualizar", "as" => "alumnos.actualizar"]);
    Route::post("alumno/{id}/actualizarEstado", ["uses" => "AlumnoController@actualizarEstado", "as" => "alumnos.actualizar.estado"]);
    Route::post("alumno/{id}/actualizarHorario", ["uses" => "AlumnoController@actualizarHorario", "as" => "alumnos.actualizar.horario"]);
    Route::get("alumno/{id}/perfil", ["uses" => "AlumnoController@perfil", "as" => "alumnos.perfil"]);
    Route::delete("alumno/{id}/eliminar", ["uses" => "AlumnoController@eliminar", "as" => "alumnos.eliminar"]);
    // </editor-fold>
    // <editor-fold desc="Alumnos - pagos">
    Route::post("alumno/{id}/pagos", ["uses" => "AlumnoController@listarPagos", "as" => "alumnos.pagos.listar"]);
    Route::post("alumno/{id}/pago/actualizarEstado", ["uses" => "AlumnoController@actualizarEstadoPago", "as" => "alumnos.pagos.actualizar.estado"]);
    Route::post("alumno/{id}/pago/generarClases", ["uses" => "AlumnoController@generarClasesXPago", "as" => "alumnos.pagos.generarClases"]);
    Route::post("alumno/{id}/pago/docentesDisponibles", ["uses" => "AlumnoController@listarDocentesDisponiblesXPago", "as" => "alumnos.pagos.docentesDisponibles.listar"]);
    Route::post("alumno/{id}/pago/registrar", ["uses" => "AlumnoController@registrarPago", "as" => "alumnos.pagos.registrar"]);
    Route::post("alumno/{id}/pago/actualizar", ["uses" => "AlumnoController@actualizarPago", "as" => "alumnos.pagos.actualizar"]);
    Route::post("alumno/{id}/pago/{idPago}/datos", ["uses" => "AlumnoController@datosPago", "as" => "alumnos.pagos.datos"]);
    Route::delete("alumno/{id}/pago/{idPago}/eliminar", ["uses" => "AlumnoController@eliminarPago", "as" => "alumnos.pagos.eliminar"]);
    // </editor-fold>
    // <editor-fold desc="Alumnos - clases">
    Route::post("alumno/{id}/periodosClases", ["uses" => "AlumnoController@listarPeriodosClases", "as" => "alumnos.periodos.clases.listar"]);
    Route::post("alumno/{id}/periodo/{numeroPeriodo}/clases", ["uses" => "AlumnoController@listarClases", "as" => "alumnos.periodo.clases.listar"]);
    Route::post("alumno/{id}/clase/actualizarEstado", ["uses" => "AlumnoController@actualizarEstadoClase", "as" => "alumnos.clases.actualizar.estado"]);
    Route::post("alumno/{id}/clase/docentesDisponibles", ["uses" => "AlumnoController@listarDocentesDisponiblesXClase", "as" => "alumnos.clases.docentesDisponibles.listar"]);
    Route::post("alumno/{id}/clase/registrarActualizar", ["uses" => "AlumnoController@registrarActualizarClase", "as" => "alumnos.clases.registrar.actualizar"]);
    Route::post("alumno/{id}/clase/cancelar", ["uses" => "AlumnoController@cancelarClase", "as" => "alumnos.clases.cancelar"]);
    Route::post("alumno/{id}/clases/actualizar/grupo", ["uses" => "AlumnoController@actualizarClasesGrupo", "as" => "alumnos.clases.actualizar.grupo"]);
    Route::post("alumno/{id}/clase/{idClase}/datos", ["uses" => "AlumnoController@datosClase", "as" => "alumnos.clases.datos"]);
    Route::post("alumno/{id}/clases/datos/grupo", ["uses" => "AlumnoController@datosClasesGrupo", "as" => "alumnos.clases.datos.grupo"]);
    Route::delete("alumno/{id}/clase/{idClase}/eliminar", ["uses" => "AlumnoController@eliminarClase", "as" => "alumnos.clases.eliminar"]);
    // </editor-fold>
    // <editor-fold desc="Postulantes">
    Route::get("postulantes", ["uses" => "PostulanteController@index", "as" => "postulantes"]);
    Route::post("postulantes/listar", ["uses" => "PostulanteController@listar", "as" => "postulantes.listar"]);
    Route::get("postulante/nuevo", ["uses" => "PostulanteController@crear", "as" => "postulantes.crear"]);
    Route::post("postulante/registrar", ["uses" => "PostulanteController@registrar", "as" => "postulantes.registrar"]);
    Route::get("postulante/{id}/editar", ["uses" => "PostulanteController@editar", "as" => "postulantes.editar"]);
    Route::patch("postulante/{id}/actualizar", ["uses" => "PostulanteController@actualizar", "as" => "postulantes.actualizar"]);
    Route::post("postulante/{id}/actualizarEstado", ["uses" => "PostulanteController@actualizarEstado", "as" => "postulantes.actualizar.estado"]);
    Route::delete("postulante/{id}/eliminar", ["uses" => "PostulanteController@eliminar", "as" => "postulantes.eliminar"]);
    // </editor-fold>
    // <editor-fold desc="Profesores">
    Route::get("profesores", ["uses" => "ProfesorController@index", "as" => "profesores"]);
    Route::post("profesores/listar", ["uses" => "ProfesorController@listar", "as" => "profesores.listar"]);
    Route::get("profesor/nuevo", ["uses" => "ProfesorController@crear", "as" => "profesores.crear"]);
    Route::post("profesor/registrar", ["uses" => "ProfesorController@registrar", "as" => "profesores.registrar"]);
    Route::get("profesor/{id}/editar", ["uses" => "ProfesorController@editar", "as" => "profesores.editar"]);
    Route::patch("profesor/{id}/actualizar", ["uses" => "ProfesorController@actualizar", "as" => "profesores.actualizar"]);
    Route::post("profesor/{id}/actualizarEstado", ["uses" => "ProfesorController@actualizarEstado", "as" => "profesores.actualizar.estado"]);
    Route::post("profesor/{id}/actualizarHorario", ["uses" => "ProfesorController@actualizarHorario", "as" => "profesores.actualizar.horario"]);
    Route::get("profesor/{id}/perfil", ["uses" => "ProfesorController@perfil", "as" => "profesores.perfil"]);
    Route::delete("profesor/{id}/eliminar", ["uses" => "ProfesorController@eliminar", "as" => "profesores.eliminar"]);
    // </editor-fold>
    // <editor-fold desc="Profesores - pagos">    
    Route::post("profesor/{id}/pagos", ["uses" => "ProfesorController@listarPagos", "as" => "profesores.pagos.listar"]);
    Route::post("profesor/{id}/pago/actualizarEstado", ["uses" => "ProfesorController@actualizarEstadoPago", "as" => "profesores.pagos.actualizar.estado"]);
    Route::post("profesor/{id}/pago/registrar", ["uses" => "ProfesorController@registrarPago", "as" => "profesores.pagos.registrar"]);
    Route::post("profesor/{id}/pago/{idPago}/datos", ["uses" => "ProfesorController@datosPago", "as" => "profesores.pagos.datos"]);
    Route::delete("profesor/{id}/pago/{idPago}/eliminar", ["uses" => "ProfesorController@eliminarPago", "as" => "profesores.pagos.eliminar"]);
    // </editor-fold>
    // <editor-fold desc="Profesores - clases">
    Route::post("profesor/{id}/clases", ["uses" => "ProfesorController@listarClases", "as" => "profesores.clases.listar"]);
    Route::post("profesor/{id}/clases/pago/registrar", ["uses" => "ProfesorController@registrarPagoXClases", "as" => "profesores.clases.pagos.registrar"]);
    // </editor-fold>   
    // <editor-fold desc="Usuarios">
    Route::group(["middleware" => "verificacion.usuario:[" . RolesUsuario::Principal . "],true"], function() {
      Route::get("usuarios", ["uses" => "UsuarioController@index", "as" => "usuarios"]);
      Route::post("usuarios/listar", ["uses" => "UsuarioController@listar", "as" => "usuarios.listar"]);
      Route::get("usuario/nuevo", ["uses" => "UsuarioController@crear", "as" => "usuarios.crear"]);
      Route::post("usuario/registrar", ["uses" => "UsuarioController@registrar", "as" => "usuarios.registrar"]);
    });
    Route::get("usuario/{id}/editar", ["uses" => "UsuarioController@editar", "as" => "usuarios.editar"]);
    Route::patch("usuario/{id}/actualizar", ["uses" => "UsuarioController@actualizar", "as" => "usuarios.actualizar"]);
    Route::post("usuario/{id}/actualizarEstado", ["uses" => "UsuarioController@actualizarEstado", "as" => "usuarios.actualizar.estado"]);
    Route::delete("usuario/{id}/eliminar", ["uses" => "UsuarioController@eliminar", "as" => "usuarios.eliminar"]);
    // </editor-fold>
    // <editor-fold desc="Historial">
    Route::post("historial/{idEntidad}/perfil", ["uses" => "HistorialController@obtener", "as" => "historial.perfil"]);
    Route::post("historial/{idEntidad}/registrar", ["uses" => "HistorialController@registrar", "as" => "historial.registrar"]);
    // </editor-fold>
    // <editor-fold desc="Cursos">
    Route::get("cursos", ["uses" => "CursoController@index", "as" => "cursos"]);
    Route::post("cursos/listar", ["uses" => "CursoController@listar", "as" => "cursos.listar"]);
    Route::get("curso/nuevo", ["uses" => "CursoController@crear", "as" => "cursos.crear"]);
    Route::post("curso/registrar", ["uses" => "CursoController@registrar", "as" => "cursos.registrar"]);
    Route::get("curso/{id}/editar", ["uses" => "CursoController@editar", "as" => "cursos.editar"]);
    Route::patch("curso/{id}/actualizar", ["uses" => "CursoController@actualizar", "as" => "cursos.actualizar"]);
    Route::delete("curso/{id}/eliminar", ["uses" => "CursoController@eliminar", "as" => "cursos.eliminar"]);
    Route::post("curso/{id}/datos", ["uses" => "CursoController@datos", "as" => "cursos.datos"]);
    // </editor-fold>
    // <editor-fold desc="Reportes">
    Route::get("reporte/clases", ["uses" => "ReporteController@clases", "as" => "reporte.clases"]);
    Route::post("reporte/listar/clases", ["uses" => "ReporteController@listarClases", "as" => "reporte.listar.clases"]);
    Route::post("reporte/listar/clases/grafico", ["uses" => "ReporteController@listarClasesGrafico", "as" => "reporte.listar.clases.grafico"]);
    Route::get("reporte/pagos", ["uses" => "ReporteController@pagos", "as" => "reporte.pagos"]);
    Route::post("reporte/listar/pagos", ["uses" => "ReporteController@listarPagos", "as" => "reporte.listar.pagos"]);
    Route::post("reporte/listar/pagos/grafico", ["uses" => "ReporteController@listarPagosGrafico", "as" => "reporte.listar.pagos.grafico"]);
    // </editor-fold> 
  });
});
