@extends("externo.layouts.master")
@section("titulo", "Mis alumnos - Clases")

@section("section_script")
<script type="text/javascript">
  var urlListar = "{{ route('profesores.mis.alumnos.listar.clases', ['id' => $alumno->id]) }}";
  
  var estadoClaseConfirmadaProfesorAlumno = "{{ App\Helpers\Enum\EstadosClase::ConfirmadaProfesorAlumno }}";
  var estadoClaseRealizada = "{{ App\Helpers\Enum\EstadosClase::Realizada }}";
  
  var duracionTotalXClasesPendientes = parseInt("{{ $alumno->duracionTotalXClasesPendientes }}") / 3600;
</script>
<script src="{{ asset("assets/eah/js/modulosExternos/profesor/misAlumnosListaClases.js")}}"></script>
@endsection

@section("content")
@include("partials/errors")
@if($alumno->duracionTotalXClasesPendientes > 0)
@include("externo.profesor.util.formularioConfirmarClase")
<div class="row">
  <div class="col-sm-12">
    <div class="box box-info">
      <div class="box-header">
        <h3 class="box-title">{{ $alumno->sexo == "F" ? "Alumna" : "Alumno" }} {{ $alumno->nombre . " " .  $alumno->apellido }}</h3> 
      </div>         
      <div class="box-body">
        <div class="form-group">          
          <div class="col-sm-6">
            <b>Horario</b> @include("util.horario", ["horario" => $alumno->horario, "modo" => "visualizar", "vistaImpresion" => true])
          </div>
          <div class="clear"></div>
          <div class="col-sm-6">
            <div class="clearfix">
              <span class="pull-left">Bolsa de {{ App\Helpers\Util::formatoHora($alumno->duracionTotalXClases) }} hora(s)</span>
              <small class="pull-right">{{ number_format($alumno->porcentajeAvance, 2, ".", "") }} %</small>
            </div>
            <div class="progress xs">
              <div class="progress-bar progress-bar-green" style="width: {{ $alumno->porcentajeAvance }}%;"></div>              
            </div>
            <div class="clearfix">
              <span class="pull-left">
                <span class="text-green" data-toggle="tooltip" title="" data-original-title="Horas realizadas"><i class="fa fa-clock-o"></i> {{ App\Helpers\Util::formatoHora($alumno->duracionTotalXClasesRealizadas) }}</span> hora(s) realizadas de un total de <span class="text-info" data-toggle="tooltip" title="" data-original-title="Horas programadas"><i class="fa fa-clock-o"></i> {{ App\Helpers\Util::formatoHora($alumno->duracionTotalXClases) }}</span>
              </span>
            </div>
          </div>        
        </div>
      </div>
      <div class="sec-btn-confirmar-clase">
        <button type="button" class="btn btn-success btn-lg" data-toggle="modal" data-target="#mod-confirmar-clase">
          Confirmar clase
        </button>
      </div>
    </div>
  </div>
</div> 
@endif
<div class="row">
  <div class="col-sm-12">
    <div class="box box-info">
      <div class="box-header">
        @if($alumno->duracionTotalXClasesPendientes > 0)
        <h3 class="box-title">Lista de clases</h3>
        @else
        <h3 class="box-title">Lista de clases {{ $alumno->sexo == "F" ? "de la alumna" : "del alumno" }} {{ $alumno->nombre . " " .  $alumno->apellido }}</h3>
        @endif
      </div>         
      <div class="box-body">
        <table id="tab-lista-clases" class="table table-bordered table-hover">
          <thead>
            <tr>
              <th>N°</th>    
              <th class="all">Datos</th>
              <th>Avances</th>
              <th>Comentarios de English At Home</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>
@include("externo.profesor.util.formularioAvancesClase") 
@endsection
