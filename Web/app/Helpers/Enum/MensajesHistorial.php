<?php

namespace App\Helpers\Enum;

class MensajesHistorial {

    const TituloAlumnoRegistro = "[" . TiposEntidad::Alumno . "] se registró como nuevo alumno(a)";
    const TituloAlumnoRegistroXUsuario = "[" . TiposEntidad::Usuario . "] registró a [" . TiposEntidad::Alumno . "] como nuevo alumno(a)";
    
    const TituloAlumnoRegistroPago = "[" . TiposEntidad::Usuario . "] registró un pago del alumno [" . TiposEntidad::Alumno . "]";
    const MensajeAlumnoRegistroPago = "<strong>Motivo:</strong> [MOTIVO]<br/><strong>Monto:</strong> S/.[MONTO] [DESCRIPCION]<br/>";
        
    const TituloProfesorRegistroPago = "[" . TiposEntidad::Usuario . "] registró un pago del alumno [" . TiposEntidad::Profesor . "]";
    const MensajeProfesorRegistroPago = "<strong>Motivo:</strong> [MOTIVO]<br/><strong>Monto:</strong> S/.[MONTO] [DESCRIPCION]<br/>";
    
    const TituloCorreoAlumnoClase = "Estamos a [DIAS] de la clase del alumno [" . TiposEntidad::Alumno . "]";
    const MensajeCorreoAlumnoClase = "La próxima clase del alumno [" . TiposEntidad::Alumno . "] será el [FECHA]. Los datos principales de esta clase son:<br/><br/><strong>Período:</strong> [PERIODO]<br/><strong>Profesor asignado:</strong> [PROFESOR]<br/><strong>Duración:</strong> [DURACION]<br/>";
       
    const TituloCorreoAlumnoClaseSinProfesor = "Importante - Estamos a [DIAS] de la clase del alumno [" . TiposEntidad::Alumno . "] y no tiene profesor asignado";
    const MensajeCorreoAlumnoClaseSinProfesor = "La próxima clase del alumno [" . TiposEntidad::Alumno . "] será el [FECHA]. Los datos principales de esta clase son:<br/><br/><strong>Período:</strong> [PERIODO]<br/><strong>Duración:</strong> [DURACION]<br/><br/><span style='color:red'>Por favor considerar que está clase no tiene un profesor asignado</span>";

}
