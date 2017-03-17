@extends("layouts.master")
@section("titulo", "Reporte de clases")

@section("section_style")
<style>
  #sec-grafico{
    padding: 10px;
  }
</style>
@endsection

@section("section_script")
<script>
  var estados = {!!  json_encode(App\Helpers\Enum\EstadosClase::listar()) !!};
  var urlListar = "{{ route('reporte.listar.clases') }}";
  var urlListarGrafico = "{{ route('reporte.listar.clases.grafico') }}";
</script>
<script src="{{ asset("assets/eah/js/modulos/reporte/base.js")}}"></script>
<script src="{{ asset("assets/eah/js/modulos/reporte/clase.js")}}"></script>
@endsection

@section("breadcrumb")
<li class="active">Reporte de clases</li>
@endsection

@section("content")
@include("util.filtroBusqueda", ["incluirEstadosClase" => 1, "incluirClaseBox" => 1]) 
<div class="row">
  <div class="col-sm-12">
    <div class="nav-tabs-custom">
      <ul class="nav nav-tabs">
        <li class="active"><a href="#sec-lista" data-toggle="tab">Lista</a></li>
        <li><a href="#sec-grafico" data-toggle="tab">Gráfico</a></li>
      </ul>
      <div class="tab-content">
        <div id="sec-lista" class="active tab-pane">
          <div class="row">
            <div class="col-sm-12">
              <table id="tab-lista" class="table table-bordered table-hover">
                <thead>
                  <tr>
                    <th>Período</th> 
                    <th>Profesor(a)</th>  
                    <th>Alumno(a)</th>    
                    <th>Fecha</th>
                    <th>Duración</th>
                    <th>Pago por hora al profesor</th>
                    <th>Estado</th>
                  </tr>
                </thead>
                <tfoot>
                  <tr>
                    <th colspan="4"></th>
                    <th colspan="2" class="text-center"></th>
                    <th></th>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>
        <div id="sec-grafico" class="tab-pane">
          <div class="row">
            <div class="col-sm-12">
              <div id="grafico"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
