@extends("layouts.master")
@section("titulo", "Alumnos")

@section("section_script")
<script>
  var urlActualizarHorario = "{{ route('alumnos.actualizar.horario', ['id' => $alumno->idEntidad]) }}";
  var urlPerfil = "{{ route('alumnos.perfil', ['id' => 0]) }}";
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
        <img class="profile-user-img img-responsive img-circle" src="{{ route("archivos", ["nombre" => (isset($alumno->imagenPerfil) && $alumno->imagenPerfil != "" ? $alumno->imagenPerfil : "-"), "tip" => ($alumno->sexo == "F" ? "f" : "m")]) }}" alt="Alumn{{ $alumno->sexo == "F" ? "a" : "o" }} {{ $alumno->nombre . " " .  $alumno->apellido }}">
        <h3 class="profile-username">Alumn{{ $alumno->sexo == "F" ? "a" : "o" }} {{ $alumno->nombre . " " .  $alumno->apellido }}</h3>
        <p class="text-muted">{{ $alumno->correoElectronico }}</p>
        <span class="label {{ App\Helpers\Enum\EstadosAlumno::listar()[$alumno->estado][1] }} btn-estado">{{ App\Helpers\Enum\EstadosAlumno::listar()[$alumno->estado][0] }}</span>
      </div>
    </div>
    <div class="sec-datos box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">Datos principales {{ $alumno->sexo == "F" ? "de la alumna" : "del alumno" }}</h3>
      </div>
      <div class="box-body">
        <strong><i class="fa fa-fw fa-calendar"></i> Horario</strong>
        <p class="text-muted">
          @include("util.horario", ["horario" => $alumno->horario, "modo" => "visualizar"])
        </p>
        <hr>   
        @if(isset($alumno->profesorProximaClase)) 
        <strong><i class="fa flaticon-teach"></i> Profesor</strong>
        <p class="text-muted">
          <a href="{{ route("profesores.perfil", ["id" => $alumno->profesorProximaClase->idEntidad]) }}" target="_blank">
            {{ $alumno->profesorProximaClase->nombre . " " .  $alumno->profesorProximaClase->apellido }}
          </a>
        </p>
        <hr> 
        @endif 
        @if(isset($alumno->idCurso))
        <strong><i class="fa fa-fw flaticon-favorite-book"></i> Curso</strong>
        <p class="text-muted">{{ $cursos[$alumno->idCurso] }}</p>
        <hr> 
        @endif
        <strong><i class="fa fa-map-marker margin-r-5"></i> Dirección</strong>
        <p class="text-muted">{{ $alumno->direccion }}<br/>{{ $alumno->direccionUbicacion }}</p>
        <p class="text-muted">
          @include("util.ubicacionMapa", ["geoLatitud" => $alumno->geoLatitud, "geoLongitud" => $alumno->geoLongitud, "modo" => "visualizar"])
        </p>
        <hr>        
        @if(isset($alumno->numeroDocumento))
        <strong><i class="fa fa-user margin-r-5"></i> {{ (isset($alumno->idTipoDocumento) ? App\Models\TipoDocumento::listarSimple()[$alumno->idTipoDocumento] : "") }}</strong>
        <p class="text-muted">
          {{ $alumno->numeroDocumento }}
        </p>
        <hr>
        @endif
        @if(isset($alumno->telefono))
        <strong><i class="fa fa-phone margin-r-5"></i> Teléfono</strong>
        <p class="text-muted">
          {{ $alumno->telefono }}
        </p>
        <hr>
        @endif
        @if(isset($alumno->fechaNacimiento))
        <strong><i class="fa fa-birthday-cake margin-r-5"></i> Fecha de nacimiento</strong>
        <p class="text-muted">
          {{ \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $alumno->fechaNacimiento)->format("d/m/Y") }}
        </p>
        <hr>    
        @endif                          
        <a href="{{ route("alumnos.editar", $alumno->id)}}" class="btn btn-primary btn-block"><b>Editar datos</b></a>
      </div>
    </div>
  </div>
  <div class="col-sm-9">
    <div class="box box-primary">        
      <div class="box-body">
        <div class="form-group">
          <div class="col-sm-8">
            <a href="{{ route("alumnos.crear")}}" class="btn btn-primary btn-clean">Nuevo alumno</a> 
          </div>           
          <div class="col-sm-4">
            {{ Form::select("",App\Models\Alumno::listarBusqueda(), $alumno->id, ["id"=>"sel-alumno", "class" => "form-control", "data-seccion" => "perfil", "style" => "width: 100%;"]) }}
          </div>
        </div> 
      </div>
    </div>
    <div class="nav-tabs-custom">
      <ul class="nav nav-tabs">
        <li class="active"><a href="#historial" data-toggle="tab">Historial</a></li>
        <li><a href="#pago" data-toggle="tab">Pagos</a></li>
        <li><a href="#clase" data-toggle="tab">Clases</a></li>
        <li><a href="#calendario" data-toggle="tab">Calendario</a></li>
      </ul>
      <div class="tab-content">
        <div id="historial" class="active tab-pane">
          @include("util.historial", ["idEntidad" => $alumno->id]) 
        </div>
        <div id="pago" class="tab-pane">
          @include("alumno.pago.principal", ["idAlumno" => $alumno->id, "fechaInicioClase" => $alumno->fechaInicioClase, "costoHoraClase" => $alumno->costoHoraClase, "numeroPeriodos" => $alumno->numeroPeriodos, "totalSaldoFavor" => $alumno->totalSaldoFavor, "idCurso" => (isset($alumno->idCurso) ? $alumno->idCurso : NULL)]) 
        </div>
        <div id="clase" class="tab-pane">
          @include("alumno.clase.principal", ["idAlumno" => $alumno->id, "costoHoraClase" => $alumno->costoHoraClase, "idCurso" => (isset($alumno->idCurso) ? $alumno->idCurso : NULL)])
        </div>
        <div id="calendario" class="tab-pane">
          @include("util.calendario", ["idEntidad" => $alumno->id]) 
        </div>
        @include("alumno.pago.datos") 
        @include("util.datosClase") 
      </div>
    </div>
  </div>
</div>
@endsection