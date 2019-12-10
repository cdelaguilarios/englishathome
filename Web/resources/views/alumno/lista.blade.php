{{----}}
@extends("layouts.master")
@section("titulo", "Alumnos")

@section("section_script")
<script>
  var urlListar = "{{ route('alumnos.listar') }}";
  var urlEditar = "{{ route('alumnos.editar', ['id' => 0]) }}";
  var urlEliminar = "{{ route('alumnos.eliminar', ['id' => 0]) }}";
  var urlHorarioMultiple = "{{ route('horario.multiple') }}";
  var urlListarClases = "{{ route('alumnos.clases.listar', ['id' => 0]) }}";

  var estados = {!! json_encode(App\Helpers\Enum\EstadosAlumno::listar()) !!};
  var estadosDisponibleCambio = {!! json_encode(App\Helpers\Enum\EstadosAlumno::listarDisponibleCambio()) !!};
</script>
<script src="{{ asset("assets/eah/js/modulos/util/horario.js") }}"></script>
<script src="{{ asset("assets/eah/js/modulos/alumno/lista.js") }}"></script>
@endsection

@section("breadcrumb")
<li class="active">Alumnos</li>
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
            {{ Form::select("estado", App\Helpers\Enum\EstadosAlumno::listarBusqueda(), App\Helpers\Enum\EstadosAlumno::Activo, ["id"=>"bus-estado", "class" => "form-control", "placeholder" => "Todos", "data-idtabla" => "tab-lista-alumnos"]) }}
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
        <h3 class="box-title">Lista de alumnos</h3>
        <a href="{{ route("alumnos.crear")}}" class="btn btn-primary btn-clean">Nuevo alumno</a>   
      </div>         
      <div class="box-body">
        <table id="tab-lista-alumnos" class="table table-bordered table-hover">
          <thead>
            <tr>
              <th>N°</th> 
              <th>Nombre completo/Distrito</th>   
              <th>Avance de clases</th>  
              <th>Curso</th>    
              <th>Estado/Nivel</th>
              <th>Pagos<br/>(Bolsa de horas)</th>
              <th>Fecha de registro</th>
              <th class="all">Opciones</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>
<div id="mod-lista-clases" class="modal" data-keyboard="false" style="text-align: initial">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Lista de clases</h4>
      </div>
      <div class="modal-body">   
        <div class="row">     
          @include("util.listaClases")
        </div>
      </div>
    </div>
  </div>
</div>
<div style="display: none">
  {{ Form::select("", App\Helpers\Enum\EstadosAlumno::listarDisponibleCambio(), null, ["id" => "sel-estados", "class" => "form-control", "data-urlactualizar" => route('alumnos.actualizar.estado', ['id' => 0]), "data-estados" => json_encode(App\Helpers\Enum\EstadosAlumno::listar())]) }}
</div>
@endsection
