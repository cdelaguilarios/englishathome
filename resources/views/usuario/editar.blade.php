@extends((in_array($usuarioActual->tipo, [App\Helpers\Enum\TiposEntidad::Alumno, App\Helpers\Enum\TiposEntidad::Profesor]) ? "externo.layouts.master" : "layouts.master"))
@section("titulo", "Usuarios")

@section("section_script")
<script>
  var urlEditar = "{{ route('usuarios.editar', ['id' => 0]) }}";
  var urlBuscar = "{{ route('usuarios.buscar') }}";
  
  var idUsuario = "{{ $usuario->id}}";
  var nombreCompletoUsuario = "{{ $usuario->nombre . " " .  $usuario->apellido }}";
</script>
<script src="{{ asset("assets/eah/js/modulos/usuario/formulario.js")}}"></script>
<script src="{{ asset("assets/eah/js/modulos/usuario/busqueda.js") }}"></script>
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
@if($usuarioActual->rol == App\Helpers\Enum\RolesUsuario::Principal)
<div class="row">
  <div class="col-sm-12">
    <div class="box box-primary">        
      <div class="box-body">
        <div class="form-group">
          <div class="col-sm-6">
            <a href="{{ route("usuarios.crear")}}" class="btn btn-primary btn-clean">Nuevo usuario</a>
          </div>            
          <div class="col-sm-2">
            @if(isset($usuario->idUsuarioSiguiente))
            <a href="{{ route("usuarios.editar", ["id" => $usuario->idUsuarioSiguiente]) }}" class="btn btn-default pull-right"><span class="glyphicon glyphicon-arrow-right"></span></a>
            @endif
            @if(isset($usuario->idUsuarioAnterior))
            <a href="{{ route("usuarios.editar", ["id" => $usuario->idUsuarioAnterior]) }}" class="btn btn-default pull-right"><span class="glyphicon glyphicon-arrow-left"></span></a>
            @endif
          </div>      
          <div class="col-sm-4">
            {{ Form::select("", [], null, ["id"=>"sel-usuario", "class" => "form-control", "data-seccion" => "editar", "style" => "width: 100%"]) }}
          </div>
        </div> 
      </div>
    </div>
  </div>
</div>
@endif
{{ Form::model($usuario, ["method" => "PATCH", "action" => ["UsuarioController@actualizar", $usuario->id], "id" => "formulario-usuario", "class" => "form-horizontal", "files" => true]) }}
@include("usuario.formulario")
{{ Form::close() }}
@endsection
