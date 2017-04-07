<div class="box-header">
  <h3 class="box-title with-border">Editar pago</h3>                
</div>  
<div class="box-body">
  <div class="form-group">
    {{ Form::label("motivo-actualizar-pago", "Motivo: ", ["class" => "col-sm-2 control-label"]) }}
    <div class="col-sm-3">
      {{ Form::select("motivo", App\Helpers\Enum\MotivosPago::listar(), null, ["id" => "motivo-actualizar-pago", "class" => "form-control"]) }}
    </div>                  
  </div> 
  <div class="form-group">
    {{ Form::label("cuenta-actualizar-pago", "Cuenta: ", ["class" => "col-sm-2 control-label"]) }}
    <div class="col-sm-3">
      {{ Form::select("cuenta", App\Helpers\Enum\CuentasBancoPago::listar(), null, ["id" => "cuenta-actualizar-pago", "class" => "form-control"]) }}
    </div>   
  </div>
  <div class="form-group">
    {{ Form::label("estado-actualizar-pago", "Estado: ", ["class" => "col-sm-2 control-label"]) }}
    <div class="col-sm-3">
      {{ Form::select("estado", App\Helpers\Enum\EstadosPago::listarCambio(), null, ["id" => "estado-actualizar-pago", "class" => "form-control"]) }}
    </div>   
  </div>
  <div class="form-group">
    {{ Form::label("descripcion-actualizar-pago", "Descripción: ", ["class" => "col-sm-2 control-label"]) }}
    <div class="col-sm-10">
      {{ Form::text("descripcion", null, ["id" => "descripcion-actualizar-pago", "class" => "form-control", "maxlength" =>"255"]) }}
    </div>
  </div>
  <div class="form-group">
    {{ Form::label("imagenComprobante", "Imagen de comprobante: ", ["class" => "col-sm-2 control-label"]) }}
    <div class="col-sm-5">
      {{ Form::file("imagenComprobante", null) }}
    </div>
    <div class="col-sm-3">
      <a id="imagen-comprobante-actualizar-pago" href="{{ route("archivos", ["nombre" => "0"]) }}" target="_blank">
        <img src="{{ route("archivos", ["nombre" => "0"]) }}" width="40"/>
      </a>
    </div>
  </div> 
  <div class="form-group">
    {{ Form::label("monto-actualizar-pago", "Monto total (*): ", ["class" => "col-sm-2 control-label"]) }}
    <div class="col-sm-3">
      <div class="input-group">
        <span class="input-group-addon">
          <b>S/.</b>
        </span>
        {{ Form::text("monto", null, ["id" => "monto-actualizar-pago", "class" => "monto-pago form-control", "maxlength" =>"19", "data-modo" =>"actualizar"]) }}
      </div>
    </div>
    @if($totalSaldoFavor > 0)
    <div class="col-sm-7">
      <div class="checkbox">
        <label class="checkbox-custom" data-initialize="checkbox">
          {{ Form::label("usarSaldoFavor", "Utilizar saldo a favor total (S/. " . number_format($totalSaldoFavor, 2, ".", ",") . ")", ["class" => "checkbox-label"]) }}
          {{ Form::checkbox("usarSaldoFavor", null, FALSE, ["class" => "usar-saldo-favor", "data-modo" =>"actualizar"]) }}
        </label>
      </div>
    </div> 
    @endIf
  </div>             
  <div class="form-group">
    <div class="col-sm-12">
      <br/><span>(*) Campos obligatorios</span>
    </div>
  </div>  
  {{ Form::hidden("idPago") }} 
</div> 
<div class="box-footer">    
  <button type="button" class="btn-cancelar-pago btn btn-default">Cancelar</button>
  <button type="submit" class="btn btn-success pull-right">Guardar</button>
</div> 