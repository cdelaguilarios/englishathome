@extends("layouts.master")
@section("titulo", "Interesados")

@section("section_script")
<script>
  var urlListar = "{{ route('interesados.listar') }}";
  var urlEditar = "{{ route('interesados.editar', ['id' => 0]) }}";
  var urlCotizar = "{{ route('interesados.cotizar', ['id' => 0]) }}";
  var urlPerfilAlumnoInteresado = "{{ route('interesados.perfil.alumno', ['id' => 0]) }}";
  var urlActualizarEstado = "{{ route('interesados.actualizar.estado', ['id' => 0]) }}";
  var urlEliminar = "{{ route('interesados.eliminar', ['id' => 0]) }}";
  var estados = {!! json_encode(App\Helpers\Enum\EstadosInteresado::listar()) !!};
  var estadosCambio = {!! json_encode(App\Helpers\Enum\EstadosInteresado::listarCambio()) !!};
  var estadoAlumnoRegistrado = "{{ App\Helpers\Enum\EstadosInteresado::AlumnoRegistrado }}";
</script>
<script src="{{ asset("assets/eah/js/modulos/interesado.js") }}"></script>
@endsection

@section("breadcrumb")
<li class="active">Interesados</li>
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
            {{ Form::select("estado", App\Helpers\Enum\EstadosInteresado::listarBusqueda(), App\Helpers\Enum\EstadosInteresado::PendienteInformacion, ["id"=>"bus-estado", "class" => "form-control", "placeholder" => "Todos"]) }}
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
        <h3 class="box-title">Lista de interesados</h3>
        <a href="{{ route("interesados.crear")}}" class="btn btn-primary btn-clean">Nuevo interesado</a>   
      </div>         
      <div class="box-body">
        <table id="tab-lista" class="table table-bordered table-hover">
          <thead>
            <tr>
              <th>Nombre completo</th>   
              <th>Teléfono</th>  
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
  {{ Form::select("", App\Helpers\Enum\EstadosInteresado::listarCambio(), null, ["id" => "sel-estados", "class" => "form-control"]) }}
</div>
@endsection
