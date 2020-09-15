<?php

namespace App\Helpers\Enum;

class MensajesNotificacion {
  //Interesados
  const TituloInteresadoRegistro = "[" . TiposEntidad::Interesado . "] se registró como nuevo interesado(a)";
  const TituloInteresadoRegistroXUsuario = "[" . TiposEntidad::Usuario . "] registró a [" . TiposEntidad::Interesado . "] como nuevo interesado(a)";
  const TituloInteresadoEnvioCorreoCotizacion = "[" . TiposEntidad::Usuario . "] envió un correo de cotización a [" . TiposEntidad::Interesado . "]";
  const TituloInteresadoRegistroAlumno = "[" . TiposEntidad::Interesado . "] lleno la ficha de inscripción y se registró como nuevo alumno(a)";
  const TituloInteresadoRegistroAlumnoXUsuario = "[" . TiposEntidad::Usuario . "] registró a [" . TiposEntidad::Interesado . "] como nuevo alumno(a)";
  //Alumnos
  const TituloAlumnoRegistro = "[" . TiposEntidad::Alumno . "] lleno la ficha de inscripción y se registró como nuevo alumno(a)";
  const TituloAlumnoRegistroXUsuario = "[" . TiposEntidad::Usuario . "] registró a [" . TiposEntidad::Alumno . "] como nuevo alumno(a)";
  //Alumnos - pagos
  const TituloAlumnoRegistroPago = "[" . TiposEntidad::Usuario . "] registró un pago del alumno(a) [" . TiposEntidad::Alumno . "]";
  const MensajeAlumnoRegistroPago = "<strong>Motivo:</strong> [MOTIVO]<br/><strong>Monto:</strong> S/.[MONTO] [DESCRIPCION]<br/>";
  //Profesores
  const TituloProfesorRegistroXUsuario = "[" . TiposEntidad::Usuario . "] registró a [" . TiposEntidad::Profesor . "] como nuevo profesor(a)";
  //Profesores - pagos
  const TituloProfesorRegistroPago = "[" . TiposEntidad::Usuario . "] registró un pago del profesor(a) [" . TiposEntidad::Profesor . "]";
  const MensajeProfesorRegistroPago = "<strong>Motivo:</strong> [MOTIVO]<br/><strong>Monto:</strong> S/.[MONTO] [DESCRIPCION]<br/>";
  //Postulantes
  const TituloPostulanteRegistro = "[" . TiposEntidad::Postulante . "] se registró como nuevo postulante";
  const TituloPostulanteRegistroXUsuario = "[" . TiposEntidad::Usuario . "] registró a [" . TiposEntidad::Postulante . "] como nuevo postulante";
  const TituloPostulanteRegistroProfesorXUsuario = "[" . TiposEntidad::Usuario . "] registró a [" . TiposEntidad::Postulante . "] como nuevo profesor(a)";
  //Alumnos - clases
  const TituloCorreoAlumnoClase = "Estamos a [DIAS] de la clase del alumno(a) [" . TiposEntidad::Alumno . "]";
  const MensajeCorreoAlumnoClase = "La próxima clase del alumno(a) [" . TiposEntidad::Alumno . "] será el [FECHA]. Los datos principales de esta clase son:<br/><br/><strong>Período:</strong> [PERIODO]<br/><strong>Profesor asignado:</strong> [PROFESOR]<br/><strong>Duración:</strong> [DURACION]<br/>";
  const TituloCorreoAlumnoClaseSinProfesor = "Importante - Estamos a [DIAS] de la clase del alumno(a) [" . TiposEntidad::Alumno . "] y no tiene profesor(a) asignado";
  const MensajeCorreoAlumnoClaseSinProfesor = 'La próxima clase del alumno(a) [' . TiposEntidad::Alumno . '] será el [FECHA]. Los datos principales de esta clase son:<br/><br/><strong>Período:</strong> [PERIODO]<br/><strong>Duración:</strong> [DURACION]<br/><br/><span style="color:red">Por favor considerar que está clase no tiene un profesor(a) asignado</span>';

}
