@extends("layouts.master")
@section("titulo", "Cursos")

@section("section_script")
<script>
  var urlEditar = "{{ route('cursos.editar', ['id' => 0]) }}";
</script>
<script src="{{ asset("assets/eah/js/modulos/curso.js")}}"></script>
@endsection

@section("breadcrumb")
<li><a href="{{ route("cursos") }}">Cursos</a></li>
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
            <a href="{{ route("cursos.crear")}}" class="btn btn-primary btn-clean">Nuevo curso</a>
          </div>           
          <div class="col-sm-4">
            {{ Form::select("",App\Models\Curso::listarSimple(), $curso->id, ["id"=>"sel-curso", "class" => "form-control", "data-seccion" => "editar", "style" => "width: 100%;"]) }}
          </div>
        </div> 
      </div>
    </div>
  </div>
</div>
{{ Form::model($curso, ["method" => "PATCH", "action" => ["CursoController@actualizar", $curso->id], "id" => "formulario-curso", "class" => "form-horizontal", "files" => true]) }}
@include("curso.formulario")
{{ Form::close() }}
@endsection
