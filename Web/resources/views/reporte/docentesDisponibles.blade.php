@extends("layouts.master")
@section("titulo", "Docentes disponibles")

@section("section_script")
<script>
  var urlListar = "{{ route('reporte.docentes.disponibles.listar') }}";
  var urlPerfil = "{{ route('profesores.perfil', ['id' => 0]) }}";
  var urlEditar = "{{ route('profesores.editar', ['id' => 0]) }}";
  var estados = {!! json_encode(App\Helpers\Enum\EstadosProfesor::listar()) !!};</script>
<script src="{{ asset("assets/eah/js/modulos/reporte/docenteDisponible.js")}}"></script>
@endsection

@section("breadcrumb")
<li class="active">Docentes disponibles</li>
@endsection

@section("content")
<div class="row">
  <div class="col-sm-12">
    <div class="box box-info">
      <div class="box-header">
        <h3 class="box-title">Filtros de búsquedad</h3> 
      </div>         
      <div class="box-body form-horizontal">
        <div class="form-group">          
          {{ Form::label("bus-tipo-docente", "Tipo: ", ["class" => "col-sm-2 control-label"]) }}
          <div class="col-sm-3">
            {{ Form::select("tipoDocente", App\Helpers\Enum\TiposEntidad::listarTiposDocente(), NULL, ["id" => "bus-tipo-docente", "class" => "form-control"]) }}
          </div>
          {{ Form::label("bus-sexo-docente", "Sexo: ", ["class" => "col-sm-1 control-label"]) }}
          <div class="col-sm-3">
            {{ Form::select("sexoDocente", $sexos, NULL, ["id" => "bus-sexo-docente", "class" => "form-control", "placeholder" => "Todos"]) }}
          </div>   
        </div> 
        <div class="form-group">          
          {{ Form::label("bus-id-curso-docente", "Curso: ", ["class" => "col-sm-2 control-label"]) }}
          <div class="col-sm-7">
            {{ Form::select("idCursoDocente", $cursos, NULL, ["id" => "bus-id-curso-docente", "class" => "form-control", "placeholder" => "Todos"]) }}
          </div>
        </div> 
        <div class="form-group">  
          {{ Form::label("bus-fecha-inicio", "Rango de fecha: ", ["class" => "col-sm-2 control-label"]) }}
          <div class="col-sm-3">            
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>  
              {{ Form::text("fechaInicio", \Carbon\Carbon::now()->format("d/m/Y H:i:s"), ["id" => "bus-fecha-inicio", "class" => "form-control  pull-right", "placeholder" => "dd/mm/aaaa h:m:s"]) }}
            </div>
          </div>
          <div class="col-sm-3">            
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>  
              {{ Form::text("fechaFin", \Carbon\Carbon::now()->addHours(2)->format("d/m/Y H:i:s"), ["id" => "bus-fecha-fin", "class" => "form-control  pull-right", "placeholder" => "dd/mm/aaaa h:m:s"]) }}
            </div>
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
        <h3 class="box-title">Lista de docentes disponibles</h3> 
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
@endsection
