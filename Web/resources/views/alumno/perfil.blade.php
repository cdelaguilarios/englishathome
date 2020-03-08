{{----}}
@extends("layouts.master")
@section("titulo", "Alumnos")

@section("section_script")
<script>
  var urlBuscar = "{{ route('alumnos.buscar') }}";
  var urlPerfil = "{{ route('alumnos.perfil', ['id' => 0]) }}";
  var urlActualizarHorario = "{{ route('alumnos.actualizar.horario', ['id' => $alumno->idEntidad]) }}";
  var urlActualizarProfesor = "{{ route('alumnos.actualizar.profesor', ['id' => $alumno->idEntidad, 'idDocente' => 0]) }}";
  
  var estados = {!! json_encode(App\Helpers\Enum\EstadosAlumno::listar()) !!};
  var estadosProfesor = {!! json_encode(App\Helpers\Enum\EstadosProfesor::listar()) !!};
  var idAlumno = "{{ $alumno->id}}";
  var nombreCompletoAlumno = "{{ $alumno->nombre . " " .  $alumno->apellido }}";
</script>
<script src="{{ asset("assets/eah/js/modulos/alumno/perfil.js") }}"></script>
<script src="{{ asset("assets/eah/js/modulos/alumno/busqueda.js") }}"></script>
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
        @include("util.credencialesAcceso", ["entidad" => $alumno])
        @include("util.imagenPerfil", ["entidad" => $alumno])
        <h3 class="profile-username">Alumn{{ $alumno->sexo == "F" ? "a" : "o" }} {{ $alumno->nombre . " " .  $alumno->apellido }}</h3>
        <p class="text-muted">
          <a href="{{ route("correos", ["id" => $alumno->id])}}" target="_blank">{{ $alumno->correoElectronico }}</a>
        </p>
        <p>
          @if(array_key_exists($alumno->estado, App\Helpers\Enum\EstadosAlumno::listarDisponibleCambio()))
        <div class="sec-btn-editar-estado" data-idselestados="sel-estados">
          <a href="javascript:void(0);" class="btn-editar-estado" data-id="{{ $alumno->id }}" data-estado="{{ $alumno->estado }}">
            <span class="label {{ App\Helpers\Enum\EstadosAlumno::listar()[$alumno->estado][1] }} btn-estado">{{ App\Helpers\Enum\EstadosAlumno::listar()[$alumno->estado][0] }}</span>
          </a>
        </div>
        @else
        <span class="label {{ App\Helpers\Enum\EstadosAlumno::listar()[$alumno->estado][1] }} btn-estado">{{ App\Helpers\Enum\EstadosAlumno::listar()[$alumno->estado][0] }}</span>
        @endif
        </p>
      </div>
    </div>
    <div class="sec-datos box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">Datos principales {{ $alumno->sexo == "F" ? "de la alumna" : "del alumno" }}</h3>
      </div>
      <div class="box-body">
        <strong><i class="fa fa-fw fa-calendar"></i> Horario de referencia</strong>
        <p class="text-muted">
          @include("util.horario", ["horario" => $alumno->horario, "modo" => "visualizar"])
        </p>
        <hr>   
        @if(isset($alumno->idCurso) && App\Models\Curso::verificarExistencia($alumno->idCurso))
        <strong><i class="fa fa-fw flaticon-favorite-book"></i> Curso</strong>
        <p class="text-muted">{{ App\Models\Curso::listarSimple(FALSE)[$alumno->idCurso] }}</p>
        <hr> 
        @endif  
        <strong><i class="fa flaticon-teach"></i> Profesor</strong>
        <p id="sec-nombre-profesor-actual" class="text-muted">
        @if(isset($alumno->profesorActual))
          <a href="{{ route("profesores.perfil", ["id" => $alumno->profesorActual->id]) }}" target="_blank">
            {{ $alumno->profesorActual->nombre . " " .  $alumno->profesorActual->apellido }}
          </a>
        @endif
        </p>      
        @include("alumno.util.docentesDisponibles", ["idCurso" => (isset($alumno->idCurso) ? $alumno->idCurso : null)])
        <button id="btn-seleccionar-profesor-actual" type="button" class="btn btn-primary btn-xs">{{ (isset($alumno->profesorActual) ? "Cambiar" : "Seleccionar") }} profesor</button>   
        <hr> 
        @if(isset($alumno->fechaInicioClase) && (int)\Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $alumno->fechaInicioClase)->format("Y") > 1900)
        <strong><i class="fa fa-calendar-check-o margin-r-5"></i> Fecha de inicio de clases</strong>
        <p class="text-muted">
          {{ \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $alumno->fechaInicioClase)->format("d/m/Y") }}
        </p>
        <hr>    
        @endif
        @if(isset($alumno->telefono))
        <strong><i class="fa fa-phone margin-r-5"></i> Teléfono</strong>
        <p class="text-muted">
          {!! App\Helpers\Util::incluirEnlaceWhatsApp($alumno->telefono) !!}
        </p>
        <hr>
        @endif
        <strong><i class="fa fa-map-marker margin-r-5"></i> Dirección</strong>
        <p class="text-muted">{{ $alumno->direccion }}{!! ((isset($alumno->numeroDepartamento) && $alumno->numeroDepartamento != "") ? "<br/>Depto./Int " . $alumno->numeroDepartamento : "") !!}{!! ((isset($alumno->referenciaDireccion) && $alumno->referenciaDireccion != "") ? " - " . $alumno->referenciaDireccion : "") !!}<br/>{{ $alumno->direccionUbicacion }}</p>
        <p class="text-muted">
          @include("util.ubicacionMapa", ["geoLatitud" => $alumno->geoLatitud, "geoLongitud" => $alumno->geoLongitud, "modo" => "visualizar"])
        </p>
        <hr>   
        @if(isset($alumno->interesadoRelacionado) && !empty($alumno->interesadoRelacionado["consulta"]))
        <strong><i class="fa flaticon-questioning margin-r-5"></i> Consulta (interesado)</strong>
        <p class="text-muted">
          {{ $alumno->interesadoRelacionado["consulta"] }}
        </p>
        <hr>  
        @endif              
        @if(isset($alumno->idNivelIngles) && !empty($alumno->idNivelIngles))
        <strong><i class="fa fa-list-ol margin-r-5"></i> Nivel de inglés</strong>
        <p class="text-muted">
          {{ App\Models\NivelIngles::listarSimple()[$alumno->idNivelIngles] }}
        </p>
        <hr>
        @endif              
        @if(isset($alumno->inglesLugarEstudio) && !empty($alumno->inglesLugarEstudio))
        <strong><i class="fa fa-institution margin-r-5"></i> Lugar donde estudio anteriormente</strong>
        <p class="text-muted">
          {{ $alumno->inglesLugarEstudio }}
        </p>
        <hr>
        @endif           
        @if(isset($alumno->inglesPracticaComo) && !empty($alumno->inglesPracticaComo))
        <strong><i class="fa fa-commenting-o margin-r-5"></i> ¿Cómo practica?</strong>
        <p class="text-muted">
          {{ $alumno->inglesPracticaComo }}
        </p>
        <hr>
        @endif       
        @if(isset($alumno->inglesObjetivo) && !empty($alumno->inglesObjetivo))
        <strong><i class="fa fa-check-square-o margin-r-5"></i> Objetivos específicos</strong>
        <p class="text-muted">
          {{ $alumno->inglesObjetivo }}
        </p>
        <hr>
        @endif
        @if(isset($alumno->numeroDocumento))
        <strong><i class="fa fa-user margin-r-5"></i> {{ (isset($alumno->idTipoDocumento) ? App\Models\TipoDocumento::listarSimple()[$alumno->idTipoDocumento] : "") }}</strong>
        <p class="text-muted">
          {{ $alumno->numeroDocumento }}
        </p>
        <hr>
        @endif
        @if(isset($alumno->fechaNacimiento) && (int)\Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $alumno->fechaNacimiento)->format("Y") > 1900)
        <strong><i class="fa fa-birthday-cake margin-r-5"></i> Fecha de nacimiento</strong>
        <p class="text-muted">
          {{ \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $alumno->fechaNacimiento)->format("d/m/Y") }}
        </p>
        <hr>    
        @endif                 
      </div>
    </div>
  </div>
  <div class="col-sm-9">
    <div class="box box-primary">        
      <div class="box-body">
        <div class="form-group">
          <div class="col-sm-6">
            <a href="{{ route("alumnos.crear")}}" class="btn btn-primary btn-clean">Nuevo alumno</a>          
            <a href="{{ route("alumnos.editar", $alumno->id)}}" class="btn btn-primary btn-clean">Editar datos</a>
            <a href="{{ route("alumnos.ficha", $alumno->id)}}" target="_blank" class="btn btn-primary btn-clean">Descargar ficha</a>
            <a href="{{ route("correos", ["id" => $alumno->id])}}" target="_blank" class="btn btn-primary btn-clean">Enviar correo</a>
          </div>      
          <div class="col-sm-2">
            @if(isset($alumno->idAlumnoSiguiente))
            <a href="{{ route("alumnos.perfil", ["id" => $alumno->idAlumnoSiguiente]) }}" class="btn btn-default pull-right"><span class="glyphicon glyphicon-arrow-right"></span></a>
            @endif
            @if(isset($alumno->idAlumnoAnterior))
            <a href="{{ route("alumnos.perfil", ["id" => $alumno->idAlumnoAnterior]) }}" class="btn btn-default pull-right"><span class="glyphicon glyphicon-arrow-left"></span></a>
            @endif
          </div>            
          <div class="col-sm-4">
            {{ Form::select("", [], null, ["id"=>"sel-alumno", "class" => "form-control", "data-seccion" => "perfil", "style" => "width: 100%"]) }}
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
        <li><a href="#sec-comentarios-administrador" data-toggle="tab">Comentarios</a></li>
      </ul>
      <div class="tab-content">
        <div id="historial" class="active tab-pane">
          @include("util.notificacion.principalHistorial", ["idEntidad" => $alumno->id, "nombreEntidad" => "alumno"]) 
        </div>
        <div id="pago" class="tab-pane">
          @include("alumno.pago.principal")         
        </div>
        <div id="clase" class="tab-pane">
          @include("alumno.clase.principal")
        </div>
        <div id="calendario" class="tab-pane">
          @include("util.calendario", ["idEntidad" => $alumno->id]) 
        </div>
        <div id="sec-comentarios-administrador" class="tab-pane">
          @include("util.comentariosAdministrador", ["idEntidad" => $alumno->id, "comentarioAdministrador" => $alumno->comentarioAdministrador]) 
        </div> 
      </div>
    </div>
  </div>
</div>
<div style="display: none">
  {{ Form::select("", App\Helpers\Enum\EstadosAlumno::listarDisponibleCambio(), null, ["id" => "sel-estados", "class" => "form-control", "data-urlactualizar" => route('alumnos.actualizar.estado', ['id' => 0]), "data-estados" => json_encode(App\Helpers\Enum\EstadosAlumno::listar())]) }}
</div>
@endsection