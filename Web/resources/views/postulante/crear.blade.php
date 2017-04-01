@extends("layouts.master")
@section("titulo", "Postulantes")

@section("section_script")
<script src="{{ asset("assets/eah/js/modulos/postulante.js") }}"></script>
@endsection

@section("breadcrumb")
<li><a href="{{ route("postulantes") }}">Postulantes</a></li>
<li class="active">Nuevo</li>
@endsection

@section("content") 
@include("partials/errors")
{{ Form::open(["url" => route("postulantes.registrar"), "id" => "formulario-postulante", "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }}
@include("postulante.formulario", ["modo" => "registrar"])
{{ Form::close() }}
@endsection