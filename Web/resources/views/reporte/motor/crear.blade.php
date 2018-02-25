@extends("layouts.master")
@section("titulo", "Motor de reportes")

@section("section_script")
<script>
  var tiposSexos = {!! json_encode(App\Helpers\Enum\SexosEntidad::listar()) !!};
  var tiposDocumentos = {!! json_encode(App\Models\TipoDocumento::listarSimple()) !!};
  var alumnos = {!! json_encode(App\Models\Alumno::listarBusqueda()) !!};
  var entidades = {!! json_encode(App\Helpers\Enum\TiposEntidad::listar()) !!};
  var urlListarCampos = "{{ route('reportes.listar.campos') }}";
  var urlListarEntidadesRelacionadas = "{{ route('reportes.listar.entidades.relacionadas') }}";
</script>
<script src="{{ asset("assets/eah/js/modulos/reporte/motor/formulario.js") }}"></script>
@endsection

@section("breadcrumb")
<li><a href="{{ route("reportes") }}">Motor de reportes</a></li>
<li class="active">Nuevo</li>
@endsection

@section("content") 
@include("partials/errors")
{{ Form::open(["url" => route("reportes.registrar"), "id" => "formulario-reporte", "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }}
@include("reporte.motor.formulario", ["modo" => "registrar"])
{{ Form::close() }}
@endsection