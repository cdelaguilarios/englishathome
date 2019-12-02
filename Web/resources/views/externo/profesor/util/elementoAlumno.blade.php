<div class="col-sm-3 sec-entidad-alumno">
  <a href="{{ route("profesores.mis.alumnos.clases", ["id" => $alumno->idEntidad]) }}">
    <img src="{{ route("archivos", ["nombre" => (isset($alumno->imagenPerfil) && $alumno->imagenPerfil != "" ? $alumno->imagenPerfil : "-"), "sexoEntidad" => ($alumno->sexo == "F" ? "f" : "m")]) }}" width="128" alt="Alumn{{ $alumno->sexo == "F" ? "a" : "o" }} {{ $alumno->nombre . " " .  $alumno->apellido }}">
    <span class="users-list-name">{{ $alumno->nombre . " " . $alumno->apellido }}<br/><small>{{ $alumno->correoElectronico }}</small></span>
  </a>
</div>