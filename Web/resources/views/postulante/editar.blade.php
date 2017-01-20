@extends("layouts.master")
@section("titulo", "Posultantes")

@section("section_script")
<script src="{{ asset("assets/eah/js/modulos/postulante.js") }}"></script>
@endsection

@section("breadcrumb")
<li><a href="{{ route("postulantes") }}">Posultantes</a></li>
<li class="active">Editar</li>
@endsection

@section("content") 
@include("partials/errors")
{{ Form::model($postulante, ["method" => "PATCH", "action" => ["PostulanteController@actualizar", $postulante->id], "id" => "formulario-postulante", "class" => "form-horizontal", "files" => true]) }}
@include("postulante.formulario")
{{ Form::close() }}
@endsection
