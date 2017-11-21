@extends("layouts.master")
@section("titulo", "Reporte de pagos")

@section("section_style")
<style>
  #sec-grafico{
    padding: 10px;
  }
</style>
@endsection

@section("section_script")
<script>
  var estados = {!!  json_encode(App\Helpers\Enum\EstadosPago::listar()) !!};
  var motivosPago = {!!  json_encode(App\Helpers\Enum\MotivosPago::listar()) !!};
  var cuentasBanco = {!! json_encode(App\Helpers\Enum\CuentasBancoPago::listar()) !!};
  var urlListar = "{{ route('reporte.listar.pagos') }}";
  var urlListarGrafico = "{{ route('reporte.listar.pagos.grafico') }}";
</script>
<script src="{{ asset("assets/eah/js/modulos/reporte/base.js")}}"></script>
<script src="{{ asset("assets/eah/js/modulos/reporte/pago.js")}}"></script>
@endsection

@section("breadcrumb")
<li class="active">Reporte de pagos</li>
@endsection

@section("content")
@include("util.filtroBusqueda", ["incluirEstadosPago" => 1, "incluirTipoPago" => 1, "incluirClaseBox" => 1]) 
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
                    <th>Código</th> 
                    <th>Alumno/Profesor</th>  
                    <th>Motivo</th>    
                    <th>Cuenta</th>
                    <th>Fecha de pago</th>
                    <th>Estado</th>
                    <th>Monto</th>
                  </tr>
                </thead>
                <tfoot>
                  <tr>
                    <th colspan="6"></th>
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
