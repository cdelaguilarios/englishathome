@extends("layouts.master")
@section("titulo", "Usuarios")

@section("section_script")
<script src="{{ asset("assets/eah/js/modulos/usuario.js")}}"></script>
@endsection

@section("breadcrumb")
<li><a href="{{ route("usuarios") }}">Usuarios</a></li>
<li class="active">Nuevo</li>
@endsection

@section("content") 
@include("partials/errors")
{!! Form::open(["url" => "usuarios", "id" => "formulario_usuario", "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) !!}
@include("usuario.formulario", ["modo" => "registrar"])
{!! Form::close() !!}
@endsection