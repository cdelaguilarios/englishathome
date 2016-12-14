<div class="row">
  <div class="col-xs-12">
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
    </div> 
    <div id="sec-clase-2">  
      {{ Form::open(['url' => route("alumnos.clases.registrar", ["id" => $idAlumno]), "id" => "formulario-registrar-clase", "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }}
      @include('partials/errors')
      @include("alumno.clase.formulario") 
      {{ Form::close() }}
    </div>    
    <div id="sec-clase-3">  
      {{ Form::model($alumno, ["method" => "PATCH", "action" => ["AlumnoController@actualizarClase", $alumno->id], "id" => "formulario-actualizar-clase", "class" => "form-horizontal", "files" => true]) }}      
      @include('partials/errors')
      @include("alumno.clase.formulario") 
      {{ Form::close() }}
    </div>
    <div id="sec-clase-4">     
      {{ Form::open(['url' => route("alumnos.clases.cancelar", ["id" => $idAlumno]), "id" => "formulario-cancelar-clase", "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }}
      @include('partials/errors')
      @include("alumno.clase.formularioCancelar") 
      {{ Form::close() }}
    </div> 
  </div>
  @include('alumno.util.docentesDisponibles', ['seccion' => 'clase'])
</div>
<script>
  var urlListarPeriodos = "{{ route('alumnos.periodosClases.listar', ['id' => $idAlumno]) }}";
  var urlListarClases = "{{ route('alumnos.periodo.clases.listar', ['id' => $idAlumno, 'numeroPeriodo' => 0]) }}";
  var urlEliminarClase = "{{ route('alumnos.clases.eliminar', ['id' => $idAlumno, 'idClase' => 0]) }}";
  var urlPerfilProfesorClase = "{{ route('profesores.perfil', ['id' => 0]) }}";
  var urlListarDocentesDisponiblesClase = "{{ route('alumnos.clases.docentesDisponibles.listar', ['id' => $idAlumno]) }}";
  var estadosClase = {!!  json_encode(App\Helpers\Enum\EstadosClase::Listar()) !!};
  var estadoClaseRealizada = "{{ App\Helpers\Enum\EstadosClase::Realizada }}";
  var tipoCancelacionAlumno = "{{ App\Helpers\Enum\TiposCancelacionClase::CancelacionAlumno }}";
</script>
<script src='{{ asset('assets/eah/js/modulos/alumno/clase.js')}}'></script>