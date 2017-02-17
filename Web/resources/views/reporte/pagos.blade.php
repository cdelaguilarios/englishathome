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
  var urlListar = "{{ route('reporte.pagos.listar') }}";
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
    <div id="sec-grafico" class="box box-info">
      <div id="grafico"></div>
    </div>
  </div>
</div>
@endsection
