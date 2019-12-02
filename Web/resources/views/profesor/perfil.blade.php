{{----}}
@extends("layouts.master")
@section("titulo", "Profesores")

@section("section_script")
<script>
  var urlActualizarHorario = "{{ route('profesores.actualizar.horario', ['id' => $profesor->idEntidad]) }}";
  var urlPerfil = "{{ route('profesores.perfil', ['id' => 0]) }}";
  var urlBuscar = "{{ route('profesores.buscar') }}";
  
  var estados = {!! json_encode(App\Helpers\Enum\EstadosProfesor::listar()) !!};
  
  var idProfesor = "{{ $profesor->id}}";
  var nombreCompletoProfesor = "{{ $profesor->nombre . " " .  $profesor->apellido }}";</script>
<script src="{{ asset("assets/eah/js/modulos/profesor/perfil.js") }}"></script>
<script src="{{ asset("assets/eah/js/modulos/profesor/busqueda.js") }}"></script>
@endsection

@section("breadcrumb")
<li><a href="{{ route("profesores") }}">Profesores</a></li>
<li class="active">Perfil</li>
@endsection

@section("content")
@include("partials/errors")
<div class="row">
  <div class="col-sm-3">
    <div class="box box-primary">
      <div class="box-body box-profile">
        @include("util.credencialesAcceso", ["entidad" => $profesor])
        @include("util.imagenPerfil", ["entidad" => $profesor])
        <h3 class="profile-username">Profesor{{ $profesor->sexo == "F" ? "a" : "" }} {{ $profesor->nombre . " " .  $profesor->apellido }}</h3>
        <p class="text-muted">
          <a href="{{ route("correos", ["id" => $profesor->id])}}" target="_blank">{{ $profesor->correoElectronico }}</a>
        </p>
        <p>
          @if(array_key_exists($profesor->estado, App\Helpers\Enum\EstadosProfesor::listarDisponibleCambio()))
        <div class="sec-btn-editar-estado" data-idselestados="sel-estados">
          <a href="javascript:void(0);" class="btn-editar-estado" data-id="{{ $profesor->id }}" data-estado="{{ $profesor->estado }}">
            <span class="label {{ App\Helpers\Enum\EstadosProfesor::listar()[$profesor->estado][1] }} btn-estado">{{ App\Helpers\Enum\EstadosProfesor::listar()[$profesor->estado][0] }}</span>
          </a>
        </div>
        @else
        <span class="label {{ App\Helpers\Enum\EstadosProfesor::listar()[$profesor->estado][1] }} btn-estado">{{ App\Helpers\Enum\EstadosProfesor::listar()[$profesor->estado][0] }}</span>
        @endif
        </p>
      </div>
    </div>
    <div class="sec-datos box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">Datos principales {{ $profesor->sexo == "F" ? "de la profesora" : "del profesor" }}</h3>
      </div>
      <div class="box-body">
        <strong><i class="fa fa-fw fa-calendar"></i> Horario</strong>
        <p class="text-muted">
          @include("util.horario", ["horario" => $profesor->horario, "modo" => "visualizar"])
        </p>
        <hr>  
        @if(isset($profesor->cursos) && count($profesor->cursos) > 0)      
        <strong><i class="fa fa-fw flaticon-favorite-book"></i> Cursos</strong>
        @foreach($profesor->cursos as $curso)
        <p class="text-muted">{{ App\Models\Curso::listarSimple(FALSE)[$curso->idCurso] }}</p>
        @endforeach
        <hr>
        @endif
        @if(isset($profesor->telefono))
        <strong><i class="fa fa-phone margin-r-5"></i> Teléfono</strong>
        <p class="text-muted">
          {!! App\Helpers\Util::incluirEnlaceWhatsApp($profesor->telefono) !!}
        </p>
        <hr>
        @endif
        <strong><i class="fa fa-map-marker margin-r-5"></i> Dirección</strong>
        <p class="text-muted">{{ $profesor->direccion }}{!! ((isset($profesor->numeroDepartamento) && $profesor->numeroDepartamento != "") ? "<br/>Depto./Int " . $profesor->numeroDepartamento : "") !!}{!! ((isset($profesor->referenciaDireccion) && $profesor->referenciaDireccion != "") ? " - " . $profesor->referenciaDireccion : "") !!}<br/>{{ $profesor->direccionUbicacion }}</p>
        <p class="text-muted">
          @include("util.ubicacionMapa", ["geoLatitud" => $profesor->geoLatitud, "geoLongitud" => $profesor->geoLongitud, "modo" => "visualizar"])
        </p>
        <hr>     
        @if (isset($profesor->audio) && !empty($profesor->audio))
        <strong><i class="fa fa-volume-up margin-r-5"></i> Audio de presentación</strong>
        <p class="text-muted">
          <audio controls style="width: 100%;">
            <source src="{{ route("archivos", ["nombre" => ($profesor->audio), "esAudio" => 1]) }}">
            Tu explorador no soporta este elemento de audio
          </audio>
        </p>
        <hr>   
        @endif    
        @if (!empty($profesor->comentarioAdministrador))
        <strong><i class="fa fa-list-alt margin-r-5"></i> Comentarios</strong>
        <p class="text-muted">
          {!! $profesor->comentarioAdministrador !!}
        </p>
        <hr>   
        @endif   
        @if(isset($profesor->numeroDocumento))
        <strong><i class="fa fa-user margin-r-5"></i> {{ (isset($profesor->idTipoDocumento) ? App\Models\TipoDocumento::listarSimple()[$profesor->idTipoDocumento] : "") }}</strong>
        <p class="text-muted">
          {{ $profesor->numeroDocumento }}
        </p>
        <hr> 
        @endif
        @if(isset($profesor->fechaNacimiento) && (int)\Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $profesor->fechaNacimiento)->format("Y") > 1900)
        <strong><i class="fa fa-birthday-cake margin-r-5"></i> Fecha de nacimiento</strong>
        <p class="text-muted">
          {{ \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $profesor->fechaNacimiento)->format("d/m/Y") }}
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
            <a href="{{ route("profesores.crear")}}" class="btn btn-primary btn-clean">Nuevo profesor</a>                        
            <a href="{{ route("profesores.editar", $profesor->id)}}" class="btn btn-primary btn-clean">Editar datos</a>
            <a href="{{ route("profesores.ficha", $profesor->id)}}" target="_blank" class="btn btn-primary btn-clean">Descargar ficha</a>
            <a href="{{ route("profesores.ficha.alumno", $profesor->id)}}" target="_blank" class="btn btn-primary btn-clean">Descargar ficha para el alumno</a>
            <a href="{{ route("correos", ["id" => $profesor->id])}}" target="_blank" class="btn btn-primary btn-clean">Enviar correo</a>
          </div>      
          <div class="col-sm-2">
            @if(isset($profesor->idProfesorSiguiente))
            <a href="{{ route("profesores.perfil", ["id" => $profesor->idProfesorSiguiente]) }}" class="btn btn-default pull-right"><span class="glyphicon glyphicon-arrow-right"></span></a>
            @endif
            @if(isset($profesor->idProfesorAnterior))
            <a href="{{ route("profesores.perfil", ["id" => $profesor->idProfesorAnterior]) }}" class="btn btn-default pull-right"><span class="glyphicon glyphicon-arrow-left"></span></a>
            @endif
          </div>           
          <div class="col-sm-4">
            {{ Form::select("", [], null, ["id"=>"sel-profesor", "class" => "form-control", "data-seccion" => "perfil", "style" => "width: 100%"]) }}
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
        <li><a href="#experiencia-laboral" data-toggle="tab">Experiencia laboral</a></li>
        <li><a href="#sec-comentarios-administrador" data-toggle="tab">Comentarios</a></li>
        <li><a href="#sec-perfil" data-toggle="tab">Perfil</a></li>
      </ul>
      <div class="tab-content">
        <div id="historial" class="active tab-pane">
          @include("util.historial", ["idEntidad" => $profesor->id, "nombreEntidad" => "profesor"]) 
        </div>
        <div id="pago" class="tab-pane">
          @include("profesor.pago.principal")
        </div>
        <div id="clase" class="tab-pane">
          @include("profesor.clase.principal")
        </div>
        <div id="calendario" class="tab-pane">
          @include("util.calendario", ["idEntidad" => $profesor->id, "esEntidadProfesor" => 1]) 
        </div>
        <div id="experiencia-laboral" class="tab-pane">
          @include("docente.util.experienciaLaboral", ["docente" => $profesor]) 
        </div>
        <div id="sec-comentarios-administrador" class="tab-pane">
          @include("util.comentariosAdministrador", ["idEntidad" => $profesor->id, "comentarioAdministrador" => $profesor->comentarioAdministrador]) 
        </div>
        <div id="sec-perfil" class="tab-pane">
          {{ Form::open(["url" => route("profesores.actualizar.comentarios.perfil", ["id" => $profesor->id]), "id" => "formulario-comentarios-perfil", "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }}
          <div class="row">
            <div class="col-sm-12">
              <div class="box-body">
                <div class="form-group">
                  <div class="col-sm-12">
                    {{ Form::textarea("comentarioPerfil", $profesor->comentarioPerfil, ["id" => "comentarios-perfil", "class" => "form-control", "rows" => "10", "maxlength" =>"8000"]) }}
                  </div>                                        
                </div>
              </div>
              <div class="box-footer">    
                <div class="form-group">          
                  <div class="col-sm-12">               
                    <button type="submit" class="btn btn-success pull-right">Guardar</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          {{ Form::close() }}
        </div>
        @include("profesor.pago.datos") 
      </div>
    </div>
  </div>
</div>
<div style="display: none">
  {{ Form::select("", App\Helpers\Enum\EstadosProfesor::listarDisponibleCambio(), null, ["id" => "sel-estados", "class" => "form-control", "data-urlactualizar" => route('profesores.actualizar.estado', ['id' => 0]), "data-estados" => json_encode(App\Helpers\Enum\EstadosProfesor::listar())]) }}
</div>
@endsection