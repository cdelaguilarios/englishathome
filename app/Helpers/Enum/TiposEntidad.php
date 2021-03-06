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
        TiposEntidad::Interesado => ["Interesados", "Interesado(a)", "Interesado", "Interesada", "interesados.editar", route("interesados.buscar"), '<i class="fa flaticon-questioning"></i>'],
        TiposEntidad::Alumno => ["Alumnos", "Alumno(a)", "Alumno", "Alumna", "alumnos.perfil", route("alumnos.buscar"), '<i class="fa fa-mortar-board"></i>'],
        TiposEntidad::Postulante => ["Postulantes", "Postulante", "Postulante", "Postulante", "postulantes.perfil", route("postulantes.buscar"), 'CV&nbsp;&nbsp;'],
        TiposEntidad::Profesor => ["Profesores", "Profesor(a)", "Profesor", "Profesora", "profesores.perfil", route("profesores.buscar"), '<i class="fa flaticon-teacher-with-stick"></i>'],
        TiposEntidad::Usuario => ["Usuarios", "Usuario(a)", "Usuario", "Usuaria", "usuarios.editar", route("usuarios.buscar"), '<i class="fa fa-users"></i>'],
        TiposEntidad::Curso => ["Cursos", "Curso", "Curso", "cursos.editar", route("cursos.buscar"), '<i class="fa fa-book"></i>'],
        TiposEntidad::Clase => ["Clases", "Clase", "Clase", "", "", '<i class="fa flaticon-student-in-front-of-a-stack-of-books"></i>'],
        TiposEntidad::Pago => ["Pagos", "Pago", "Pago", "", "", '<i class="fa fa-dollar"></i>']
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
