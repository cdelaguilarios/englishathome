<script>
  var urlListarPagosXClases = "{{ route('docentes.pagosXClases.listar') }}";
  var urlListarPagosXClasesDetalle = "{{ route('docentes.pagosXClases.listarDetalle', ['id' => 0]) }}";
  var urlEliminarPagoXClases = "{{ route('docentes.pagosXClases.eliminar', ['id' => 0, 'idPago' => -1]) }}";

  var estados = {!! json_encode(App\Helpers\Enum\EstadosPago::listar()) !!};
  var estadoPagoRealizado = "{{ App\Helpers\Enum\EstadosPago::Realizado }}";
</script>
<script src="{{ asset("assets/eah/js/modulos/docente/pago/lista.js")}}"></script>
<div id="sec-pago-lista" style="display: none">
  <div class="row">
    <div class="col-sm-12">
      <div class="{{ (isset($excluirClaseBox) ? "" : "box box-info") }}">
        <div class="box-header">
          <h3 class="box-title">Filtros de búsqueda</h3> 
        </div>         
        <div class="box-body form-horizontal">
          <div class="form-group">          
            {{ Form::label("bus-estado", "Estado: ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-3">
              {{ Form::select("estadoPago", App\Helpers\Enum\EstadosPago::listarBusqueda(FALSE), App\Helpers\Enum\EstadosPago::Pendiente, ["id"=>"bus-estado", "class" => "form-control", "placeholder" => "Todos", "data-idtabla" => "tab-lista-pagos"]) }}
            </div>
          </div>
          @include("util.filtrosBusquedaFechas", ["idSeccion" => "docentes-pagos-x-clases"])
        </div>
      </div>
    </div>
  </div>          
  <div class="row">
    <div class="col-sm-12">
      <div class="box box-info">
        <div class="box-header">
          <h3 class="box-title">Lista de pagos</h3> 
        </div>         
        <div class="box-body">
          <table id="tab-lista-pagos" class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>N°</th> 
                <th class="all">Profesor(a)</th> 
                <th>Total de clases</th>    
                <th>Duración total (horas)</th>   
                <th>Pago por hora promedio</th>   
                <th class="all">Monto total</th> 
                <th>Datos</th> 
                <th class="all"></th> 
              </tr>
            </thead>
            <tfoot>
              <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>