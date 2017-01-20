@extends("layouts.master")
@section("titulo", "Usuarios")

@section("section_script")
<script src="{{ asset("assets/eah/js/modulos/usuario.js")}}"></script>
@endsection

@section("breadcrumb")
@if($usuarioActual->rol == App\Helpers\Enum\RolesUsuario::Principal)
<li><a href="{{ route("usuarios") }}">Usuarios</a></li>
@else
<li>Mi cuenta</li>
@endif
<li class="active">Editar</li>
@endsection

@section("content") 
@include("partials/errors")
{{ Form::model($usuario, ["method" => "PATCH", "action" => ["UsuarioController@actualizar", $usuario->id], "id" => "formulario-usuario", "class" => "form-horizontal", "files" => true]) }}
@include("usuario.formulario")
{{ Form::close() }}
@endsection
