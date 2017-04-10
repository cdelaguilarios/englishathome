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
              <th>Código</th> 
              <th>Motivo</th>   
              <th>Fecha de registro</th>
              <th>Estado</th>  
              <th>Monto</th>
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
    <div id="sec-pago-2" style="display: none;">     
      {{ Form::open(["url" => route("profesores.pagos.registrar", ["id" => $idProfesor]), "id" => "formulario-pago", "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }}
      @include("profesor.pago.formulario") 
      {{ Form::close() }}
    </div>    
    <div id="sec-pago-3" style="display: none;">
      {{ Form::open(["url" => route("profesores.pagos.actualizar", ["id" => $idProfesor]), "id" => "formulario-actualizar-pago", "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }}
      @include("profesor.pago.formularioActualizar") 
      {{ Form::close() }}
    </div>
  </div>
</div>
<div style="display: none">
  {{ Form::select("", App\Helpers\Enum\EstadosPago::listarCambio(), null, ["id" => "sel-estados-pago", "class" => "form-control"]) }}
</div>
<script>
  var urlListarPagos = "{{ route('profesores.pagos.listar', ['id' => $idProfesor]) }}";
  var urlActualizarEstadoPago = "{{ route('profesores.pagos.actualizar.estado', ['id' => $idProfesor]) }}";
  var urlDatosPago = "{{ route('profesores.pagos.datos', ['id' => $idProfesor, 'idPago' => 0]) }}";
  var urlEliminarPago = "{{ route('profesores.pagos.eliminar', ['id' => $idProfesor, 'idPago' => 0]) }}";
  var motivosPago = {!!  json_encode(App\Helpers\Enum\MotivosPago::listar()) !!};
</script>
<script src="{{ asset("assets/eah/js/modulos/profesor/pago.js")}}"></script>