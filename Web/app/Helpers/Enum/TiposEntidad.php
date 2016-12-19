<?php

namespace App\Helpers\Enum;

class TiposEntidad {

  const Alumno = "ALUMNO";
  const Interesado = "INTERESADO";
  const Profesor = "PROFESOR";
  const Postulante = "POSTULANTE";
  const Usuario = "USUARIO";

  public static function Listar() {
    return [
        TiposEntidad::Alumno => ["Alumno", "alumnos.perfil"],
        TiposEntidad::Interesado => ["Interesado", "interesados.editar"],
        TiposEntidad::Profesor => ["Profesor", "profesores.perfil"],
        TiposEntidad::Postulante => ["Postulante", "postulantes.editar"],
        TiposEntidad::Usuario => ["Usuario", "usuarios.editar"]
    ];
  }

  public static function listarTiposDocente() {
    return [
        TiposEntidad::Profesor => "Profesor",
        TiposEntidad::Postulante => "Postulante"
    ];
  }

}
