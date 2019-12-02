{{----}}
@extends("layouts.master")
@section("titulo", "Profesores")

@section("section_script")
<script>
  var urlListar = "{{ route('profesores.listar') }}";
  var urlEditar = "{{ route('profesores.editar', ['id' => 0]) }}";
  var urlEliminar = "{{ route('profesores.eliminar', ['id' => 0]) }}";
  
  var estados = {!! json_encode(App\Helpers\Enum\EstadosProfesor::listar()) !!};
  var estadosDisponibleCambio = {!! json_encode(App\Helpers\Enum\EstadosProfesor::listarDisponibleCambio()) !!};
</script>
<script src="{{ asset("assets/eah/js/modulos/profesor/lista.js") }}"></script>
@endsection

@section("breadcrumb")
<li class="active">Profesores</li>
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
            {{ Form::select("estado", App\Helpers\Enum\EstadosProfesor::listarBusqueda(), null, ["id"=>"bus-estado", "class" => "form-control", "placeholder" => "Todos", "data-idtabla" => "tab-lista-profesores"]) }}
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
        <h3 class="box-title">Lista de profesores</h3>
        <a href="{{ route("profesores.crear")}}" class="btn btn-primary btn-clean">Nuevo profesor</a>   
      </div>         
      <div class="box-body">
        <table id="tab-lista-profesores" class="table table-bordered table-hover">
          <thead>
            <tr>
              <th>N°</th> 
              <th>Nombre completo</th>     
              <th>Correo electrónico</th>
              <th>Teléfono</th>
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
  {{ Form::select("", App\Helpers\Enum\EstadosProfesor::listarDisponibleCambio(), null, ["id" => "sel-estados", "class" => "form-control", "data-urlactualizar" => route('profesores.actualizar.estado', ['id' => 0]), "data-estados" => json_encode(App\Helpers\Enum\EstadosProfesor::listar())]) }}
</div>
@endsection
