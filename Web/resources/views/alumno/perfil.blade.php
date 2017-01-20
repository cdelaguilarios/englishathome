@extends("layouts.master")
@section("titulo", "Alumnos")

@section("section_script")
<script>
  var urlActualizarHorario = "{{ route('alumnos.actualizar.horario', ['id' => $alumno->idEntidad]) }}";
</script>
<script src="{{ asset("assets/eah/js/modulos/alumno/alumno.js") }}"></script>
@endsection

@section("breadcrumb")
<li><a href="{{ route("alumnos") }}">Alumnos</a></li>
<li class="active">Perfil</li>
@endsection

@section("content")
@include("partials/errors")
<div class="row">
  <div class="col-sm-3">
    <div class="box box-primary">
      <div class="box-body box-profile">
        <img class="profile-user-img img-responsive img-circle" src="{{ route("imagenes", ["rutaImagen" => (isset($alumno->rutaImagenPerfil) && $alumno->rutaImagenPerfil != "" ? $alumno->rutaImagenPerfil : "-")]) }}" alt="User profile picture">
        <h3 class="profile-username">Alumn{{ $alumno->sexo == "F" ? "a" : "o" }} {{ $alumno->nombre . " " .  $alumno->apellido }}</h3>
        <p class="text-muted">{{ $alumno->correoElectronico }}</p>
        <span class="label {{ $estadosAlumno[$alumno->estado][1] }} btn-estado">{{ $estadosAlumno[$alumno->estado][0] }}</span>
      </div>
    </div>
    <div class="sec-datos box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">Datos principales del alumn{{ $alumno->sexo == "F" ? "a" : "o" }}</h3>
      </div>
      <div class="box-body">
        <strong><i class="fa fa-fw fa-calendar"></i> Horario</strong>
        <p class="text-muted">
          @include("util.horario", ["horario" => $alumno->horario, "modo" => "visualizar"])
        </p>
        <hr>        
        <strong><i class="fa fa-fw flaticon-favorite-book"></i> Curso</strong>
        <p class="text-muted">{{ $cursos[$alumno->idCurso] }}</p>
        <hr>
        <strong><i class="fa fa-map-marker margin-r-5"></i> Dirección</strong>
        <p class="text-muted">{{ $alumno->direccion }}<br/>{{ $alumno->direccionUbicacion }}</p>
        <p class="text-muted">
          @include("util.ubicacionMapa", ["geoLatitud" => $alumno->geoLatitud, "geoLongitud" => $alumno->geoLongitud, "modo" => "visualizar"])
        </p>
        <hr>
        <strong><i class="fa fa-user margin-r-5"></i> {{ $tiposDocumentos[$alumno->idTipoDocumento] }}</strong>
        <p class="text-muted">
          {{ $alumno->numeroDocumento }}
        </p>
        <hr>
        <strong><i class="fa fa-phone margin-r-5"></i> Teléfono</strong>
        <p class="text-muted">
          {{ $alumno->telefono }}
        </p>
        <hr>
        <strong><i class="fa fa-birthday-cake margin-r-5"></i> Fecha de nacimiento</strong>
        <p class="text-muted">
          {{ \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $alumno->fechaNacimiento)->format("d/m/Y") }}
        </p>
        <hr>                              
        <a href="{{ route("alumnos.editar", $alumno->id)}}" class="btn btn-primary btn-block"><b>Editar datos</b></a>
      </div>
    </div>
  </div>
  <div class="col-sm-9">
    <div class="nav-tabs-custom">
      <ul class="nav nav-tabs">
        <li class="active"><a href="#historial" data-toggle="tab">Historial</a></li>
        <li><a href="#pago" data-toggle="tab">Pagos</a></li>
        <li><a href="#clase" data-toggle="tab">Clases</a></li>
      </ul>
      <div class="tab-content">
        <div class="active tab-pane" id="historial">
          @include("util.historial", ["idEntidad" => $alumno->id]) 
        </div>
        <div class="tab-pane" id="pago">
          @include("alumno.pago.principal", ["idAlumno" => $alumno->id, "fechaInicioClase" => $alumno->fechaInicioClase, "costoHoraClase" => $alumno->costoHoraClase, "numeroPeriodos" => $alumno->numeroPeriodos, "totalSaldoFavor" => $alumno->totalSaldoFavor]) 
        </div>
        <div class="tab-pane" id="clase">
          @include("alumno.clase.principal", ["idAlumno" => $alumno->id, "costoHoraClase" => $alumno->costoHoraClase])
        </div>
      </div>
    </div>
  </div>
</div>
@endsection