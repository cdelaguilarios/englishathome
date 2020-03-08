{{----}}
@extends("layouts.master")
@section("titulo", "Interesados")

@section("section_script")
<script>
  var urlEditar = "{{ route('interesados.editar', ['id' => 0]) }}";
  var urlBuscar = "{{ route('interesados.buscar') }}";
  
  var idInteresado = "{{ $interesado->id}}";
  var nombreCompletoInteresado = "{{ $interesado->nombre . " " .  $interesado->apellido }}";
</script>
<script src="{{ asset("assets/eah/js/modulos/interesado/formulario.js") }}"></script>
<script src="{{ asset("assets/eah/js/modulos/interesado/busqueda.js") }}"></script>
@endsection

@section("breadcrumb")
<li><a href="{{ route("interesados") }}">Interesados</a></li>
<li class="active">Editar</li>
@endsection

@section("content") 
@include("partials/errors")
<div class="row">
  <div class="col-sm-12">
    <div class="box box-primary">        
      <div class="box-body">
        <div class="form-group">
          <div class="col-sm-7">
            <a href="{{ route("interesados.crear")}}" class="btn btn-primary btn-clean">Nuevo interesado</a>
            @if($interesado->estado != App\Helpers\Enum\EstadosInteresado::AlumnoRegistrado)
            <button id="btn-registrar-alumno"  type="button" class="btn btn-primary"><i class="fa fa-mortar-board"></i> Registrar como alumno</button>
            @endif
            <a href="{{ route("interesados.cotizar", ["id" => $interesado->idEntidad]) }}" type="button" class="btn btn-primary" ><i class="fa fa-envelope"></i> Enviar cotización</a>             
            <button class="btn btn-primary" onclick="return busquedaInteresado.copiarEnlaceFichaInscripcion('{{ route("alumnos.crear.externo", ["codigoVerificacion" => Crypt::encrypt($interesado->id)]) }}')">Enlace ficha de inscripción</button>
          </div>    
          <div class="col-sm-2">
            @if(isset($interesado->idInteresadoSiguiente))
            <a href="{{ route("interesados.editar", ["id" => $interesado->idInteresadoSiguiente]) }}" class="btn btn-default pull-right"><span class="glyphicon glyphicon-arrow-right"></span></a>
            @endif
            @if(isset($interesado->idInteresadoAnterior))
            <a href="{{ route("interesados.editar", ["id" => $interesado->idInteresadoAnterior]) }}" class="btn btn-default pull-right"><span class="glyphicon glyphicon-arrow-left"></span></a>
            @endif
          </div>
          <div class="col-sm-3">
            {{ Form::select("", [], null, ["id"=>"sel-interesado", "class" => "form-control", "data-seccion" => "editar", "style" => "width: 100%"]) }}
          </div> 
        </div> 
      </div>
    </div>
  </div>
  <div class="col-sm-12">
    <div class="nav-tabs-custom">
      <ul class="nav nav-tabs">
        <li class="active"><a href="#datos" data-toggle="tab">Datos</a></li>
        <li><a href="#historial" data-toggle="tab">Historial</a></li>
      </ul>
      <div class="tab-content">
        <div class="active tab-pane" id="datos">
          {{ Form::model($interesado, ["method" => "PATCH", "action" => ["InteresadoController@actualizar", $interesado->id], "id" => "formulario-interesado", "class" => "form-horizontal", "files" => true]) }}
          @include("interesado.formulario")
          {{ Form::close() }}
        </div>
        <div class="tab-pane" id="historial">
          @include("util.notificacion.principalHistorial", ["idEntidad" => $interesado->id, "nombreEntidad" => "interesado"]) 
        </div>
      </div>
    </div>
  </div>
</div>
@endsection