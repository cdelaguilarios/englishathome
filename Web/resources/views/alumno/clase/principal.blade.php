<div class="row">
  <div class="col-xs-12">
    @include("partials/errors")
    <div id="sec-mensajes-clase"></div>
    <div id="sec-clase-1">
      <div class="box-header">
        <a id="btn-nuevo-clase" type="button" class="btn btn-primary btn-sm pull-right">Nueva clase</a>   
      </div>         
      <div class="box-body">
        <table id="tab-lista-periodos-clases" class="table table-bordered table-hover">
          <thead>
            <tr> 
              <th class="col-md-2">Período</th>    
              <th>Fecha de inicio</th>
              <th>Fecha de fin</th>
              <th>Total de horas</th>
              <th class="col-md-1">&nbsp;</th> 
            </tr>
          </thead>
        </table>
      </div>
      <div style="display: none">
        {{ Form::select("", $estadosClase, NULL, ["id" => "sel-estados-clase", "class" => "form-control"]) }}
      </div>
    </div> 
    <div id="sec-clase-2">  
      {{ Form::open(["url" => route("alumnos.clases.registrar.actualizar", ["id" => $idAlumno]), "id" => "formulario-registrar-actualizar-clase", "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }}
      @include("alumno.clase.formulario") 
      {{ Form::close() }}
    </div>
    <div id="sec-clase-3">     
      {{ Form::open(["url" => route("alumnos.clases.cancelar", ["id" => $idAlumno]), "id" => "formulario-cancelar-clase", "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }}
      @include("alumno.clase.formularioCancelar") 
      {{ Form::close() }}
    </div> 
  </div>
  @include("alumno.util.docentesDisponibles", ["seccion" => "clase"])
</div>
<script>
  var urlDatosClase = "{{ route("alumnos.clases.datos", ["id" => $idAlumno, "idClase" => 0]) }}";
  var urlListarPeriodos = "{{ route("alumnos.periodosClases.listar", ["id" => $idAlumno]) }}";
  var urlListarClases = "{{ route("alumnos.periodo.clases.listar", ["id" => $idAlumno, "numeroPeriodo" => 0]) }}";
  var urlActualizarEstadoClase = "{{ route("alumnos.clases.actualizar.estado", ["id" => $idAlumno]) }}";
  var urlEliminarClase = "{{ route("alumnos.clases.eliminar", ["id" => $idAlumno, "idClase" => 0]) }}";
  var urlPerfilProfesorClase = "{{ route("profesores.perfil", ["id" => 0]) }}";
  var urlListarDocentesDisponiblesClase = "{{ route("alumnos.clases.docentesDisponibles.listar", ["id" => $idAlumno]) }}";
  var estadosClase = {!!  json_encode(App\Helpers\Enum\EstadosClase::listar()) !!};
  var estadoClaseRealizada = "{{ App\Helpers\Enum\EstadosClase::Realizada }}";
  var estadoClaseCancelada = "{{ App\Helpers\Enum\EstadosClase::Cancelada }}";
  var tipoCancelacionAlumno = "{{ App\Helpers\Enum\TiposCancelacionClase::CancelacionAlumno }}";
  var estadosPagoClase = {!!  json_encode(App\Helpers\Enum\EstadosPago::Listar()) !!};</script>
<script src="{{ asset("assets/eah/js/modulos/alumno/clase.js")}}"></script>