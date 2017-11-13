@extends("layouts.master")
@section("titulo", "Motor de reportes")

@section("section_style")
<style>
  .btn-entidad{
    width: 250px;
    height: 50px;
    margin: 10px;    
    border: 0px;
    border-radius: 5px;
    font-size: 18px;
  }
  .btn-entidad:hover, .btn-activo{
    background-color: #3c8dbc;
    color: #fff;
  }
  #sec-campos-entidad-relacionada{
    margin-top: 80px;
  }
  .fuelux .checkbox-custom:before {
    right: -25px;
  }
</style>
@endsection

@section("section_script")
<script>
  var tiposSexos = {!! json_encode(App\Helpers\Enum\SexosEntidad::listar()) !!};
  var tiposDocumentos = {!! json_encode(App\Models\TipoDocumento::listarSimple()) !!};
  var alumnos = {!! json_encode(App\Models\Alumno::listarBusqueda()) !!};
  var urlListarCampos = "{{ route('reporte.motor.litar.campos') }}";
  var urlListarEntidadesRelacionadas = "{{ route('reporte.motor.litar.entidades.relacionadas') }}";
</script>
<script src="{{ asset("assets/eah/js/modulos/reporte/motor.js") }}"></script>
@endsection

@section("breadcrumb")
<li><a href="{{ route("reporte.motor") }}">Motor de reportes</a></li>
<li class="active">Nuevo</li>
@endsection

@section("content") 
@include("partials/errors")
{{ Form::open(["url" => route("reporte.motor.registrar"), "id" => "formulario-reporte-motor", "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }}
@include("reporte.motor.formulario", ["modo" => "registrar"])
{{ Form::close() }}
@endsection