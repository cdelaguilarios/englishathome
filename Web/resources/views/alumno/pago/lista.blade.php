{{----}}
<script>
  var urlListarPagos = "{{ route('alumnos.pagos.listar', ['id' => $alumno->id]) }}";
          var urlEliminarPago = "{{ route('alumnos.pagos.eliminar', ['id' => $alumno->id, 'idPago' => 0]) }}";
          var estadosPagoDisponibleCambio = {!! json_encode(App\Helpers\Enum\EstadosPago::listarDisponibleCambio()) !!};
          var estadoPagoCosumido = "{{ App\Helpers\Enum\EstadosPago::Consumido }}";
          var motivosPago = {!!  json_encode(App\Helpers\Enum\MotivosPago::listar()) !!};
          var motivoPagoClases = "{{ App\Helpers\Enum\MotivosPago::Clases }}";
          var cuentasBanco = {!! json_encode(App\Helpers\Enum\CuentasBancoPago::listar()) !!};</script>
<script src="{{ asset("assets/eah/js/modulos/alumno/pago/lista.js")}}"></script>
<div id="sec-pago-lista" style="display: none">
  <div class="box-header"> 
      <span class="pull-left">
        <b>Nota: </b> los pagos con estado <span class="label label-primary btn-estado">Consumido</span> no serán considerados en la bolsa de horas de alumno(a).
      </span>
    <a id="btn-nuevo-pago" type="button" class="btn btn-primary btn-sm pull-right">Nuevo pago</a>  
  </div>     
  <div class="box-body">   
    <table id="tab-lista-pagos" class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>Datos generales</th>                
          <th>Fecha de pago</th>
          <th>Estado</th> 
          <th>Montos</th>
          <th class="all">Opciones</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <th colspan="3"></th>
          <th></th>
          <th></th>
        </tr>
      </tfoot>
    </table> 
  </div>
  <div style="display: none">
    {{ Form::select("", App\Helpers\Enum\EstadosPago::listarDisponibleCambio(), null, ["id" => "sel-estados-pago", "class" => "form-control", "data-urlactualizar" => route('alumnos.pagos.actualizar.estado', ['id' => $alumno->id, 'idPago' => 0]), "data-estados" => json_encode(App\Helpers\Enum\EstadosPago::listar())]) }}
  </div>     
</div> 