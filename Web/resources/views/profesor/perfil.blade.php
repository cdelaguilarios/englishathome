@extends("layouts.master")
@section("titulo", "Profesores")

@section("section_script")
<script>
  var urlActualizarHorario = "{{ route('profesores.actualizar.horario', ['id' => $profesor->idEntidad]) }}";
  var urlPerfil = "{{ route('profesores.perfil', ['id' => 0]) }}";
</script>
<script src="{{ asset("assets/eah/js/modulos/profesor/profesor.js") }}"></script>
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
        <img class="profile-user-img img-responsive img-circle" src="{{ route("archivos", ["nombre" => (isset($profesor->imagenPerfil) && $profesor->imagenPerfil != "" ? $profesor->imagenPerfil : "-"), "tip" => ($profesor->sexo == "F" ? "f" : "m")]) }}" alt="Profesor{{ $profesor->sexo == "F" ? "a" : "" }} {{ $profesor->nombre . " " .  $profesor->apellido }}">
        <h3 class="profile-username">Profesor{{ $profesor->sexo == "F" ? "a" : "" }} {{ $profesor->nombre . " " .  $profesor->apellido }}</h3>
        <p class="text-muted">{{ $profesor->correoElectronico }}</p>
        <span class="label {{ App\Helpers\Enum\EstadosProfesor::listar()[$profesor->estado][1] }} btn-estado">{{ App\Helpers\Enum\EstadosProfesor::listar()[$profesor->estado][0] }}</span>
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
        <p class="text-muted">{{ $cursos[$curso->idCurso] }}</p>
        @endforeach
        <hr>
        @endif
        <strong><i class="fa fa-map-marker margin-r-5"></i> Dirección</strong>
        <p class="text-muted">{{ $profesor->direccion }}{!! ((isset($profesor->numeroDepartamento) && $profesor->numeroDepartamento != "") ? "<br/>Depto./Int " . $profesor->numeroDepartamento : "") !!}{!! ((isset($profesor->referenciaDireccion) && $profesor->referenciaDireccion != "") ? " - " . $profesor->referenciaDireccion : "") !!}<br/>{{ $profesor->direccionUbicacion }}</p>
        <p class="text-muted">
          @include("util.ubicacionMapa", ["geoLatitud" => $profesor->geoLatitud, "geoLongitud" => $profesor->geoLongitud, "modo" => "visualizar"])
        </p>
        <hr>        
        @if(isset($profesor->numeroDocumento))
        <strong><i class="fa fa-user margin-r-5"></i> {{ (isset($profesor->idTipoDocumento) ? App\Models\TipoDocumento::listarSimple()[$profesor->idTipoDocumento] : "") }}</strong>
        <p class="text-muted">
          {{ $profesor->numeroDocumento }}
        </p>
        <hr> 
        @endif
        @if(isset($profesor->telefono))
        <strong><i class="fa fa-phone margin-r-5"></i> Teléfono</strong>
        <p class="text-muted">
          {{ $profesor->telefono }}
        </p>
        <hr>
        @endif
        @if(isset($profesor->fechaNacimiento))
        <strong><i class="fa fa-birthday-cake margin-r-5"></i> Fecha de nacimiento</strong>
        <p class="text-muted">
          {{ \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $profesor->fechaNacimiento)->format("d/m/Y") }}
        </p>
        <hr>    
        @endif                           
        <a href="{{ route("profesores.editar", $profesor->id)}}" class="btn btn-primary btn-block"><b>Editar datos</b></a>
      </div>
    </div>
  </div>
  <div class="col-sm-9">
    <div class="box box-primary">        
      <div class="box-body">
        <div class="form-group">
          <div class="col-sm-8">
            <a href="{{ route("profesores.crear")}}" class="btn btn-primary btn-clean">Nuevo profesor</a> 
          </div>           
          <div class="col-sm-4">
            {{ Form::select("",App\Models\Profesor::listarBusqueda(), $profesor->id, ["id"=>"sel-profesor", "class" => "form-control", "data-seccion" => "perfil", "style" => "width: 100%"]) }}
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
          @include("util.historial", ["idEntidad" => $profesor->id]) 
        </div>
        <div id="pago" class="tab-pane">
          @include("profesor.pago.principal", ["idProfesor" => $profesor->id])
        </div>
        <div id="clase" class="tab-pane">
          @include("profesor.clase.principal", ["idProfesor" => $profesor->id])
        </div>
        <div id="calendario" class="tab-pane">
          @include("util.calendario", ["idEntidad" => $profesor->id, "esEntidadProfesor" => 1]) 
        </div>
        @include("profesor.pago.datos") 
      </div>
    </div>
  </div>
</div>
@endsection