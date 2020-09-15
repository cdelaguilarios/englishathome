<script>
  var urlListarPagos = "{{ route('profesores.pagos.listar', ['id' => $profesor->id]) }}";
  var urlEliminarPago = "{{ route('profesores.pagos.eliminar', ['id' => $profesor->id, 'idPago' => 0]) }}";

  var motivosPago = {!!  json_encode(App\Helpers\Enum\MotivosPago::listar()) !!};
  var motivoPagoClases = "{{ App\Helpers\Enum\MotivosPago::Clases }}";
</script>
<script src="{{ asset("assets/eah/js/modulos/profesor/pago/lista.js")}}"></script>
<div id="sec-pago-lista" style="display: none">
  <div class="box-header">
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
    {{ Form::select("", App\Helpers\Enum\EstadosPago::listarDisponibleCambio(), null, ["id" => "sel-estados-pago", "class" => "form-control", "data-urlactualizar" => route('profesores.pagos.generales.actualizar.estado', ['id' => $profesor->id, 'idPago' => 0]), "data-estados" => json_encode(App\Helpers\Enum\EstadosPago::listar())]) }}
  </div>     
</div> 