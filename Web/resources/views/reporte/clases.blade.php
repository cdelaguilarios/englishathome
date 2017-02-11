@extends("layouts.master")
@section("titulo", "Reporte - Clases")

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
  var urlListar = "{{ route('reporte.clases.listar') }}";
</script>
<script src="{{ asset("assets/eah/js/modulos/reporte/base.js")}}"></script>
<script src="{{ asset("assets/eah/js/modulos/reporte/clase.js")}}"></script>
@endsection

@section("breadcrumb")
<li class="active">Reporte - Clases</li>
@endsection

@section("content")
@include("util.filtroBusqueda", ["seccionReporte" => 1, "estados" => App\Helpers\Enum\EstadosClase::listarSimple()]) 
<div class="row">
  <div class="col-sm-12">
    <div id="sec-grafico" class="box box-info">
      <div id="grafico"></div>
    </div>
  </div>
</div>
@endsection
