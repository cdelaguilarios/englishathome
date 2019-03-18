@extends("layouts.masterAlumnosProfesores")
@section("titulo", "Clases")

@section("section_script")
<script type="text/javascript">
  var urlListar = "{{ route('clases.propias.listar') }}";    
  var estados = {!!  json_encode(App\Helpers\Enum\EstadosClase::listar()) !!};
</script>
<script src="{{ asset("assets/eah/js/clasesPropias.js")}}"></script>
@endsection

@section("breadcrumb")
<li class="active">Clases</li>
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
            {{ Form::select("estado", App\Helpers\Enum\EstadosClase::listarBusqueda(), null, ["id"=>"bus-estado", "class" => "form-control", "placeholder" => "Todos"]) }}
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
        <h3 class="box-title">Lista de clases</h3>
      </div>         
      <div class="box-body">
        <table id="tab-lista" class="table table-bordered table-hover">
          <thead>
            <tr>
              <th>N°</th>    
              <th>Datos</th>
              <th>Estado</th>
              <th>Comentarios</th>
              <th>Comentarios de English At Home</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
