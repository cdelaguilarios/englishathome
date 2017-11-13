<?php

namespace App\Helpers\Enum;

class TiposEntidad {

  const Alumno = "ALUMNO";
  const Interesado = "INTERESADO";
  const Profesor = "PROFESOR";
  const Postulante = "POSTULANTE";
  const Usuario = "USUARIO";
  const Curso = "CURSO";
  const Clase = "CLASE";
  const Pago = "PAGO";

  public static function listar() {
    return [
        TiposEntidad::Alumno => ["Alumnos", "Alumno", "Alumna", "alumnos.perfil", '<i class="fa fa-mortar-board"></i>'],
        TiposEntidad::Interesado => ["Interesados", "Interesado", "Interesada", "interesados.editar", '<i class="fa flaticon-questioning"></i>'],
        TiposEntidad::Profesor => ["Profesores", "Profesor", "Profesora", "profesores.perfil", '<i class="fa flaticon-teacher-with-stick"></i>'],
        TiposEntidad::Postulante => ["Postulantes", "Postulante", "Postulante", "postulantes.perfil", 'CV&nbsp;&nbsp;'],
        TiposEntidad::Usuario => ["Usuarios", "Usuario", "Usuaria", "usuarios.editar", '<i class="fa fa-users"></i>'],
        TiposEntidad::Curso => ["Cursos", "Curso", "Curso", "", '<i class="fa fa-book"></i>'],
        TiposEntidad::Clase => ["Clases", "Clase", "Clase", "", '<i class="fa flaticon-student-in-front-of-a-stack-of-books"></i>'],
        TiposEntidad::Pago => ["Pagos", "Pago", "Pago", "", '<i class="fa fa-dollar"></i>']
    ];
  }

  public static function listarTiposBase() {
    $tipos = TiposEntidad::listar();
    $tiposBase = [TiposEntidad::Alumno, TiposEntidad::Interesado, TiposEntidad::Profesor, TiposEntidad::Postulante, TiposEntidad::Usuario];
    $tiposSel = [];
    foreach ($tiposBase as $tipoBase) {
      $tiposSel[$tipoBase] = $tipos[$tipoBase];
    }
    return $tiposSel;
  }

  public static function listarTiposDocente() {
    $tipos = TiposEntidad::listar();
    $tiposDocente = [TiposEntidad::Profesor, TiposEntidad::Postulante];
    $tiposSel = [];
    foreach ($tiposDocente as $tipoDocente) {
      $tiposSel[$tipoDocente] = $tipos[$tipoDocente][0];
    }
    return $tiposSel;
  }

  public static function listarSeccionCorreos() {
    return [
        TiposEntidad::Alumno => "Todos los alumnos",
        TiposEntidad::Interesado => "Todos los interesados",
        TiposEntidad::Profesor => "Todos los profesores",
        TiposEntidad::Postulante => "Todos los postulantes",
        TiposEntidad::Usuario => "Todos los usuarios"
    ];
  }

}
