@extends("layouts.master")
@section("titulo", "Profesores")

@section("section_script")
<script src="{{ asset("assets/eah/js/modulos/profesor/profesor.js") }}"></script>
@endsection

@section("breadcrumb")
<li><a href="{{ route("profesores") }}">Profesores</a></li>
<li class="active">Nuevo</li>
@endsection

@section("content") 
@include("partials/errors")
{{ Form::open(["url" => route("profesores.registrar"), "id" => "formulario-profesor", "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }}
@include("profesor.formulario", ["modo" => "registrar"])
{{ Form::close() }}
@endsection