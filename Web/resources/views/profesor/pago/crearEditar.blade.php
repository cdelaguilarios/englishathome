<script>
  var urlDatosPago = "{{ route('profesores.pagos.datos', ['id' => $profesor->id, 'idPago' => 0]) }}";  
</script>
<script src="{{ asset("assets/eah/js/modulos/profesor/pago/crearEditar.js")}}"></script>   
<div id="sec-pago-crear-editar" style="display: none">
  <div class="box-header">
    <h3 class="box-title with-border"></h3>                
  </div> 
  {{ Form::open(["url" => route("profesores.pagos.generales.registrar.actualizar", ["id" => $profesor->id]), "id" => "formulario-pago", "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }} 
  @include("profesor.pago.formulario")
  <div class="box-footer">    
    <button id="btn-cancelar-pago" type="button" class="btn btn-default">Cancelar</button>
    <button id="btn-registrar-pago" type="submit" class="btn btn-success pull-right">Guardar cambios</button>
  </div> 
  {{ Form::close() }}
</div>