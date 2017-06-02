<div class="row">
  <div class="col-sm-12">
    <div id="sec-mensajes-pago"></div>
    <div id="sec-pago-1">
      <div class="box-header">
        <a id="btn-nuevo-pago" type="button" class="btn btn-primary btn-sm pull-right">Nuevo pago</a>  
      </div>         
      <div class="box-body">
        <table id="tab-lista-pagos" class="table table-bordered table-hover">
          <thead>
            <tr>
              <th>Datos generales</th>                
              <th>Fecha de pago</th>
              <th>Fecha de registro</th>
              <th>Estado</th> 
              <th>Montos</th>
              <th class="all">Opciones</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th colspan="4"></th>
              <th></th>
              <th></th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>      
    <div id="sec-pago-2" style="display: none">
      {{ Form::open(["url" => route("alumnos.pagos.registrar", ["id" => $idAlumno]), "id" => "formulario-pago", "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }}
      @include("alumno.pago.formulario") 
      {{ Form::close() }}
    </div>     
    <div id="sec-pago-3" style="display: none">
      {{ Form::open(["url" => route("alumnos.pagos.actualizar", ["id" => $idAlumno]), "id" => "formulario-actualizar-pago", "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }}
      @include("alumno.pago.formularioActualizar") 
      {{ Form::close() }}
    </div>
  </div>
</div>
@include("alumno.util.docentesDisponibles", ["seccion" => "pago", "idCurso" => $idCurso])
<div style="display: none">
  {{ Form::select("", App\Helpers\Enum\EstadosPago::listarCambio(), null, ["id" => "sel-estados-pago", "class" => "form-control"]) }}
</div>
<script>
  var urlListarPagos = "{{ route('alumnos.pagos.listar', ['id' => $idAlumno]) }}";
  var urlActualizarEstadoPago = "{{ route('alumnos.pagos.actualizar.estado', ['id' => $idAlumno]) }}";
  var urlGenerarClasesPago = "{{ route('alumnos.pagos.generarClases', ['id' => $idAlumno]) }}";
  var urlListarDocentesDisponiblesPago = "{{ route('alumnos.pagos.docentesDisponibles.listar', ['id' => $idAlumno]) }}";
  var saldoFavorTotal = {{ ($totalSaldoFavor != "" ? $totalSaldoFavor : 0) }};
  var urlDatosPago = "{{ route('alumnos.pagos.datos', ['id' => $idAlumno, 'idPago' => 0]) }}";
  var urlEliminarPago = "{{ route('alumnos.pagos.eliminar', ['id' => $idAlumno, 'idPago' => 0]) }}";
  var motivosPago = {!!  json_encode(App\Helpers\Enum\MotivosPago::listar()) !!};
  var cuentasBanco = {!! json_encode(App\Helpers\Enum\CuentasBancoPago::listar()) !!};
</script>
<script src="{{ asset("assets/eah/js/modulos/alumno/pago.js")}}"></script>