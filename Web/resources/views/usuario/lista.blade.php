@extends("layouts.master")
@section("titulo", "Usuarios")

@section("section_script")
<script type="text/javascript">
  var urlListar = "{{ route('usuarios.listar') }}";
  var urlEditar = "{{ route('usuarios.editar', ['id' => 0]) }}";
  var urlActualizarEstado = "{{ route('usuarios.actualizar.estado', ['id' => 0]) }}";
  var urlEliminar = "{{ route('usuarios.eliminar', ['id' => 0]) }}";
  var roles = {!!  json_encode(App\Helpers\Enum\RolesUsuario::listarDelSistema()) !!};
  var estados = {!!  json_encode(App\Helpers\Enum\EstadosUsuario::listar()) !!};
  var estadosCambio = {!! json_encode(App\Helpers\Enum\EstadosUsuario::listarCambio()) !!};
</script>
<script src="{{ asset("assets/eah/js/modulos/usuario.js")}}"></script>
@endsection

@section("breadcrumb")
<li class="active">Usuarios</li>
@endsection

@section("content")
<div class="row">
  <div class="col-sm-12">
    <div class="box box-info">
      <div class="box-header">
        <h3 class="box-title">Filtros de búsquedad</h3> 
      </div>         
      <div class="box-body">
        <div class="form-group">          
          {{ Form::label("bus-estado", "Estado: ", ["class" => "col-sm-1 control-label"]) }}
          <div class="col-sm-3">
            {{ Form::select("estado", App\Helpers\Enum\EstadosUsuario::listarBusqueda(), App\Helpers\Enum\EstadosUsuario::Activo, ["id"=>"bus-estado", "class" => "form-control", "placeholder" => "Todos"]) }}
          </div>
        </div> 
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-sm-12">
    <div class="box box-info">
      <div class="box-header">
        <h3 class="box-title">Lista de usuarios</h3>
        <a href="{{ route("usuarios.crear")}}" class="btn btn-primary btn-clean">Nuevo usuario</a>   
      </div>         
      <div class="box-body">
        <table id="tab-lista" class="table table-bordered table-hover">
          <thead>
            <tr>
              <th>Nombre completo</th>     
              <th>Correo electrónico</th>
              <th>Rol</th>
              <th>Estado</th>
              <th>Fecha registro</th>
              <th class="all">Opciones</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>
<div style="display: none">
  {{ Form::select("", App\Helpers\Enum\EstadosUsuario::listarCambio(), null, ["id" => "sel-estados", "class" => "form-control"]) }}
</div>
@endsection
