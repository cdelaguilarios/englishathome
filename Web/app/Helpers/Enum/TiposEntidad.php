<?php

namespace App\Helpers\Enum;

class TiposEntidad {

  const Alumno = "ALUMNO";
  const Interesado = "INTERESADO";
  const Profesor = "PROFESOR";
  const Postulante = "POSTULANTE";
  const Usuario = "USUARIO";

  public static function listar() {
    return [
        TiposEntidad::Alumno => ["Alumno", "Alumna", "alumnos.perfil"],
        TiposEntidad::Interesado => ["Interesado", "Interesada", "interesados.editar"],
        TiposEntidad::Profesor => ["Profesor", "Profesora", "profesores.perfil"],
        TiposEntidad::Postulante => ["Postulante", "Postulante", "postulantes.perfil"],
        TiposEntidad::Usuario => ["Usuario", "Usuaria", "usuarios.editar"]
    ];
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

  public static function listarTiposDocente() {
    return [
        TiposEntidad::Profesor => "Profesor",
        TiposEntidad::Postulante => "Postulante"
    ];
  }

}
