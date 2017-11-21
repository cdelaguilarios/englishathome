@extends("layouts.master")
@section("titulo", "Motor de reportes")

@section("section_script")
<script>
  var tiposSexos = {!! json_encode(App\Helpers\Enum\SexosEntidad::listar()) !!};
  var tiposDocumentos = {!! json_encode(App\Models\TipoDocumento::listarSimple()) !!};
  var alumnos = {!! json_encode(App\Models\Alumno::listarBusqueda()) !!};
  var entidades = {!! json_encode($entidades) !!};
  var urlListarCampos = "{{ route('reportes.listar.campos') }}";
  var urlListarEntidadesRelacionadas = "{{ route('reportes.listar.entidades.relacionadas') }}";
  var urlEditar = "{{ route('reportes.editar', ['id' => 0]) }}";
</script>
<script src="{{ asset("assets/eah/js/modulos/reporte/motor/formulario.js") }}"></script>
@endsection

@section("breadcrumb")
<li><a href="{{ route("reportes") }}">Motor de reportes</a></li>
<li class="active">Editar</li>
@endsection

@section("content") 
@include("partials/errors")
<div class="row">
  <div class="col-sm-12">
    <div class="box box-primary">        
      <div class="box-body">
        <div class="form-group">
          <div class="col-sm-8">
            <a href="{{ route("reportes.crear")}}" class="btn btn-primary btn-clean">Nuevo reporte</a>
          </div>  
        </div> 
      </div>
    </div>
  </div>
</div>
{{ Form::model($reporte, ["method" => "PATCH", "action" => ["ReporteController@actualizar", $reporte->id], "id" => "formulario-reporte", "class" => "form-horizontal", "files" => true]) }}
@include("reporte.motor.formulario")
{{ Form::close() }}
@endsection