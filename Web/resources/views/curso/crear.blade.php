{{----}}
@extends("layouts.master")
@section("titulo", "Cursos")

@section("section_script")
<script src="{{ asset("assets/eah/js/modulos/curso/formulario.js")}}"></script>
@endsection

@section("breadcrumb")
<li><a href="{{ route("cursos") }}">Cursos</a></li>
<li class="active">Nuevo</li>
@endsection

@section("content") 
@include("partials/errors")
{{ Form::open(["url" => route("cursos.registrar"), "id" => "formulario-curso", "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }}
@include("curso.formulario", ["modo" => "registrar"])
{{ Form::close() }}
@endsection