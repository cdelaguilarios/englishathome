@extends("layouts.master")
@section("titulo", "Interesados")

@section("section_script")
<script src="{{ asset("assets/eah/js/modulos/interesado.js")}}"></script>
@endsection

@section("breadcrumb")
<li><a href="{{ route("interesados") }}">Interesados</a></li>
<li class="active">Editar</li>
@endsection

@section("content") 
@include("partials/errors")
{{ Form::model($interesado, ["method" => "PATCH", "action" => ["InteresadoController@actualizar", $interesado->id], "id" => "formulario-interesado", "class" => "form-horizontal", "files" => true]) }}
@include("interesado.formulario")
{{ Form::close() }}
@endsection
