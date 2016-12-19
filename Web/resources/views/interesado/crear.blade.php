@extends("layouts.master")
@section("titulo", "Interesados")

@section("section_script")
<script src="{{ asset("assets/eah/js/modulos/interesado.js")}}"></script>
@endsection

@section("breadcrumb")
<li><a href="{{ route("interesados") }}">Interesados</a></li>
<li class="active">Nuevo</li>
@endsection

@section("content") 
@include("partials/errors")
{!! Form::open(["url" => "interesados", "id" => "formulario_interesado", "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) !!}
@include("interesado.formulario", ["modo" => "registrar"])
{!! Form::close() !!}
@endsection