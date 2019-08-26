@extends("layouts.master")
@section("titulo", "Postulantes")

@section("section_script")
<script>
  var urlListar = "{{ route('postulantes.listar') }}";
  var urlPerfil = "{{ route('postulantes.perfil', ['id' => 0]) }}";
  var urlEditar = "{{ route('postulantes.editar', ['id' => 0]) }}";
  var urlPerfilProfesorPostulante = "{{ route('postulantes.perfil.profesor', ['id' => 0]) }}";
  var urlEliminar = "{{ route('postulantes.eliminar', ['id' => 0]) }}";
  var estados = {!! json_encode(App\Helpers\Enum\EstadosPostulante::listar()) !!};
  var estadosDisponibleCambio = {!! json_encode(App\Helpers\Enum\EstadosPostulante::listarDisponibleCambio()) !!};
  var estadoProfesorRegistrado = "{{ App\Helpers\Enum\EstadosPostulante::ProfesorRegistrado }}";</script>
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
            {{ Form::select("estado", App\Helpers\Enum\EstadosPostulante::listarBusqueda(), null, ["id"=>"bus-estado", "class" => "form-control", "placeholder" => "Todos", "data-idtabla" => "tab-lista"]) }}
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
      <div class="box-footer">
        <span><b>Nota:</b> El enlace <b>{!! route("postulantes.crear.externo") !!}</b> permite el registro de postulantes sin necesidad de ingresar al sistema.</span>
      </div>
    </div>
  </div>
</div>
<div style="display: none">
  {{ Form::select("", App\Helpers\Enum\EstadosPostulante::listarDisponibleCambio(), null, ["id" => "sel-estados", "class" => "form-control", "data-urlactualizar" => route('postulantes.actualizar.estado', ['id' => 0]), "data-estados" => json_encode(App\Helpers\Enum\EstadosPostulante::listar())]) }}
</div>
@endsection
