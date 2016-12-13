@extends("layouts.master")
@section("titulo", "Alumnos")

@section("section_script")
<script src="{{ asset('assets/eah/js/modulos/alumno/alumno.js') }}"></script>
@endsection

@section("breadcrumb")
<li><a href="{{ route('alumnos') }}">Alumnos</a></li>
<li class="active">Nuevo</li>
@endsection

@section("content") 
@include("partials/errors")
{{ Form::open(["url" => "alumnos", "id" => "formulario-alumno", "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }}
@include("alumno.formulario", ["modo" => "registrar"])
{{ Form::close() }}
@endsection