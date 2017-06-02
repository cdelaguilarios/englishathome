@extends("layouts.master")
@section("titulo", "Buscar docentes disponibles")

@section("section_script")
<script>
  var urlListar = "{{ route('docentes.disponibles.listar') }}";
  var estados = {!! json_encode(App\Helpers\Enum\EstadosDocente::listar()) !!};</script>
<script src="{{ asset("assets/eah/js/modulos/docente.js") }}"></script>
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
      </div>         
      <div class="box-body">
        <div class="form-group">          
          {{ Form::label("", "Días: ", ["class" => "col-sm-1 control-label"]) }}
          <div class="col-sm-11 sec-filtro-busqueda-dias">
            {{ Form::label("bus-dia-lunes", "Lu.", ["class" => "checkbox-label"]) }}
            {{ Form::checkbox("lunes", null, false, ["id" => "bus-dia-lunes"]) }}
            {{ Form::label("bus-dia-martes", "Ma.", ["class" => "checkbox-label"]) }}
            {{ Form::checkbox("martes", null, false, ["id" => "bus-dia-martes"]) }}
            {{ Form::label("bus-dia-miercoles", "Mi.", ["class" => "checkbox-label"]) }}
            {{ Form::checkbox("miercoles", null, false, ["id" => "bus-dia-miercoles"]) }}
            {{ Form::label("bus-dia-jueves", "Ju.", ["class" => "checkbox-label"]) }}
            {{ Form::checkbox("jueves", null, false, ["id" => "bus-dia-jueves"]) }}
            {{ Form::label("bus-dia-viernes", "Vi.", ["class" => "checkbox-label"]) }}
            {{ Form::checkbox("viernes", null, false, ["id" => "bus-dia-viernes"]) }}
            {{ Form::label("bus-dia-sabado", "Sá.", ["class" => "checkbox-label"]) }}
            {{ Form::checkbox("sabado", null, false, ["id" => "bus-dia-sabado"]) }}
            {{ Form::label("bus-dia-domingo", "Do.", ["class" => "checkbox-label"]) }}
            {{ Form::checkbox("domingo", null, false, ["id" => "bus-dia-domingo"]) }}
          </div>
        </div> 
        <div class="clearfix"></div>
        <div class="form-group">  
          {{ Form::label("bus-tipo", "Tipo: ", ["class" => "col-sm-1 control-label"]) }}
          <div class="col-sm-3">
            {{ Form::select("tipo", App\Helpers\Enum\TiposEntidad::listarTiposDocente(), null, ["id" => "bus-tipo", "class" => "form-control"]) }}
          </div>    
          {{ Form::label("bus-estado", "Estado: ", ["class" => "col-sm-1 control-label"]) }}
          <div class="col-sm-3">
            {{ Form::select("estado", App\Helpers\Enum\EstadosDocente::listarBusqueda(), null, ["id"=>"bus-estado", "class" => "form-control", "placeholder" => "Todos"]) }}
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
