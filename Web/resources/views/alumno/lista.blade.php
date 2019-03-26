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
  var urlHorarioMultiple = "{{ route('horario.multiple') }}";
  var urlListarClases = "{{ route('alumnos.clases.listar') }}";
  var estados = {!! json_encode(App\Helpers\Enum\EstadosAlumno::listar()) !!};
  var estadosCambio = {!! json_encode(App\Helpers\Enum\EstadosAlumno::listarCambio()) !!};
  var estadoCuotaProgramada = "{{ App\Helpers\Enum\EstadosAlumno::CuotaProgramada }}";</script>
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
        <div id="sec-men-lista-clases"></div><br/>
        <div class="row">
          <div class="col-sm-12">
            <div class="box box-info">       
              <div class="box-body">
                <table id="tab-lista-clases" class="table table-bordered table-hover">
                  <thead>
                    <tr>
                      <th>N°</th>    
                      <th>Datos</th>
                      <th>Estado</th>
                      <th>Comentarios</th>
                    </tr>
                  </thead>
                </table>
                {{ Form::hidden("idAlumno") }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div id="mod-comentarios" class="modal" data-keyboard="false" style="text-align: initial">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Comentarios</h4>
      </div>
      {{ Form::open(["url" => route("alumnos.clases.actualizar.comentarios"), "id" => "formulario-comentarios", "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }}
      <div class="modal-body">
        <div class="row">
          <div class="col-sm-12">
            <div class="box-body">
              <div class="form-group">
                <div class="col-sm-12">
                  {{ Form::textarea("comentario", null, ["class" => "form-control", "rows" => "10", "maxlength" =>"8000"]) }}
                </div>
              </div>
              {{ Form::hidden("idClase") }}
              {{ Form::hidden("idAlumno") }}
              {{ Form::hidden("tipo") }}
            </div>   
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success btn-sm">Guardar</button>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>
<div style="display: none">
  {{ Form::select("", App\Helpers\Enum\EstadosAlumno::listarCambio(), null, ["id" => "sel-estados", "class" => "form-control"]) }}
</div>
@endsection
