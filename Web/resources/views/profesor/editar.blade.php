@extends("layouts.master")
@section("titulo", "Profesores")

@section("section_script")
<script src="{{ asset("assets/eah/js/modulos/profesor/profesor.js") }}"></script>
@endsection

@section("breadcrumb")
<li><a href="{{ route("profesores") }}">Profesores</a></li>
<li class="active">Editar</li>
@endsection

@section("content") 
@include("partials/errors")
{{ Form::model($profesor, ["method" => "PATCH", "action" => ["ProfesorController@update", $profesor->id], "id" => "formulario-profesor", "class" => "form-horizontal", "files" => true]) }}
@include("profesor.formulario")
{{ Form::close() }}
@endsection
