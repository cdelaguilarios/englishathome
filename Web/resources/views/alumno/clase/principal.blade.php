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
      @include("alumno.clase.formulario") 
    </div>
    <div id="sec-clase-3">     
      @include("alumno.clase.formularioCancelar") 
    </div> 
  </div>
  @include('alumno.util.docentesDisponibles', ['seccion' => 'clase'])
  <script>
      var urlListarPeriodos = "{{ route('alumnos.periodosClases.listar', ['id' => $idAlumno]) }}";
      var urlListarClases = "{{ route('alumnos.periodo.clases.listar', ['id' => $idAlumno, 'numeroPeriodo' => 0]) }}";
      var urlEliminarClase = "{{ route('alumnos.clases.eliminar', ['id' => $idAlumno, 'idClase' => 0]) }}";
      var urlPerfilProfesorClase = "{{ route('profesores.perfil', ['id' => 0]) }}";
      var urlListarDocentesDisponiblesClase = "{{ route('alumnos.clases.docentesDisponibles.listar', ['id' => $idAlumno]) }}";
      var estadosClase = {!!  json_encode(App\Helpers\Enum\EstadosClase::Listar()) !!};
      var estadosClaseProgramada = "{{ App\Helpers\Enum\EstadosClase::Programada }}";
      var tipoCancelacionAlumno = "{{ App\Helpers\Enum\TiposCancelacionClase::CancelacionAlumno }}";  
    </script>
  <script src='{{ asset('assets/eah/js/modulos/alumno/clase.js')}}'></script>