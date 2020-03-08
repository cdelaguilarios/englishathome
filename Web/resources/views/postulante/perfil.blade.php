{{----}}
@extends("layouts.master")
@section("titulo", "Postulantes")

@section("section_script")
<script>
  var urlActualizarHorario = "{{ route('postulantes.actualizar.horario', ['id' => $postulante->idEntidad]) }}";
  var urlPerfil = "{{ route('postulantes.perfil', ['id' => 0]) }}";
  var urlBuscar = "{{ route('postulantes.buscar') }}";
  
  var estados = {!! json_encode(App\Helpers\Enum\EstadosPostulante::listar()) !!};
  
  var idPostulante = "{{ $postulante->id}}";
  var nombreCompletoPostulante = "{{ $postulante->nombre . " " .  $postulante->apellido }}";
</script>
<script src="{{ asset("assets/eah/js/modulos/postulante/perfil.js") }}"></script>
<script src="{{ asset("assets/eah/js/modulos/postulante/busqueda.js") }}"></script>
@endsection

@section("breadcrumb")
<li><a href="{{ route("postulantes") }}">Postulantes</a></li>
<li class="active">Perfil</li>
@endsection

@section("content")
@include("partials/errors")
<div class="row">
  <div class="col-sm-3">
    <div class="box box-primary">
      <div class="box-body box-profile">
        @include("util.imagenPerfil", ["entidad" => $postulante])
        <h3 class="profile-username">Postulante {{ $postulante->nombre . " " .  $postulante->apellido }}</h3>
        <p class="text-muted">
          <a href="{{ route("correos", ["id" => $postulante->id])}}" target="_blank">{{ $postulante->correoElectronico }}</a>
        </p>
        <p>
        @if(array_key_exists($postulante->estado, App\Helpers\Enum\EstadosPostulante::listarDisponibleCambio()))
        <div class="sec-btn-editar-estado" data-idselestados="sel-estados">
          <a href="javascript:void(0);" class="btn-editar-estado" data-id="{{ $postulante->id }}" data-estado="{{ $postulante->estado }}">
            <span class="label {{ App\Helpers\Enum\EstadosPostulante::listar()[$postulante->estado][1] }} btn-estado">{{ App\Helpers\Enum\EstadosPostulante::listar()[$postulante->estado][0] }}</span>
          </a>
        </div>
        @else
        <span class="label {{ App\Helpers\Enum\EstadosPostulante::listar()[$postulante->estado][1] }} btn-estado">{{ App\Helpers\Enum\EstadosPostulante::listar()[$postulante->estado][0] }}</span>
        @endif
        @if($postulante->estado == App\Helpers\Enum\EstadosPostulante::ProfesorRegistrado)
        <a href="{{ route('postulantes.perfil.profesor', ['id' => $postulante->idEntidad]) }}" title="Ver perfil del profesor" target="_blank" class="btn-perfil-relacion-entidad"><i class="fa fa-eye"></i></a>
        @endif
        </p>
      </div>
    </div>
    <div class="sec-datos box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">Datos principales {{ $postulante->sexo == "F" ? "de la postulantea" : "del postulante" }}</h3>
      </div>
      <div class="box-body">
        <strong><i class="fa fa-fw fa-calendar"></i> Horario</strong>
        <p class="text-muted">
          @include("util.horario", ["horario" => $postulante->horario, "modo" => "visualizar"])
        </p>
        <hr>  
        @if(isset($postulante->cursos) && count($postulante->cursos) > 0)      
        <strong><i class="fa fa-fw flaticon-favorite-book"></i> Cursos</strong>
        @foreach($postulante->cursos as $curso)
        <p class="text-muted">{{ App\Models\Curso::listarSimple(FALSE)[$curso->idCurso] }}</p>
        @endforeach
        <hr>
        @endif
        @if(isset($postulante->telefono))
        <strong><i class="fa fa-phone margin-r-5"></i> Teléfono</strong>
        <p class="text-muted">
          {!! App\Helpers\Util::incluirEnlaceWhatsApp($postulante->telefono) !!}
        </p>
        <hr>
        @endif
        <strong><i class="fa fa-map-marker margin-r-5"></i> Dirección</strong>
        <p class="text-muted">{{ $postulante->direccion }}{!! ((isset($postulante->numeroDepartamento) && $postulante->numeroDepartamento != "") ? "<br/>Depto./Int " . $postulante->numeroDepartamento : "") !!}{!! ((isset($postulante->referenciaDireccion) && $postulante->referenciaDireccion != "") ? " - " . $postulante->referenciaDireccion : "") !!}<br/>{{ $postulante->direccionUbicacion }}</p>
        <p class="text-muted">
          @include("util.ubicacionMapa", ["geoLatitud" => $postulante->geoLatitud, "geoLongitud" => $postulante->geoLongitud, "modo" => "visualizar"])
        </p>
        <hr>  
        @if (isset($postulante->audio) && !empty($postulante->audio))
        <strong><i class="fa fa-volume-up margin-r-5"></i> Audio de presentación</strong>
        <p class="text-muted">
          <audio controls style="width: 100%;">
            <source src="{{ route("archivos", ["nombre" => ($postulante->audio), "esAudio" => 1]) }}">
            Tu explorador no soporta este elemento de audio
          </audio>
        </p>
        <hr>   
        @endif 
        @if (!empty($postulante->comentarioAdministrador))
        <strong><i class="fa fa-list-alt margin-r-5"></i> Comentarios</strong>
        <p class="text-muted">
          {!! $postulante->comentarioAdministrador !!}
        </p>
        <hr>   
        @endif        
        @if(isset($postulante->numeroDocumento))
        <strong><i class="fa fa-user margin-r-5"></i> {{ (isset($postulante->idTipoDocumento) ? App\Models\TipoDocumento::listarSimple()[$postulante->idTipoDocumento] : "") }}</strong>
        <p class="text-muted">
          {{ $postulante->numeroDocumento }}
        </p>
        <hr> 
        @endif
        @if(isset($postulante->fechaNacimiento))
        <strong><i class="fa fa-birthday-cake margin-r-5"></i> Fecha de nacimiento</strong>
        <p class="text-muted">
          {{ \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $postulante->fechaNacimiento)->format("d/m/Y") }}
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
            <a href="{{ route("postulantes.crear")}}" class="btn btn-primary btn-clean">Nuevo postulante</a>                     
            <a href="{{ route("postulantes.editar", $postulante->id)}}" class="btn btn-primary btn-clean">Editar datos</a>
            <a href="{{ route("correos", ["id" => $postulante->id])}}" target="_blank" class="btn btn-primary btn-clean">Enviar correo</a> 
          </div>     
          <div class="col-sm-2">
            @if(isset($postulante->idPostulanteSiguiente))
            <a href="{{ route("postulantes.perfil", ["id" => $postulante->idPostulanteSiguiente]) }}" class="btn btn-default pull-right"><span class="glyphicon glyphicon-arrow-right"></span></a>
            @endif
            @if(isset($postulante->idPostulanteAnterior))
            <a href="{{ route("postulantes.perfil", ["id" => $postulante->idPostulanteAnterior]) }}" class="btn btn-default pull-right"><span class="glyphicon glyphicon-arrow-left"></span></a>
            @endif
          </div>         
          <div class="col-sm-4">
            {{ Form::select("", [], null, ["id"=>"sel-postulante", "class" => "form-control", "data-seccion" => "perfil", "style" => "width: 100%"]) }}
          </div>
        </div> 
      </div>
    </div>
    <div class="nav-tabs-custom">
      <ul class="nav nav-tabs">
        <li class="active"><a href="#historial" data-toggle="tab">Historial</a></li>
        <li><a href="#experiencia-laboral" data-toggle="tab">Experiencia laboral</a></li>
        <li><a href="#sec-comentarios-administrador" data-toggle="tab">Comentarios</a></li>
      </ul>
      <div class="tab-content">
        <div id="historial" class="active tab-pane">
          @include("util.notificacion.principalHistorial", ["idEntidad" => $postulante->id, "nombreEntidad" => "postulante"]) 
        </div>
        <div id="experiencia-laboral" class="tab-pane">
          @include("docente.util.experienciaLaboral", ["docente" => $postulante]) 
        </div>
        <div id="sec-comentarios-administrador" class="tab-pane">
          @include("util.comentariosAdministrador", ["idEntidad" => $postulante->id, "comentarioAdministrador" => $postulante->comentarioAdministrador]) 
        </div>
      </div>
    </div>
  </div>
</div>
<div style="display: none">
  {{ Form::select("", App\Helpers\Enum\EstadosPostulante::listarDisponibleCambio(), null, ["id" => "sel-estados", "class" => "form-control", "data-urlactualizar" => route('postulantes.actualizar.estado', ['id' => 0]), "data-estados" => json_encode(App\Helpers\Enum\EstadosPostulante::listar())]) }}
</div>
@endsection