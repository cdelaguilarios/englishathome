<div class="row">
  <div class="col-sm-12">
    <div id="sec-mensajes-clase"></div>
    <div id="sec-clase-1">
      <div class="box-header">  
        <a href="{{ route("alumnos.clases.descargar.lista", $alumno->id)}}" target="_blank" class="btn btn-primary btn-sm pull-right">Descargar lista de clases</a>
        <a id="btn-nuevo-clase" type="button" class="btn btn-primary btn-sm pull-right">Nueva clase</a>
      </div>         
      <div class="box-body">
        <table id="tab-lista-periodos-clases" class="table table-bordered table-hover">
          <thead>
            <tr>   
              <th>Datos</th>
              <th>&nbsp;</th> 
            </tr>
          </thead>
        </table>
      </div>
    </div> 
    <div id="sec-clase-2" style="display: none">
      {{ Form::open(["url" => route("alumnos.clases.registrar.actualizar", ["id" => $idAlumno]), "id" => "formulario-registrar-actualizar-clase", "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }}
      @include("alumno.clase.formulario") 
      {{ Form::close() }}
    </div>
    <div id="sec-clase-3" style="display: none">     
      {{ Form::open(["url" => route("alumnos.clases.cancelar", ["id" => $idAlumno]), "id" => "formulario-cancelar-clase", "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }}
      @include("alumno.clase.formularioCancelar") 
      {{ Form::close() }}
    </div>  
    <div id="sec-clase-4" style="display: none">
      {{ Form::open(["url" => route("alumnos.clases.actualizar.grupo", ["id" => $idAlumno]), "id" => "formulario-actualizar-clases", "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }}
      @include("alumno.clase.formularioGrupo") 
      {{ Form::close() }}
    </div>
  </div>
  @include("alumno.util.docentesDisponibles", ["seccion" => "clase", "idCurso" => $idCurso])
</div>
<div style="display: none">
  {{ Form::select("", App\Helpers\Enum\EstadosClase::listarCambio(), null, ["id" => "sel-estados-clase", "class" => "form-control"]) }}
</div>
<script>
  var idAlumno = "{{ $idAlumno }}";
  var urlListarPeriodos = "{{ route('alumnos.periodos.clases.listar', ['id' => $idAlumno]) }}";
  var urlListarClases = "{{ route('alumnos.periodo.clases.listar', ['id' => $idAlumno, 'numeroPeriodo' => 0]) }}";
  var urlActualizarEstadoClase = "{{ route('alumnos.clases.actualizar.estado', ['id' => $idAlumno]) }}";
  var urlListarDocentesDisponiblesClase = "{{ route('alumnos.clases.docentesDisponibles.listar', ['id' => $idAlumno]) }}";
  var urlDatosClasesGrupo = "{{ route('alumnos.clases.datos.grupo', ['id' => $idAlumno]) }}";
  var urlTotalClasesXHorario = "{{ route('alumnos.clases.total.horario', ['id' => $idAlumno]) }}";
  var urlEliminarClase = "{{ route('alumnos.clases.eliminar', ['id' => $idAlumno, 'idClase' => 0]) }}";
  var estadosClaseCambio = {!! json_encode(App\Helpers\Enum\EstadosClase::listarCambio()) !!};
  var estadoClaseRealizada = "{{ App\Helpers\Enum\EstadosClase::Realizada }}";
  var estadoClaseCancelada = "{{ App\Helpers\Enum\EstadosClase::Cancelada }}";
  var tipoCancelacionClaseAlumno = "{{ App\Helpers\Enum\TiposCancelacionClase::CancelacionAlumno }}";</script>
<script src="{{ asset("assets/eah/js/modulos/alumno/clase.js")}}"></script>