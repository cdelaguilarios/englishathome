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
              <th>Motivo</th>     
              <th>Monto</th>
              <th>Fecha de registro</th>
              <th>Estado</th>
              <th class="col-sm-1">&nbsp;</th>
            </tr>
          </thead>
        </table>
      </div>
      <div style="display: none">
        {{ Form::select("", $estadosPagoSimple, NULL, ["id" => "sel-estados-pago", "class" => "form-control"]) }}
      </div>
    </div>        
    @include("alumno.pago.formulario") 
  </div>
</div>
@include("alumno.util.docentesDisponibles", ["seccion" => "pago"])
@include("alumno.pago.datos") 
<script>
  var urlListarPagos = "{{ route("alumnos.pagos.listar", ["id" => $idAlumno]) }}";
  var urlActualizarEstadoPago = "{{ route("alumnos.pagos.actualizar.estado", ["id" => $idAlumno]) }}";
  var urlDatosPago = "{{ route("alumnos.pagos.datos", ["id" => $idAlumno, "idPago" => 0]) }}";
  var urlEliminarPago = "{{ route("alumnos.pagos.eliminar", ["id" => $idAlumno, "idPago" => 0]) }}";  
  var urlGenerarClasesPago = "{{ route("alumnos.pagos.generarClases", ["id" => $idAlumno]) }}";
  var urlListarDocentesDisponiblesPago = "{{ route("alumnos.pagos.docentesDisponibles.listar", ["id" => $idAlumno]) }}"; 
  var saldoFavorTotal = {{ ($totalSaldoFavor != "" ? $totalSaldoFavor : 0) }};
</script>
<script src="{{ asset("assets/eah/js/modulos/alumno/pago.js")}}"></script>