@extends("layouts.master")
@section("titulo", "Alumnos")

@section("section_script")
<script>
  var urlListar = "{{ route('alumnos.listar') }}";
  var urlPerfil = "{{ route('alumnos.perfil', ['id' => 0]) }}";
  var urlEditar = "{{ route('alumnos.editar', ['id' => 0]) }}";
  var urlActualizarEstado = "{{ route('alumnos.actualizar.estado', ['id' => 0]) }}";
  var urlEliminar = "{{ route('alumnos.eliminar', ['id' => 0]) }}";
  var urlPerfilProfesor = "{{ route('profesores.perfil', ['id' => 0]) }}";  
  var urlHorario = "{{ route('horario', ['id' => 0]) }}";
  var estados = {!! json_encode(App\Helpers\Enum\EstadosAlumno::listar()) !!};
  var estadosCambio = {!! json_encode(App\Helpers\Enum\EstadosAlumno::listarCambio()) !!};
  var estadoCuotaProgramada = "{{ App\Helpers\Enum\EstadosAlumno::CuotaProgramada }}";
</script>
<script src="{{ asset("assets/eah/js/modulos/alumno/alumno.js") }}"></script>
<script src="{{ asset("assets/eah/js/horario.js") }}"></script>
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
            {{ Form::select("estado", App\Helpers\Enum\EstadosAlumno::listarBusqueda(), App\Helpers\Enum\EstadosAlumno::Activo, ["id"=>"bus-estado", "class" => "form-control", "placeholder" => "Todos"]) }}
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
        <table id="tab-lista" class="table table-bordered table-hover">
          <thead>
            <tr>
              <th>N°</th> 
              <th>Nombre completo/Distrito</th>   
              <th>Avance de clases</th>  
              <th>Curso</th>    
              <th>Estado/Nivel</th>
              <th>Pago acumulado</th>
              <th class="all">Opciones</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>
<div style="display: none">
  {{ Form::select("", App\Helpers\Enum\EstadosAlumno::listarCambio(), null, ["id" => "sel-estados", "class" => "form-control"]) }}
</div>
@endsection
