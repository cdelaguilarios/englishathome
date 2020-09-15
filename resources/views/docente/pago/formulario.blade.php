<script src="{{ asset("assets/eah/js/modulos/docente/pago/formulario.js")}}"></script>   
<div class="box-body">
  <div class="form-group">
    {{ Form::label("motivo-pago", "Motivo: ", ["class" => "col-sm-2 control-label"]) }}
    <div class="col-sm-4">
      {{ Form::text("motivo", App\Helpers\Enum\MotivosPago::listar()[App\Helpers\Enum\MotivosPago::Clases], ["id" => "motivo-pago", "class" => "form-control", "disabled" =>""]) }}
    </div>   
  </div> 
  <div class="form-group">  
    {{ Form::label("fecha-pago", "Fecha de pago (*): ", ["class" => "col-sm-2 control-label"]) }}
    <div class="col-sm-4">
      <div class="input-group date">
        <div class="input-group-addon">
          <i class="fa fa-calendar"></i>
        </div>                                
        {{ Form::text("fecha", null, ["id" => "fecha-pago", "class" => "form-control", "placeholder" => "dd/mm/aaaa"]) }}
      </div>
    </div>  
  </div>
  <div class="form-group">
    {{ Form::label("descripcion-pago", "Descripción: ", ["class" => "col-sm-2 control-label"]) }}
    <div class="col-sm-9">
      {{ Form::text("descripcion", null, ["id" => "descripcion-pago", "class" => "form-control", "maxlength" =>"255"]) }}
    </div>
  </div> 
  @include("util.archivosAdjuntos", ["adjuntos" => [(object)["idCampo" => "ImagenesComprobantes", "idHtml" => "imagenes-comprobantes", "titulo" => "Imágenes comprobantes", "archivosRegistrados" => null, "mensajeReferencia" => null, "cantidadMaximaArchivos" => 5, "soloImagenes" => true]]]) 
  <div class="form-group">
    {{ Form::label("monto-pago", "Monto total (*): ", ["class" => "col-sm-2 control-label"]) }}
    <div class="col-sm-4">
      <div class="input-group">
        <span class="input-group-addon">
          <b>S/.</b>
        </span>
        {{ Form::text("monto", null, ["id" => "monto-pago", "class" => "form-control", "disabled" =>""]) }}
      </div>
    </div>
    {{ Form::hidden("idPago") }}
    {{ Form::hidden("idProfesor") }}
  </div>        
  <div class="form-group">
    <div class="col-sm-12">
      <br/><span>(*) Campos obligatorios</span>
    </div>
  </div> 
</div>   