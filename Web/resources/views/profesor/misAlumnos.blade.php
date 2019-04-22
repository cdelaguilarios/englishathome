@extends("layouts.masterAlumnoProfesor")
@section("titulo", "Alumnos")

@section("content")
<div class="row">
  <div class="col-md-12">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-info">
          <div class="box-header with-border">
            <h3 class="box-title">Mis alumnos</h3>
          </div>
          <div class="box-body no-padding">
            @foreach ($alumnos as $alumno)              
            <div class="col-sm-3 sec-entidad-alumno">
              <a href="{{ route("profesores.mis.alumnos.clases", ["id" => $alumno->idEntidad]) }}">
                <img src="{{ route("archivos", ["nombre" => (isset($alumno->imagenPerfil) && $alumno->imagenPerfil != "" ? $alumno->imagenPerfil : "-"), "tip" => ($alumno->sexo == "F" ? "f" : "m")]) }}" width="128" alt="Alumn{{ $alumno->sexo == "F" ? "a" : "o" }} {{ $alumno->nombre . " " .  $alumno->apellido }}">
                <span class="users-list-name">{{ $alumno->nombre . " " . $alumno->apellido }}<br/><small>{{ $alumno->correoElectronico }}</small></span>
              </a>
            </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection