<div class="box-header">
  <h3 class="box-title with-border">Nuevo pago</h3>                
</div>  
<div class="box-body">
  <div class="form-group">
    <div class="col-sm-12">
      <span>Ingrese los datos solicitados para registrar el pago al profesor por las clases que ha seleccionado.</span></b>
    </div>                                        
  </div> 
  <div class="form-group">
    {{ Form::label("estado", "Estado: ", ["class" => "col-sm-3 control-label"]) }}
    <div class="col-sm-3">
      {{ Form::select("estado", App\Helpers\Enum\EstadosPago::listarCambio(), null, ["class" => "form-control"]) }}
    </div>   
  </div>
  <div class="form-group">
    {{ Form::label("monto-clase-pago", "Monto total (*): ", ["class" => "col-sm-3 control-label"]) }}
    <div class="col-sm-3">
      <div class="input-group">
        <span class="input-group-addon">
          <b>S/.</b>
        </span>
        {{ Form::text("monto", null, ["id" => "monto-clase-pago", "class" => "form-control", "maxlength" =>"19"]) }}
      </div>
    </div>
  </div>             
  <div class="form-group">
    {{ Form::label("imagenDocumentoVerificacion", "Imagen de la ficha de conformidad (*): ", ["class" => "col-sm-3 control-label"]) }}
    <div class="col-sm-4">
      {{ Form::file("imagenDocumentoVerificacion", null) }}
    </div>
  </div> 
  <div class="form-group">
    {{ Form::label("imagenComprobante", "Imagen de comprobante: ", ["class" => "col-sm-3 control-label"]) }}
    <div class="col-sm-4">
      {{ Form::file("imagenComprobante", null) }}
    </div>
  </div>  
  <div class="form-group">
    <div class="col-sm-12">
      <br/><span>(*) Campos obligatorios</span>
    </div>
  </div>   
  {{ Form::hidden("motivo", App\Helpers\Enum\MotivosPago::Clases) }} 
  {{ Form::hidden("datosClases") }} 
</div> 
<div class="box-footer">    
  <button id="btn-cancelar-pago-clase" type="button" class="btn btn-default">Cancelar</button>
  <button type="submit" class="btn btn-success pull-right">Registrar pago</button>
</div> 