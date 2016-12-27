@extends("layouts.master")
@section("titulo", "Profesores")

@section("section_script")
<script src="{{ asset("assets/eah/js/modulos/profesor/profesor.js") }}"></script>
@endsection

@section("breadcrumb")
<li><a href="{{ route("profesores") }}">Profesores</a></li>
<li class="active">Perfil</li>
@endsection

@section("content")
<div class="row">
  <div class="col-sm-3">
    <div class="box box-primary">
      <div class="box-body box-profile">
        <img class="profile-user-img img-responsive img-circle" src="{{ route("imagenes", ["rutaImagen" => (isset($profesor->rutaImagenPerfil) && $profesor->rutaImagenPerfil != "" ? $profesor->rutaImagenPerfil : "-")]) }}" alt="User profile picture">
        <h3 class="profile-username">Profesor {!! $profesor->nombre . " " .  $profesor->apellido !!}</h3>
        <p class="text-muted">{!! $profesor->correoElectronico !!}</p>
        <span class="label {!! $estadosProfesor[$profesor->estado][1] !!} btn_estado">{!! $estadosProfesor[$profesor->estado][0] !!}</span>
      </div>
    </div>
    <div class="sec-datos box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">Datos principales del profesor</h3>
      </div>
      <div class="box-body">
        <strong><i class="fa fa-fw fa-calendar"></i> Horario</strong>
        <p class="text-muted">
          @include("util.horario", ["horario" => $profesor->horario, "modo" => "visualizar"])
        </p>
        <hr>
        <strong><i class="fa fa-map-marker margin-r-5"></i> Dirección</strong>
        <p class="text-muted">{!! $profesor->direccion !!}<br/>{!! $profesor->direccionUbicacion !!}</p>
        <p class="text-muted">
          @include("util.ubicacionMapa", ["geoLatitud" => $profesor->geoLatitud, "geoLongitud" => $profesor->geoLongitud, "modo" => "visualizar"])
        </p>
        <hr>
        <strong><i class="fa fa-user margin-r-5"></i> {!! $tiposDocumentos[$profesor->idTipoDocumento] !!}</strong>
        <p class="text-muted">
          {!! $profesor->numeroDocumento !!}
        </p>
        <hr>
        <strong><i class="fa fa-phone margin-r-5"></i> Teléfono</strong>
        <p class="text-muted">
          {!! $profesor->telefono !!}
        </p>
        <hr>
        <strong><i class="fa fa-birthday-cake margin-r-5"></i> Fecha de nacimiento</strong>
        <p class="text-muted">
          {!! \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $profesor->fechaNacimiento)->format("d/m/Y") !!}
        </p>
        <hr>                              
        <a href="{{ route("profesores.editar", $profesor->id)}}" class="btn btn-primary btn-block"><b>Editar datos</b></a>
      </div>
    </div>
  </div>
  <div class="col-sm-9">
    <div class="nav-tabs-custom">
      <ul class="nav nav-tabs">
        <li class="active"><a href="#historial" data-toggle="tab">Historial</a></li>
        <li><a href="#pago" data-toggle="tab">Detalle económico</a></li>
        <li><a href="#clase" data-toggle="tab">Clases</a></li>
      </ul>
      <div class="tab-content">
        <div class="active tab-pane" id="historial">
          @include("util.historial", ["idEntidad" => $profesor->id]) 
        </div>
        <div class="tab-pane" id="pago">
          @include("profesor.pago.principal", ["idProfesor" => $profesor->id])
        </div>
        <div class="tab-pane" id="clase">
          @include("profesor.clase.principal", ["idProfesor" => $profesor->id])
        </div>
      </div>
    </div>
  </div>
</div>
@endsection