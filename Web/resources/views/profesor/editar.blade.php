@extends("layouts.master")
@section("titulo", "Profesores")

@section("section_script")
<script>
  var urlEditar = "{{ route('profesores.editar', ['id' => 0]) }}";
  var urlBuscar = "{{ route('profesores.buscar') }}";
  var idProfesor = "{{ $profesor->id}}";
  var nombreCompletoProfesor = "{{ $profesor->nombre . " " .  $profesor->apellido }}";
</script>
<script src="{{ asset("assets/eah/js/modulos/profesor/profesor.js") }}"></script>
@endsection

@section("breadcrumb")
<li><a href="{{ route("profesores") }}">Profesores</a></li>
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
            <a href="{{ route("profesores.crear")}}" class="btn btn-primary btn-clean">Nuevo profesor</a>
            <a href="{{ route("profesores.perfil", ["id" => $profesor->id]) }}" type="button" class="btn btn-primary"><i class="fa fa-eye"></i> Ver perfil</a>
          </div>           
          <div class="col-sm-4">
            {{ Form::select("", [], null, ["id"=>"sel-profesor", "class" => "form-control", "data-seccion" => "editar", "style" => "width: 100%"]) }}
          </div>
        </div> 
      </div>
    </div>
  </div>
</div>
{{ Form::model($profesor, ["method" => "PATCH", "action" => ["ProfesorController@actualizar", $profesor->id], "id" => "formulario-profesor", "class" => "form-horizontal", "files" => true]) }}
@include("profesor.formulario")
{{ Form::close() }}
@endsection
