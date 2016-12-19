@extends("layouts.master")
@section("titulo", "Alumnos")

@section("section_script")
<script src="{{ asset("assets/eah/js/modulos/alumno/alumno.js") }}"></script>
@endsection

@section("breadcrumb")
<li><a href="{{ route("alumnos") }}">Alumnos</a></li>
<li class="active">Editar</li>
@endsection

@section("content") 
@include("partials/errors")
{{ Form::model($alumno, ["method" => "PATCH", "action" => ["AlumnoController@update", $alumno->id], "id" => "formulario-alumno", "class" => "form-horizontal", "files" => true]) }}
@include("alumno.formulario")
{{ Form::close() }}
@endsection
