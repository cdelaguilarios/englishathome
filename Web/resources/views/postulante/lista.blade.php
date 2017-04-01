@extends("layouts.master")
@section("titulo", "Postulantes")

@section("section_script")
<script>
  var urlListar = "{{ route('postulantes.listar') }}";
  var urlEditar = "{{ route('postulantes.editar', ['id' => 0]) }}";
  var urlActualizarEstado = "{{ route('postulantes.actualizar.estado', ['id' => 0]) }}";
  var urlEliminar = "{{ route('postulantes.eliminar', ['id' => 0]) }}";
  var estados = {!! json_encode(App\Helpers\Enum\EstadosPostulante::listar()) !!};
  var estadosCambio = {!! json_encode(App\Helpers\Enum\EstadosPostulante::listarCambio()) !!};
</script>
<script src="{{ asset("assets/eah/js/modulos/postulante.js") }}"></script>
@endsection

@section("breadcrumb")
<li class="active">Postulantes</li>
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
            {{ Form::select("estado", App\Helpers\Enum\EstadosPostulante::listarBusqueda(), null, ["id"=>"bus-estado", "class" => "form-control", "placeholder" => "Todos"]) }}
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
        <h3 class="box-title">Lista de postulantes</h3>
        <a href="{{ route("postulantes.crear")}}" class="btn btn-primary btn-clean">Nuevo postulante</a>   
      </div>         
      <div class="box-body">
        <table id="tab-lista" class="table table-bordered table-hover">
          <thead>
            <tr>
              <th>Nombre completo</th>     
              <th>Correo electrónico</th>
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
  {{ Form::select("", App\Helpers\Enum\EstadosPostulante::listarCambio(), NULL, ["id" => "sel-estados", "class" => "form-control"]) }}
</div>
@endsection
