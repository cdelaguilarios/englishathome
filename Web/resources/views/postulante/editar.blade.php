@extends("layouts.master")
@section("titulo", "Postulantes")

@section("section_script")
<script>
  var urlEditar = "{{ route('postulantes.editar', ['id' => 0]) }}";
</script>
<script src="{{ asset("assets/eah/js/modulos/postulante.js") }}"></script>
@endsection

@section("breadcrumb")
<li><a href="{{ route("postulantes") }}">Postulantes</a></li>
<li class="active">Editar</li>
@endsection

@section("content") 
@include("partials/errors")
<div class="row">
  <div class="col-sm-12">
    <div class="box box-primary">        
      <div class="box-body">
        <div class="form-group">
          <div class="col-sm-6">
            <a href="{{ route("postulantes.crear")}}" class="btn btn-primary btn-clean">Nuevo postulante</a>
            <a href="{{ route("postulantes.perfil", ["id" => $postulante->id]) }}" type="button" class="btn btn-primary"><i class="fa fa-eye"></i> Ver perfil</a>
            @if($postulante->estado != App\Helpers\Enum\EstadosPostulante::ProfesorRegistrado)
            <button id="btn-registrar-profesor"  type="button" class="btn btn-primary"><i class="fa flaticon-teach"></i> Registrar como profesor</button>
            @endif
          </div>    
          <div class="col-sm-2">
            @if(isset($postulante->idPostulanteSiguiente))
            <a href="{{ route("postulantes.editar", ["id" => $postulante->idPostulanteSiguiente]) }}" class="btn btn-default pull-right"><span class="glyphicon glyphicon-arrow-right"></span></a>
            @endif
            @if(isset($postulante->idPostulanteAnterior))
            <a href="{{ route("postulantes.editar", ["id" => $postulante->idPostulanteAnterior]) }}" class="btn btn-default pull-right"><span class="glyphicon glyphicon-arrow-left"></span></a>
            @endif
          </div>           
          <div class="col-sm-4">
            {{ Form::select("",App\Models\Postulante::listarBusqueda(), $postulante->id, ["id"=>"sel-postulante", "class" => "form-control", "data-seccion" => "editar", "style" => "width: 100%"]) }}
          </div>
        </div> 
      </div>
    </div>
  </div>
</div>
{{ Form::model($postulante, ["method" => "PATCH", "action" => ["PostulanteController@actualizar", $postulante->id], "id" => "formulario-postulante", "class" => "form-horizontal", "files" => true]) }}
@include("postulante.formulario")
{{ Form::close() }}
@endsection
