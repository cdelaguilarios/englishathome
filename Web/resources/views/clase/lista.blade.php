@extends("layouts.master")
@section("titulo", "Clases")

@section("section_script")
<script>
  var estados = {!!  json_encode(App\Helpers\Enum\EstadosClase::listar()) !!};
  var urlListar = "{{ route('clases.listar') }}";
</script>
<script src="{{ asset("assets/eah/js/modulos/clase.js")}}"></script>
@endsection

@section("breadcrumb")
<li class="active">Clases</li>
@endsection

@section("content")
@include("util.filtroBusqueda", ["incluirEstadosClase" => 1, "incluirClaseBox" => 1])
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
              <th>Período</th> 
              <th>Alumno</th>  
              <th>Profesor</th>    
              <th>Fecha</th>
              <th>Duración</th>
              <th>Pago por hora al profesor</th>
              <th>Estado</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
