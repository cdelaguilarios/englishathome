@extends("layouts.masterAlumnoProfesor")
@section("titulo", "Mis alumnos - Clases")

@section("section_script")
<script type="text/javascript">
  var urlListar = "{{ route('profesores.mis.alumnos.listar.clases', ['id' => $alumno->id]) }}";
  var estadoClaseProgramada = "{{ App\Helpers\Enum\EstadosClase::Programada }}";
  var estadoClasePendienteConfirmar = "{{ App\Helpers\Enum\EstadosClase::PendienteConfirmar }}";
  var estadoClaseConfirmadaProfesorAlumno = "{{ App\Helpers\Enum\EstadosClase::ConfirmadaProfesorAlumno }}";
  var estadoClaseRealizada = "{{ App\Helpers\Enum\EstadosClase::Realizada }}";
  var duracionProximaClase = "{{ (!is_null($proximaClase) ? $proximaClase->duracion : 0) }}";
  var totalDuracionClasesRestantes = parseInt("{{ $alumno->duracionTotalClases - $alumno->duracionTotalClasesRealizadas }}") / 3600;

  maxHorasClase = (typeof (maxHorasClase) === "undefined" ? 0 : maxHorasClase);
  maxHorasClase = (totalDuracionClasesRestantes > maxHorasClase ? maxHorasClase : totalDuracionClasesRestantes);
</script>
<script src="{{ asset("assets/eah/js/modulos/profesor/misAlumnosClases.js")}}"></script>
@endsection

@section("content")
@include("partials/errors")
@if(!is_null($proximaClase))
<div class="row">
  <div class="col-sm-12">
    <div id="mod-confirmar-clase" class="modal" data-keyboard="false" style="text-align: initial">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">Confirmación de clase {{ $alumno->sexo == "F" ? "de la alumna" : "del alumno" }} {{ $alumno->nombre . " " .  $alumno->apellido }}</h4>
          </div>
          {{ Form::open(["url" => route("profesores.mis.alumnos.confirmar.clase", ['id' => $alumno->id]), "id" => "formulario-confirmar-clase", "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }}
          <div class="modal-body">
            <div class="row">
              <div class="col-sm-12">
                <div class="box-body">
                  <div class="form-group">
                    {{ Form::label("duracion", "Duracion: ", ["class" => "col-sm-4 control-label"]) }}
                    {{ Form::label("", App\Helpers\Util::formatoHora($proximaClase->duracion), ["id" => "sec-duracion", "class" => "col-sm-1 control-label"]) }}
                    <div id="sec-cambio-duracion" class="col-sm-3" style="display: none">
                      <div class="input-group date">
                        <div class="input-group-addon">
                          <i class="fa  fa-clock-o"></i>
                        </div>    
                        {{ Form::select("duracion", [], null, ["id" => "duracion-clase", "class" => "form-control"]) }}
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    {{ Form::label("codigoVerificacion", "Código del alumno (*): ", ["class" => "col-sm-4 control-label"]) }}
                    <div class="col-sm-4">
                      {{ Form::password("codigoVerificacion", ["class" => "form-control", "minlength" =>"4", "maxlength" =>"6"]) }}
                    </div>
                  </div>
                  <div class="form-group">
                    {{ Form::label("comentario", "Avances de la clase: ", ["class" => "col-sm-4 control-label"]) }}
                    <div class="col-sm-8">
                      {{ Form::textarea("comentario", null, ["class" => "form-control", "rows" => "6", "maxlength" =>"8000"]) }}
                    </div>  
                  </div>
                </div>   
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button id="btn-cambiar-duracion" type="button" class="btn btn-warning btn-next pull-left">Cambiar duración de la clase</button>            
            <button type="submit" class="btn btn-success">Confirmar</button>
          </div>
          {{ Form::close() }}
        </div>
      </div>
    </div>
  </div>
</div>
@endif
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
              <span class="pull-left">Total de clases: {{ $alumno->totalClases }}</span>
              <small class="pull-right">{{ number_format($alumno->porcentajeAvance, 2, ".", "") }} %</small>
            </div>
            <div class="progress xs">
              <div class="progress-bar progress-bar-green" style="width: {{ $alumno->porcentajeAvance }}%;"></div>              
            </div>
            <div class="clearfix">
              <span class="pull-left">
                <span class="text-green" data-toggle="tooltip" title="" data-original-title="Horas realizadas"><i class="fa fa-clock-o"></i> {{ App\Helpers\Util::formatoHora($alumno->duracionTotalClasesRealizadas) }}</span> horas realizadas de un total de <span class="text-info" data-toggle="tooltip" title="" data-original-title="Horas programadas"><i class="fa fa-clock-o"></i> {{ App\Helpers\Util::formatoHora($alumno->duracionTotalClases) }}</span>
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
<div id="mod-avances-clase" class="modal" data-keyboard="false" style="text-align: initial">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Registrar avances</h4>
      </div>
      {{ Form::open(["url" => route("profesores.mis.alumnos.registrar.avance.clase", ['id' => $alumno->id]), "id" => "formulario-avances-clase", "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }}
      <div class="modal-body">
        <div class="row">
          <div class="col-sm-12">
            <div class="box-body">
              <div class="form-group">
                <div class="col-sm-12">
                  {{ Form::textarea("comentario", null, ["class" => "form-control", "rows" => "10", "maxlength" =>"8000"]) }}
                </div>
              </div>
              {{ Form::hidden("idClase") }}
            </div>   
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success btn-sm">Guardar</button>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>
@endsection
