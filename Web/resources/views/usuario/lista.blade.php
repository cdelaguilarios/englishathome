@extends("layouts.master")
@section("titulo", "Usuarios")

@section("section_script")
<script type="text/javascript">
  var urlListar = "{{ route("usuarios.listar") }}";
  var urlEditar = "{{ route("usuarios.editar", ["id" => 0]) }}";
  var urlEliminar = "{{ route("usuarios.destroy", ["id" => 0]) }}";
  var roles = {!!  json_encode($roles) !!};
  var estados = {!!  json_encode(App\Helpers\Enum\EstadosUsuario::listar()) !!};</script>
<script src="{{ asset("assets/eah/js/modulos/usuario.js")}}"></script>
@endsection

@section("breadcrumb")
<li class="active">Usuarios</li>
@endsection

@section("content")
<div class="row">
  <div class="col-xs-12">
    <div class="box box-info">
      <div class="box-header">
        <h3 class="box-title">Lista de usuarios</h3>
        <a href="{{ route("usuarios.nuevo")}}" class="btn btn-primary btn-clean">Nuevo usuario</a>   
      </div>         
      <div class="box-body">
        <table id="tab_lista" class="table table-bordered table-hover">
          <thead>
            <tr>
              <th>Nombre completo</th>     
              <th>Correo electrónico</th>
              <th>Rol</th>
              <th>Estado</th>
              <th class="col-md-1">&nbsp;</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
