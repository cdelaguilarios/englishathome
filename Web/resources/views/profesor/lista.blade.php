@extends("layouts.master")
@section("titulo", "Profesores")

@section("section_script")
<script>
  var urlListar = "{{ route('profesores.listar') }}";
  var urlPerfil = "{{ route('profesores.perfil', ['id' => 0]) }}";
  var urlEditar = "{{ route('profesores.editar', ['id' => 0]) }}";
  var urlActualizarEstado = "{{ route('profesores.actualizar.estado', ['id' => 0]) }}";
  var urlEliminar = "{{ route('profesores.eliminar', ['id' => 0]) }}";
  var estados = {!! json_encode(App\Helpers\Enum\EstadosProfesor::listar()) !!};
</script>
<script src="{{ asset("assets/eah/js/modulos/profesor/profesor.js") }}"></script>
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
          {{ Form::label("bus-estado", "Búsqueda por estado: ", ["class" => "col-sm-2 control-label"]) }}
          <div class="col-sm-3">
            {{ Form::select("estado", App\Helpers\Enum\EstadosProfesor::listarSimple(), App\Helpers\Enum\EstadosProfesor::Activo, ["id"=>"bus-estado", "class" => "form-control", "placeholder" => "Todos"]) }}
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
        <table id="tab-lista" class="table table-bordered table-hover">
          <thead>
            <tr>
              <th>Nombre completo</th>     
              <th>Correo electrónico</th>
              <th>Estado</th>
              <th class="all">Opciones</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>
<div style="display: none">
  {{ Form::select("", App\Helpers\Enum\EstadosProfesor::listarSimple(), NULL, ["id" => "sel-estados", "class" => "form-control"]) }}
</div>
@endsection
