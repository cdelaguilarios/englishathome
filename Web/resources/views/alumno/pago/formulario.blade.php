{{----}}
<script>
  var motivoPagoXClases = "{{ App\Helpers\Enum\MotivosPago::Clases }}";
</script>
<script src="{{ asset("assets/eah/js/modulos/alumno/pago/formulario.js")}}"></script>
<div class="box-body">  
  <div class="form-group">
    {{ Form::label("pago-motivo", "Motivo: ", ["class" => "col-sm-2 control-label"]) }}
    <div class="col-sm-3">
      {{ Form::select("motivo", App\Helpers\Enum\MotivosPago::listar(), null, ["id" => "pago-motivo", "class" => "form-control"]) }}
    </div> 
  </div> 
  <div class="form-group">      
    {{ Form::label("pago-fecha", "Fecha de pago (*): ", ["class" => "col-sm-2 control-label"]) }}
    <div class="col-sm-3">
      <div class="input-group date">
        <div class="input-group-addon">
          <i class="fa fa-calendar"></i>
        </div>                                
        {{ Form::text("fecha", null, ["id" => "pago-fecha", "class" => "form-control", "placeholder" => "dd/mm/aaaa"]) }}
      </div>
    </div>  
  </div>
  <div class="form-group">         
    {{ Form::label("pago-cuenta", "Cuenta: ", ["class" => "col-sm-2 control-label"]) }}
    <div class="col-sm-3">
      {{ Form::select("cuenta", App\Helpers\Enum\CuentasBancoPago::listar(), null, ["id" => "pago-cuenta", "class" => "form-control"]) }}
    </div>
    {{ Form::label("pago-estado", "Estado: ", ["class" => "col-sm-2 control-label"]) }}
    <div class="col-sm-3">
      {{ Form::select("estado", App\Helpers\Enum\EstadosPago::listarDisponibleCambio(), App\Helpers\Enum\EstadosPago::Realizado, ["id" => "pago-estado", "class" => "form-control"]) }}
    </div> 
  </div> 
  <div class="form-group">
    {{ Form::label("pago-descripcion", "Descripción: ", ["class" => "col-sm-2 control-label"]) }}
    <div class="col-sm-8">
      {{ Form::text("descripcion", null, ["id" => "pago-descripcion", "class" => "form-control", "maxlength" =>"255"]) }}
    </div>
  </div>  
  @include("util.archivosAdjuntos", ["adjuntos" => [(object)["idCampo" => "ImagenesComprobantes", "idHtml" => "imagenes-comprobantes", "titulo" => "Imágenes comprobantes", "archivosRegistrados" => null, "mensajeReferencia" => null, "cantidadMaximaArchivos" => 5, "soloImagenes" => true]]]) 
  <div class="form-group">
    {{ Form::label("pago-monto", "Monto total (*): ", ["class" => "col-sm-2 control-label"]) }}
    <div class="col-sm-3">
      <div class="input-group">
        <span class="input-group-addon">
          <b>S/.</b>
        </span>
        {{ Form::text("monto", null, ["id" => "pago-monto", "class" => "form-control", "maxlength" =>"19"]) }}
      </div>
    </div>
    <div id="sec-pago-saldo-favor" class="col-sm-7" style="display: none">
      <div class="checkbox">
        <label class="checkbox-custom" data-initialize="checkbox">
          {{ Form::label("pago-usar-saldo-favor", "", ["id" => "lbl-pago-usar-saldo-favor", "class" => "checkbox-label"]) }}
          {{ Form::checkbox("usarSaldoFavor", null, FALSE, ["id" => "pago-usar-saldo-favor"]) }}
        </label>
      </div>
    </div> 
  </div>                     
  <div id="sec-pago-datos-clases">
    <div class="form-group"> 
      {{ Form::label("pago-costo-x-hora-clase", "Costo por hora de clase (*): ", ["class" => "col-sm-2 control-label"]) }}   
      <div class="col-sm-3">
        <div class="input-group">
          <span class="input-group-addon">
            <b>S/.</b>
          </span>
          {{ Form::text("costoXHoraClase", number_format($alumno->costoXHoraClase, 2, ".", ","), ["id" => "pago-costo-x-hora-clase", "class" => "form-control", "maxlength" =>"19"]) }}
        </div>
      </div>
      {{ Form::label("pago-periodo-clases", "Período de clases (*): ", ["class" => "col-sm-2 control-label"]) }}
      <div class="col-sm-2">
        {{ Form::number("periodoClases", (((int) $alumno->numeroPeriodos) +1), ["id" => "pago-periodo-clases", "class" => "form-control", "maxlength" =>"11", "min" =>"1"]) }}
      </div> 
    </div> 
    <div class="form-group">  
      {{ Form::label("pago-x-hora-docente", "Pago por hora al profesor (*): ", ["class" => "col-sm-2 control-label"]) }}
      <div class="col-sm-3">
        <div class="input-group">
          <span class="input-group-addon">
            <b>S/.</b>
          </span>
          {{ Form::text("pagoXHoraProfesor", null, ["id" => "pago-x-hora-docente", "class" => "form-control", "maxlength" =>"19"]) }}
        </div>
      </div>
      {{ Form::hidden("idPago") }}
    </div>   
  </div>
  <div class="form-group">
    <div class="col-sm-12">
      <br/><span>(*) Campos obligatorios</span>
    </div>
  </div>  
</div> 