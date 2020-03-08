<?php

use App\Helpers\Enum\RolesUsuario;

// <editor-fold desc="Sesión">
Route::get("iniciar_sesion", ["uses" => "Auth\AuthController@getLogin", "as" => "auth.login"]); //--
Route::post("iniciar_sesion", ["uses" => "Auth\AuthController@postLogin", "as" => "auth.login"]); //--
Route::get("cerrar_sesion", ["uses" => "Auth\AuthController@getLogout", "as" => "auth.logout"]); //--
Route::get("restablecimientoContrasenia/{token?}", ["uses" => "Auth\PasswordController@showResetForm", "as" => "auth.password.reset"]); //--
Route::post("restablecimientoContrasenia/correoElectronico", ["uses" => "Auth\PasswordController@sendResetLinkEmail", "as" => "auth.password.email"]); //--
Route::post("restablecimientoContrasenia", ["uses" => "Auth\PasswordController@reset", "as" => "auth.password.reset"]); //--
// </editor-fold> 
// <editor-fold desc="Interesados registro externo (desde la web)">
Route::post("interesado/registrar/externo", ["uses" => "InteresadoController@registrarExterno", "as" => "interesados.registrar.externo"]); //--
// </editor-fold> 
// <editor-fold desc="Alumnos registro externo (ficha de inscripción)">
Route::get("alumno/nuevo/{codigoVerificacion}", ["uses" => "AlumnoController@crearExterno", "as" => "alumnos.crear.externo"]); //--
Route::post("alumno/registrar/externo", ["uses" => "AlumnoController@registrarExterno", "as" => "alumnos.registrar.externo"]); //--
// </editor-fold> 
// <editor-fold desc="Postulantes registro externo">
Route::get("postulante/nuevo/externo", ["uses" => "PostulanteController@crearExterno", "as" => "postulantes.crear.externo"]); //--
Route::post("postulante/registrar/externo", ["uses" => "PostulanteController@registrarExterno", "as" => "postulantes.registrar.externo"]); //--
// </editor-fold> 

Route::get("cron/enviarCorreos", ["uses" => "CronController@enviarCorreos", "as" => "cron.enviar.correos"]);
Route::get("cron/sincronizarEstados", ["uses" => "CronController@sincronizarEstados", "as" => "cron.sincronizar.estados"]);

// <editor-fold desc="Archivos">
Route::post("archivos", ["uses" => "ArchivoController@registrar", "as" => "archivos.reqistrar"]); //--
Route::get("archivos/{nombre}", ["uses" => "ArchivoController@obtener", "as" => "archivos"]); //--
Route::delete("archivos/eliminar", ["uses" => "ArchivoController@eliminar", "as" => "archivos.eliminar"]); //--
// </editor-fold> 
// <editor-fold desc="Ubigeo"> 
Route::post("ubigeo/listarDepartamentos", ["uses" => "UbigeoController@listarDepartamentos", "as" => "ubigeo.listarDepartamentos"]); //--
Route::post("ubigeo/listarProvincias/{codigoDepartamento}", ["uses" => "UbigeoController@listarProvincias", "as" => "ubigeo.listarProvincias"]); //--
Route::post("ubigeo/listarDistritos/{codigoProvincia}", ["uses" => "UbigeoController@listarDistritos", "as" => "ubigeo.listarDistritos"]); //--
// </editor-fold> 

Route::group(["middleware" => "auth"], function() {
  Route::group(["middleware" => "verificacion.usuario:*"], function() {
    Route::get("/", ["uses" => "InicioController@inicio", "as" => "/"]);
    // <editor-fold desc="Usuarios">
    Route::get("usuario/{id}/editar", ["uses" => "UsuarioController@editar", "as" => "usuarios.editar"]); //--
    Route::patch("usuario/{id}/actualizar", ["uses" => "UsuarioController@actualizar", "as" => "usuarios.actualizar"]); //--
    // </editor-fold>
  });
  Route::group(["middleware" => "verificacion.usuario:[" . RolesUsuario::Profesor . "]"], function() {
    // <editor-fold desc="Clases">
    Route::get("misAlumnos", ["uses" => "ProfesorController@misAlumnos", "as" => "profesores.mis.alumnos"]); //--
    Route::get("misAlumnos/{id}/clases", ["uses" => "ProfesorController@misAlumnosClases", "as" => "profesores.mis.alumnos.clases"]);
    Route::post("misAlumnos/{id}/clases/listar", ["uses" => "ProfesorController@misAlumnosListarClases", "as" => "profesores.mis.alumnos.listar.clases"]); //--
    Route::post("misAlumnos/{id}/clases/avance", ["uses" => "ProfesorController@misAlumnosRegistrarAvanceClase", "as" => "profesores.mis.alumnos.registrar.avance.clase"]); //--
    Route::post("misAlumnos/{id}/clases/confirmar", ["uses" => "ProfesorController@misAlumnosConfirmarClase", "as" => "profesores.mis.alumnos.confirmar.clase"]);
    // </editor-fold>
  });
  Route::group(["middleware" => "verificacion.usuario:[" . RolesUsuario::Alumno . "]"], function() {
    // <editor-fold desc="Clases">
    Route::get("misClases", ["uses" => "AlumnoController@misClases", "as" => "alumnos.mis.clases"]);
    Route::post("misClases/listar", ["uses" => "AlumnoController@listarMisClases", "as" => "listar.mis.clases"]);
    Route::put("misClases/comentarios", ["uses" => "AlumnoController@actualizarComentariosClase", "as" => "alumnos.mis.clases.actualizar.comentarios"]);
    // </editor-fold>
  });
  Route::group(["middleware" => "verificacion.usuario:[" . RolesUsuario::Principal . "|" . RolesUsuario::Secundario . "]"], function() {
    // <editor-fold desc="Entidades">
    Route::get("entidades/{id}/perfil", ["uses" => "EntidadController@perfil", "as" => "entidades.perfil"]); //--
    Route::post("entidades/{id}/actualizarComentariosAdministrador", ["uses" => "EntidadController@actualizarComentariosAdministrador", "as" => "entidades.actualizar.comentarios.administrador"]); //--
    Route::post("entidades/{id}/actualizarCredencialesAcceso", ["uses" => "EntidadController@actualizarCredencialesAcceso", "as" => "entidades.actualizar.credenciales.acceso"]); //--
    Route::post("entidades/{id}/actualizarImagenPerfil", ["uses" => "EntidadController@actualizarImagenPerfil", "as" => "entidades.actualizar.imagen.perfil"]); //--
    // </editor-fold>
    // <editor-fold desc="Interesados">
    Route::get("interesados", ["uses" => "InteresadoController@index", "as" => "interesados"]); //--
    Route::post("interesados/listar", ["uses" => "InteresadoController@listar", "as" => "interesados.listar"]); //--
    Route::get("interesados/buscar", ["uses" => "InteresadoController@buscar", "as" => "interesados.buscar"]); //--
    Route::get("interesado/nuevo", ["uses" => "InteresadoController@crear", "as" => "interesados.crear"]); //--
    Route::post("interesado/registrar", ["uses" => "InteresadoController@registrar", "as" => "interesados.registrar"]); //--
    Route::get("interesado/{id}/editar", ["uses" => "InteresadoController@editar", "as" => "interesados.editar"]); //--
    Route::patch("interesado/{id}/actualizar", ["uses" => "InteresadoController@actualizar", "as" => "interesados.actualizar"]); //--
    Route::post("interesado/{id}/actualizarEstado", ["uses" => "InteresadoController@actualizarEstado", "as" => "interesados.actualizar.estado"]); //--
    Route::get("interesado/{id}/cotizar", ["uses" => "InteresadoController@cotizar", "as" => "interesados.cotizar"]); //--
    Route::post("interesado/{id}/enviarCotizacion", ["uses" => "InteresadoController@enviarCotizacion", "as" => "interesados.enviar.cotizacion"]); //--
    Route::get("interesado/{id}/perfilAlumno", ["uses" => "InteresadoController@perfilAlumno", "as" => "interesados.perfil.alumno"]); //--
    Route::delete("interesado/{id}/eliminar", ["uses" => "InteresadoController@eliminar", "as" => "interesados.eliminar"]); //--
    // </editor-fold>
    // <editor-fold desc="Alumnos">
    Route::get("alumnos", ["uses" => "AlumnoController@index", "as" => "alumnos"]); //--
    Route::post("alumnos/listar", ["uses" => "AlumnoController@listar", "as" => "alumnos.listar"]); //--
    Route::get("alumnos/buscar", ["uses" => "AlumnoController@buscar", "as" => "alumnos.buscar"]); //--
    Route::get("alumno/nuevo", ["uses" => "AlumnoController@crear", "as" => "alumnos.crear"]); //--
    Route::post("alumno/registrar", ["uses" => "AlumnoController@registrar", "as" => "alumnos.registrar"]); //--
    Route::get("alumno/{id}/editar", ["uses" => "AlumnoController@editar", "as" => "alumnos.editar"]); //--
    Route::patch("alumno/{id}/actualizar", ["uses" => "AlumnoController@actualizar", "as" => "alumnos.actualizar"]); //--
    Route::post("alumno/{id}/actualizarEstado", ["uses" => "AlumnoController@actualizarEstado", "as" => "alumnos.actualizar.estado"]); //--
    Route::post("alumno/{id}/actualizarHorario", ["uses" => "AlumnoController@actualizarHorario", "as" => "alumnos.actualizar.horario"]); //--
    Route::post("alumno/{id}/actualizarProfesor/{idDocente}", ["uses" => "AlumnoController@actualizarProfesor", "as" => "alumnos.actualizar.profesor"]); //--
    Route::get("alumno/{id}/perfil", ["uses" => "AlumnoController@perfil", "as" => "alumnos.perfil"]); //--
    Route::get("alumno/{id}/ficha", ["uses" => "AlumnoController@ficha", "as" => "alumnos.ficha"]); //--
    Route::delete("alumno/{id}/eliminar", ["uses" => "AlumnoController@eliminar", "as" => "alumnos.eliminar"]); //--
    // <editor-fold desc="Alumnos - pagos">
    Route::post("alumno/{id}/pagos", ["uses" => "AlumnoController@listarPagos", "as" => "alumnos.pagos.listar"]); //--
    Route::post("alumno/{id}/pago/registrarActualizar", ["uses" => "AlumnoController@registrarActualizarPago", "as" => "alumnos.pagos.registrar.actualizar"]); //--
    Route::post("alumno/{id}/pago/{idPago}/actualizarEstado", ["uses" => "AlumnoController@actualizarEstadoPago", "as" => "alumnos.pagos.actualizar.estado"]); //--
    Route::post("alumno/{id}/pago/{idPago}/datos", ["uses" => "AlumnoController@obtenerDatosPago", "as" => "alumnos.pagos.datos"]); //--
    Route::delete("alumno/{id}/pago/{idPago}/eliminar", ["uses" => "AlumnoController@eliminarPago", "as" => "alumnos.pagos.eliminar"]); //--
    // </editor-fold>
    // <editor-fold desc="Alumnos - clases">
    Route::post("alumno/{id}/clases", ["uses" => "AlumnoController@listarClases", "as" => "alumnos.clases.listar"]); //--
    Route::post("alumno/{id}/clases/confirmar", ["uses" => "AlumnoController@confirmarClase", "as" => "alumnos.clases.confirmar"]); //--    
    Route::delete("alumno/{id}/clase/{idClase}/eliminar", ["uses" => "AlumnoController@eliminarClase", "as" => "alumnos.clases.eliminar"]); //--
    Route::post("alumno/{id}/clase/{idClase}/datos", ["uses" => "AlumnoController@obtenerDatosClase", "as" => "alumnos.clases.datos"]);
    // <editor-fold desc="TODO: ELIMINAR">
    Route::post("alumno/{id}/periodosClases", ["uses" => "AlumnoController@listarPeriodosClases", "as" => "alumnos.periodos.clases.listar"]);
    Route::post("alumno/{id}/periodo/{numeroPeriodo}/clases", ["uses" => "AlumnoController@listarClasesXPeriodo", "as" => "alumnos.periodo.clases.listar"]);
    Route::post("alumno/{id}/clase/actualizarEstado", ["uses" => "AlumnoController@actualizarEstadoClase", "as" => "alumnos.clases.actualizar.estado"]);
    Route::post("alumno/{id}/clase/docentesDisponibles", ["uses" => "AlumnoController@listarDocentesDisponiblesXClase", "as" => "alumnos.clases.docentesDisponibles.listar"]);
    Route::post("alumno/{id}/clase/registrarActualizar", ["uses" => "AlumnoController@registrarActualizarClase", "as" => "alumnos.clases.registrar.actualizar"]);
    Route::post("alumno/{id}/clase/cancelar", ["uses" => "AlumnoController@cancelarClase", "as" => "alumnos.clases.cancelar"]);
    Route::post("alumno/{id}/clases/actualizar/grupo", ["uses" => "AlumnoController@actualizarClasesGrupo", "as" => "alumnos.clases.actualizar.grupo"]);
    Route::post("alumno/{id}/clases/datos/grupo", ["uses" => "AlumnoController@datosClasesGrupo", "as" => "alumnos.clases.datos.grupo"]);
    Route::post("alumno/{id}/clases/total/horario", ["uses" => "AlumnoController@totalClasesXHorario", "as" => "alumnos.clases.total.horario"]);
    Route::get("alumno/{id}/clases/descargarLista", ["uses" => "AlumnoController@descargarLista", "as" => "alumnos.clases.descargar.lista"]);
    // </editor-fold>
    // </editor-fold>
    // </editor-fold>
    // <editor-fold desc="Postulantes">
    Route::get("postulantes", ["uses" => "PostulanteController@index", "as" => "postulantes"]); //--
    Route::post("postulantes/listar", ["uses" => "PostulanteController@listar", "as" => "postulantes.listar"]); //--
    Route::get("postulantes/buscar", ["uses" => "PostulanteController@buscar", "as" => "postulantes.buscar"]); //--
    Route::get("postulante/nuevo", ["uses" => "PostulanteController@crear", "as" => "postulantes.crear"]); //--
    Route::post("postulante/registrar", ["uses" => "PostulanteController@registrar", "as" => "postulantes.registrar"]); //--
    Route::get("postulante/{id}/editar", ["uses" => "PostulanteController@editar", "as" => "postulantes.editar"]); //--
    Route::patch("postulante/{id}/actualizar", ["uses" => "PostulanteController@actualizar", "as" => "postulantes.actualizar"]); //--
    Route::post("postulante/{id}/actualizarEstado", ["uses" => "PostulanteController@actualizarEstado", "as" => "postulantes.actualizar.estado"]); //--
    Route::post("postulante/{id}/actualizarHorario", ["uses" => "PostulanteController@actualizarHorario", "as" => "postulantes.actualizar.horario"]); //--
    Route::get("postulante/{id}/perfil", ["uses" => "PostulanteController@perfil", "as" => "postulantes.perfil"]); //--
    Route::get("postulante/{id}/perfilProfesor", ["uses" => "PostulanteController@perfilProfesor", "as" => "postulantes.perfil.profesor"]); //--
    Route::delete("postulante/{id}/eliminar", ["uses" => "PostulanteController@eliminar", "as" => "postulantes.eliminar"]); //--
    // </editor-fold>
    // <editor-fold desc="Profesores">
    Route::get("profesores", ["uses" => "ProfesorController@index", "as" => "profesores"]); //--
    Route::post("profesores/listar", ["uses" => "ProfesorController@listar", "as" => "profesores.listar"]); //--
    Route::get("profesores/buscar", ["uses" => "ProfesorController@buscar", "as" => "profesores.buscar"]); //--
    Route::get("profesor/nuevo", ["uses" => "ProfesorController@crear", "as" => "profesores.crear"]); //--
    Route::post("profesor/registrar", ["uses" => "ProfesorController@registrar", "as" => "profesores.registrar"]); //--
    Route::get("profesor/{id}/editar", ["uses" => "ProfesorController@editar", "as" => "profesores.editar"]); //--
    Route::patch("profesor/{id}/actualizar", ["uses" => "ProfesorController@actualizar", "as" => "profesores.actualizar"]); //--
    Route::post("profesor/{id}/actualizarEstado", ["uses" => "ProfesorController@actualizarEstado", "as" => "profesores.actualizar.estado"]); //--
    Route::post("profesor/{id}/actualizarHorario", ["uses" => "ProfesorController@actualizarHorario", "as" => "profesores.actualizar.horario"]); //--
    Route::post("profesor/{id}/actualizarComentariosPerfil", ["uses" => "ProfesorController@actualizarComentariosPerfil", "as" => "profesores.actualizar.comentarios.perfil"]); //--
    Route::get("profesor/{id}/perfil", ["uses" => "ProfesorController@perfil", "as" => "profesores.perfil"]); //--
    Route::get("profesor/{id}/ficha", ["uses" => "ProfesorController@ficha", "as" => "profesores.ficha"]); //--
    Route::get("profesor/{id}/fichaAlumno", ["uses" => "ProfesorController@fichaAlumno", "as" => "profesores.ficha.alumno"]); //--
    Route::get("profesor/{id}/descargarFicha", ["uses" => "ProfesorController@descargarFicha", "as" => "profesores.descargar.ficha"]);
    Route::delete("profesor/{id}/eliminar", ["uses" => "ProfesorController@eliminar", "as" => "profesores.eliminar"]); //--    
    // <editor-fold desc="Profesores - pagos">    
    Route::post("profesor/{id}/pagos", ["uses" => "ProfesorController@listarPagos", "as" => "profesores.pagos.listar"]); //--
    Route::post("profesor/{id}/pagoGeneral/registrar", ["uses" => "ProfesorController@registrarPagoGeneral", "as" => "profesores.pagos.generales.registrar"]); //--
    Route::post("profesor/{id}/pagoGeneral/actualizar", ["uses" => "ProfesorController@actualizarPagoGeneral", "as" => "profesores.pagos.generales.actualizar"]); //--
    Route::post("profesor/{id}/pagoGeneral/{idPago}/actualizarEstado", ["uses" => "ProfesorController@actualizarEstadoPagoGeneral", "as" => "profesores.pagos.generales.actualizar.estado"]); //--
    Route::post("profesor/{id}/pago/{idPago}/datos", ["uses" => "ProfesorController@obtenerDatosPago", "as" => "profesores.pagos.datos"]); //--
    Route::delete("profesor/{id}/pago/{idPago}/eliminar", ["uses" => "ProfesorController@eliminarPago", "as" => "profesores.pagos.eliminar"]); //--
    // </editor-fold>
    // <editor-fold desc="Profesores - clases">
    Route::post("profesor/{id}/clases", ["uses" => "ProfesorController@listarClases", "as" => "profesores.clases.listar"]); //--
    // <editor-fold desc="TODO: ELIMINAR">
    Route::post("profesor/{id}/clases/pago/registrar", ["uses" => "ProfesorController@registrarPagoXClases", "as" => "profesores.clases.pagos.registrar"]);
    // </editor-fold>
    // </editor-fold> 
    // </editor-fold>  
    // <editor-fold desc="Docentes">
    Route::get("docentes/disponibles", ["uses" => "DocenteController@disponibles", "as" => "docentes.disponibles"]); //--
    Route::post("docentes/disponibles/listar", ["uses" => "DocenteController@listarDisponibles", "as" => "docentes.disponibles.listar"]); //--
    Route::get("docentes/pagosXClases", ["uses" => "DocenteController@pagosXClases", "as" => "docentes.pagosXClases"]); //--
    Route::post("docentes/pagosXClases/listar", ["uses" => "DocenteController@listarPagosXClases", "as" => "docentes.pagosXClases.listar"]); //--    
    Route::post("docente/{id}/pagosXClases/listarDetalle", ["uses" => "DocenteController@listarPagosXClasesDetalle", "as" => "docentes.pagosXClases.listarDetalle"]); //--  
    Route::post("docentes/pagoXClases/registrarActualizar", ["uses" => "DocenteController@registrarActualizarPagoXClases", "as" => "docentes.pagosXClases.registrarActualizar"]); //--    
    Route::delete("docente/{id}/pago/{idPago}/eliminar", ["uses" => "DocenteController@eliminarPagoXClases", "as" => "docentes.pagosXClases.eliminar"]); //--
    Route::patch("docente/{id}/actualizarExperienciaLaboral", ["uses" => "DocenteController@actualizarExperienciaLaboral", "as" => "docentes.actualizar.experiencia.laboral"]); //--
    // </editor-fold>
    // <editor-fold desc="Usuarios">
    Route::group(["middleware" => "verificacion.usuario:[" . RolesUsuario::Principal . "],true"], function() {
      Route::get("usuarios", ["uses" => "UsuarioController@index", "as" => "usuarios"]); //--
      Route::post("usuarios/listar", ["uses" => "UsuarioController@listar", "as" => "usuarios.listar"]); //--
      Route::get("usuarios/buscar", ["uses" => "UsuarioController@buscar", "as" => "usuarios.buscar"]); //--
      Route::get("usuario/nuevo", ["uses" => "UsuarioController@crear", "as" => "usuarios.crear"]); //--
      Route::post("usuario/registrar", ["uses" => "UsuarioController@registrar", "as" => "usuarios.registrar"]); //--
      Route::post("usuario/{id}/actualizarEstado", ["uses" => "UsuarioController@actualizarEstado", "as" => "usuarios.actualizar.estado"]); //--
      Route::delete("usuario/{id}/eliminar", ["uses" => "UsuarioController@eliminar", "as" => "usuarios.eliminar"]); //--
    });
    // </editor-fold>
    // <editor-fold desc="Clases">
    Route::post("clase/{id}/actualizarEstado", ["uses" => "ClaseController@actualizarEstado", "as" => "clases.actualizar.estado"]); //--
    Route::post("clase/actualizarComentarios", ["uses" => "ClaseController@actualizarComentarios", "as" => "clases.actualizar.comentarios"]); //--
    // </editor-fold>
    // <editor-fold desc="Historial">
    Route::post("historial/{idEntidad}/perfil", ["uses" => "HistorialController@obtener", "as" => "historial.perfil"]);
    Route::post("historial/{idEntidad}/registrar", ["uses" => "HistorialController@registrar", "as" => "historial.registrar"]);
    Route::post("notificaciones/nuevas", ["uses" => "HistorialController@listarNuevasNotificaciones", "as" => "historial.notificaciones.nuevas"]);
    Route::post("notificaciones/nuevas/revisar", ["uses" => "HistorialController@revisarNuevasNotificaciones", "as" => "historial.notificaciones.nuevas.revisar"]);
    Route::get("notificaciones", ["uses" => "HistorialController@listarNotificaciones", "as" => "historial.notificaciones"]);
    Route::post("notificaciones", ["uses" => "HistorialController@listarNotificaciones", "as" => "historial.notificaciones"]);
    // </editor-fold>
    // <editor-fold desc="Notificaciones">
    Route::post("notificaciones/listar", ["uses" => "NotificacionController@listar", "as" => "notificaciones.listar"]);
    Route::post("notificaciones/listarNuevas", ["uses" => "NotificacionController@listarNuevas", "as" => "notificaciones.listar.nuevas"]);
    Route::post("notificaciones/{idEntidad}/listarHistorial", ["uses" => "NotificacionController@listarHistorial", "as" => "notificaciones.listar.historial"]);
    Route::post("notificaciones/registrarActualizar", ["uses" => "NotificacionController@registrarActualizar", "as" => "notificaciones.registrar.actualizar"]);
    Route::post("notificaciones/{id}/datos", ["uses" => "NotificacionController@obtenerDatos", "as" => "notificaciones.datos"]);
    Route::post("notificaciones/revisarMultiple", ["uses" => "NotificacionController@revisarMultiple", "as" => "notificaciones.revisar.multiple"]);
    Route::delete("notificaciones/{id}/eliminar", ["uses" => "NotificacionController@eliminar", "as" => "notificaciones.eliminar"]);
    // </editor-fold>
    // <editor-fold desc="Tareas">
    Route::post("tareas/listar", ["uses" => "TareaController@listar", "as" => "tareas.listar"]);
    Route::post("tareas/listarNuevas", ["uses" => "TareaController@listarNuevas", "as" => "tareas.listar.nuevas"]);
    Route::post("tareas/{idEntidad}/registrarActualizar", ["uses" => "TareaController@registrarActualizar", "as" => "tareas.registrar.actualizar"]);
    Route::post("tareas/{id}/datos", ["uses" => "TareaController@obtenerDatos", "as" => "tareas.datos"]);
    Route::delete("tareas/{id}/eliminar", ["uses" => "TareaController@eliminar", "as" => "tareas.eliminar"]);
    
    
    Route::post("tareas/{id}/actualizarRealizacion", ["uses" => "TareaController@actualizarRealizacion", "as" => "tareas.actualizar.realizacion"]);
    // </editor-fold>
    // <editor-fold desc="Horario">
    Route::post("horarios", ["uses" => "HorarioController@obtenerMultiple", "as" => "horario.multiple"]); //--
    // </editor-fold>
    // <editor-fold desc="Calendario">
    Route::get("calendario", ["uses" => "CalendarioController@index", "as" => "calendario"]); //--
    Route::post("calendario/datos", ["uses" => "CalendarioController@datos", "as" => "calendario.datos"]); //--
    // </editor-fold>
    // <editor-fold desc="Cursos">
    Route::get("cursos", ["uses" => "CursoController@index", "as" => "cursos"]); //--
    Route::post("cursos/listar", ["uses" => "CursoController@listar", "as" => "cursos.listar"]); //--
    Route::get("cursos/buscar", ["uses" => "CursoController@buscar", "as" => "cursos.buscar"]); //--
    Route::get("curso/nuevo", ["uses" => "CursoController@crear", "as" => "cursos.crear"]); //--
    Route::post("curso/registrar", ["uses" => "CursoController@registrar", "as" => "cursos.registrar"]); //--
    Route::get("curso/{id}/editar", ["uses" => "CursoController@editar", "as" => "cursos.editar"]); //--
    Route::patch("curso/{id}/actualizar", ["uses" => "CursoController@actualizar", "as" => "cursos.actualizar"]); //--
    Route::delete("curso/{id}/eliminar", ["uses" => "CursoController@eliminar", "as" => "cursos.eliminar"]); //--
    Route::post("curso/{id}/datos", ["uses" => "CursoController@datos", "as" => "cursos.datos"]); //--
    // </editor-fold>
    // <editor-fold desc="Reportes">
    Route::get("reportes", ["uses" => "ReporteController@index", "as" => "reportes"]);
    Route::post("reportes/listar", ["uses" => "ReporteController@listar", "as" => "reportes.listar"]);
    Route::get("reportes/nuevo", ["uses" => "ReporteController@crear", "as" => "reportes.crear"]);
    Route::post("reportes/registrar", ["uses" => "ReporteController@registrar", "as" => "reportes.registrar"]);
    Route::get("reportes/{id}/editar", ["uses" => "ReporteController@editar", "as" => "reportes.editar"]);
    Route::patch("reportes/{id}/actualizar", ["uses" => "ReporteController@actualizar", "as" => "reportes.actualizar"]);
    Route::delete("reportes/{id}/eliminar", ["uses" => "ReporteController@eliminar", "as" => "reportes.eliminar"]);
    Route::post("reportes/listarCampos", ["uses" => "ReporteController@listarCampos", "as" => "reportes.listar.campos"]);
    Route::post("reportes/listarEntidadesRelacionadas", ["uses" => "ReporteController@listarEntidadesRelacionadas", "as" => "reportes.listar.entidades.relacionadas"]);

    Route::get("reporte/clases", ["uses" => "ReporteController@clases", "as" => "reporte.clases"]);
    Route::post("reporte/listar/clases", ["uses" => "ReporteController@listarClases", "as" => "reporte.listar.clases"]);
    Route::post("reporte/listar/clases/grafico", ["uses" => "ReporteController@listarClasesGrafico", "as" => "reporte.listar.clases.grafico"]);
    Route::get("reporte/pagos", ["uses" => "ReporteController@pagos", "as" => "reporte.pagos"]);
    Route::post("reporte/listar/pagos", ["uses" => "ReporteController@listarPagos", "as" => "reporte.listar.pagos"]);
    Route::post("reporte/listar/pagos/grafico", ["uses" => "ReporteController@listarPagosGrafico", "as" => "reporte.listar.pagos.grafico"]);
    // </editor-fold> 
    // <editor-fold desc="Correos">
    Route::get("correos", ["uses" => "CorreoController@index", "as" => "correos"]); //--
    Route::post("correos/entidades", ["uses" => "CorreoController@listarEntidades", "as" => "correos.entidades"]); //--
    Route::post("correos/registrar", ["uses" => "CorreoController@registrarCorreos", "as" => "correos.registrar"]);
    // </editor-fold>
    // <editor-fold desc="Configuración">
    Route::get("configuracion", ["uses" => "ConfiguracionController@index", "as" => "configuracion"]);
    Route::post("configuracion/actualizar", ["uses" => "ConfiguracionController@actualizar", "as" => "configuracion.actualizar"]);
    // </editor-fold>
  });
});
