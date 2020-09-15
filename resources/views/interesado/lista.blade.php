@extends("layouts.master")
@section("titulo", "Interesados")

@section("section_script")
<script>
  var urlListar = "{{ route('interesados.listar') }}";
  var urlPerfilAlumnoInteresado = "{{ route('interesados.perfil.alumno', ['id' => 0]) }}";
  var urlEditar = "{{ route('interesados.editar', ['id' => 0]) }}";
  var urlCotizar = "{{ route('interesados.cotizar', ['id' => 0]) }}";
  var urlEliminar = "{{ route('interesados.eliminar', ['id' => 0]) }}";
  
  var estados = {!! json_encode(App\Helpers\Enum\EstadosInteresado::listar()) !!};
  var origenes = {!! json_encode(App\Helpers\Enum\OrigenesInteresado::listar()) !!};
  var estadosDisponibleCambio = {!! json_encode(App\Helpers\Enum\EstadosInteresado::listarDisponibleCambio()) !!};
  var estadoFichaCompleta = "{{ App\Helpers\Enum\EstadosInteresado::FichaCompleta }}";
  var estadoAlumnoRegistrado = "{{ App\Helpers\Enum\EstadosInteresado::AlumnoRegistrado }}";
</script>
<script src="{{ asset("assets/eah/js/modulos/interesado/lista.js") }}"></script>
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
            {{ Form::select("estado", App\Helpers\Enum\EstadosInteresado::listarBusqueda(), App\Helpers\Enum\EstadosInteresado::PendienteInformacion, ["id"=>"bus-estado", "class" => "form-control", "placeholder" => "Todos", "data-idtabla" => "tab-lista-interesados"]) }}
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
        <table id="tab-lista-interesados" class="table table-bordered table-hover">
          <thead>
            <tr>
              <th>N°</th> 
              <th>Nombre completo</th>   
              <th>Consulta</th>  
              <th>Datos de contacto</th>
              <th>Estado</th>
              <th>Fecha registro</th>
              <th class="all">Opciones</th>
              <!--- Columnas ocultas solo para exportación excel --->
              <th>Nombres</th>
              <th>Apellidos</th>
              <th>Teléfono</th>
              <th>Correo electrónico</th>
              <th>Consulta</th>
              <th>Curso de interes</th>
              <th>Origen</th>
              <th>Costo por hora de clase</th>
              <th>Comentarios adicionales</th>
              <th>Estado</th>
              <th>Fecha de registro</th>
              <!---------------------------------------------------->
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>
<div style="display: none">
  {{ Form::select("", App\Helpers\Enum\EstadosInteresado::listarDisponibleCambio(), null, ["id" => "sel-estados", "class" => "form-control", "data-urlactualizar" => route('interesados.actualizar.estado', ['id' => 0]), "data-estados" => json_encode(App\Helpers\Enum\EstadosInteresado::listar())]) }}
</div>
@endsection
