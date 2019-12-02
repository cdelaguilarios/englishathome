{{----}}
@extends("layouts.master")
@section("titulo", "Buscar docentes disponibles")

@section("section_script")
<script>
  var urlListar = "{{ route('docentes.disponibles.listar') }}";
  
  var estados = {!! json_encode(App\Helpers\Enum\EstadosDocente::listar()) !!};
  var tipoDocenteProfesor = "{{ App\Helpers\Enum\TiposEntidad::Profesor }}";
</script>
<script src="{{ asset("assets/eah/js/modulos/docente/listaDisponibles.js") }}"></script>
@endsection

@section("breadcrumb")
<li class="active">Buscar docentes disponibles</li>
@endsection

@section("content")
<div class="row">
  <div class="col-sm-12">
    <div class="box box-info">
      <div class="box-header">
        <h3 class="box-title">Filtros de búsquedad</h3> 
        <span class="text-blue" data-toggle="tooltip" title="Busca docentes disponibles a partir de la fecha actual utilizando estos filtros e ingresando un horario de búsqueda"><i class="fa fa-question-circle"></i></span>
      </div>         
      <div class="box-body">
        <div class="form-group">  
          {{ Form::label("bus-tipo", "Tipo: ", ["class" => "col-sm-1 control-label"]) }}
          <div class="col-sm-3">
            {{ Form::select("tipo", App\Helpers\Enum\TiposEntidad::listarTiposDocente(), null, ["id" => "bus-tipo", "class" => "form-control"]) }}
          </div>    
          {{ Form::label("bus-estado", "Estado: ", ["class" => "col-sm-1 control-label"]) }}
          <div class="col-sm-3">
            {{ Form::select("estado", App\Helpers\Enum\EstadosDocente::listarBusqueda(), null, ["id"=>"bus-estado", "class" => "form-control", "placeholder" => "Todos", "data-idtabla" => "tab-lista"]) }}
          </div>
          {{ Form::label("bus-sexo", "Sexo: ", ["class" => "col-sm-1 control-label"]) }}
          <div class="col-sm-3">
            {{ Form::select("sexo", App\Helpers\Enum\SexosEntidad::listar(), null, ["id" => "bus-sexo", "placeholder" => "Todos", "class" => "form-control"]) }}
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="form-group">
          {{ Form::label("bus-curso", "Curso: ", ["class" => "col-sm-1 control-label"]) }}
          <div class="col-sm-11">
            {{ Form::select("curso", $cursos, null, ["id" => "bus-curso", "placeholder" => "Todos", "class" => "form-control"]) }}
          </div> 
        </div>
        <div class="clearfix"></div>
        <div class="form-group">    
            @include("util.horario", ["textoBoton" => "Establecer horario de búsqueda", "tituloModal" => "Horario de búsqueda"])  
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
        <table id="tab-lista-docentes-disponibles" class="table table-bordered table-hover">
          <thead>
            <tr>
              <th>Nombre completo</th>     
              <th>Correo electrónico</th>
              <th>Estado</th>
              <th>Fecha registro</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
