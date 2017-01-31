<div class="box-header">
  <h3 class="box-title with-border">Nuevo pago</h3>                
</div>  
<div class="box-body">
  <div class="form-group">
    {{ Form::label("descripcion", "Descripción: ", ["class" => "col-sm-2 control-label"]) }}
    <div class="col-sm-10">
      {{ Form::text("descripcion", NULL, ["class" => "form-control", "maxlength" =>"255"]) }}
    </div>
  </div>
  <div class="form-group">
    {{ Form::label("imagenComprobante", "Imagen de comprobante: ", ["class" => "col-sm-2 control-label"]) }}
    <div class="col-sm-4">
      {{ Form::file("imagenComprobante", NULL) }}
    </div>
  </div> 
  <div class="form-group">
    {{ Form::label("monto-pago", "Monto total (*): ", ["class" => "col-sm-2 control-label"]) }}
    <div class="col-sm-3">
      <div class="input-group">
        <span class="input-group-addon">
          <b>S/.</b>
        </span>
        {{ Form::text("monto", NULL, ["id" => "monto-pago", "class" => "form-control", "maxlength" =>"19"]) }}
      </div>
    </div>
  </div>        
  <div class="form-group">
    <div class="col-sm-12">
      <br/><span>(*) Campos obligatorios</span>
    </div>
  </div>         
  {{ Form::hidden("motivo", App\Helpers\Enum\MotivosPago::Otros) }} 
</div> 
<div class="box-footer">    
  <button id="btn-cancelar-pago" type="button" class="btn btn-default">Cancelar</button>
  <button id="btn-registrar-pago" type="submit" class="btn btn-success pull-right">Registrar pago</button>
</div> 