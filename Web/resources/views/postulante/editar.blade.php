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
          <div class="col-sm-8">
            <a href="{{ route("postulantes.crear")}}" class="btn btn-primary btn-clean">Nuevo postulante</a>
          </div>           
          <div class="col-sm-4">
            {{ Form::select("",App\Models\Postulante::listarBusqueda(), $postulante->id, ["id"=>"sel-postulante", "class" => "form-control", "data-seccion" => "editar", "style" => "width: 100%;"]) }}
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
