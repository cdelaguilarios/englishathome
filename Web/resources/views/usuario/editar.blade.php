@extends("layouts.master")
@section("titulo", "Usuarios")

@section("section_script")
<script>
  var urlEditar = "{{ route('usuarios.editar', ['id' => 0]) }}";
</script>
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
<div class="row">
  <div class="col-sm-12">
    <div class="box box-primary">        
      <div class="box-body">
        <div class="form-group">
          <div class="col-sm-8">
            <a href="{{ route("usuarios.crear")}}" class="btn btn-primary btn-clean">Nuevo usuario</a>
          </div>           
          <div class="col-sm-4">
            {{ Form::select("",App\Models\Usuario::listarBusqueda(), $usuario->id, ["id"=>"sel-usuario", "class" => "form-control", "data-seccion" => "editar", "style" => "width: 100%;"]) }}
          </div>
        </div> 
      </div>
    </div>
  </div>
</div>
{{ Form::model($usuario, ["method" => "PATCH", "action" => ["UsuarioController@actualizar", $usuario->id], "id" => "formulario-usuario", "class" => "form-horizontal", "files" => true]) }}
@include("usuario.formulario")
{{ Form::close() }}
@endsection
