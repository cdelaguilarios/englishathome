@extends("layouts.master")
@section("titulo", "Alumnos")

@section("section_script")
<script>
  var urlEditar = "{{ route('alumnos.editar', ['id' => 0]) }}";
  var urlBuscar = "{{ route('alumnos.buscar') }}";
  
  var idAlumno = "{{ $alumno->id}}";
  var nombreCompletoAlumno = "{{ $alumno->nombre . " " .  $alumno->apellido }}";
</script>
<script src="{{ asset("assets/eah/js/modulos/alumno/formulario.js") }}"></script>
<script src="{{ asset("assets/eah/js/modulos/alumno/busqueda.js") }}"></script>
@endsection

@section("breadcrumb")
<li><a href="{{ route("alumnos") }}">Alumnos</a></li>
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
            <a href="{{ route("alumnos.crear")}}" class="btn btn-primary btn-clean">Nuevo alumno</a>
            <a href="{{ route("alumnos.perfil", ["id" => $alumno->id]) }}" type="button" class="btn btn-primary"><i class="fa fa-eye"></i> Ver perfil</a>
          </div>         
          <div class="col-sm-2">
            @if(isset($alumno->idAlumnoSiguiente))
            <a href="{{ route("alumnos.editar", ["id" => $alumno->idAlumnoSiguiente]) }}" class="btn btn-default pull-right"><span class="glyphicon glyphicon-arrow-right"></span></a>
            @endif
            @if(isset($alumno->idAlumnoAnterior))
            <a href="{{ route("alumnos.editar", ["id" => $alumno->idAlumnoAnterior]) }}" class="btn btn-default pull-right"><span class="glyphicon glyphicon-arrow-left"></span></a>
            @endif
          </div>     
          <div class="col-sm-4">
            {{ Form::select("", [], null, ["id"=>"sel-alumno", "class" => "form-control", "data-seccion" => "editar", "style" => "width: 100%"]) }}
          </div>
        </div> 
      </div>
    </div>
  </div>
</div>
{{ Form::model($alumno, ["method" => "PATCH", "action" => ["AlumnoController@actualizar", $alumno->id], "id" => "formulario-alumno", "class" => "form-horizontal", "files" => true]) }}
@include("alumno.formulario")
{{ Form::close() }}
@endsection