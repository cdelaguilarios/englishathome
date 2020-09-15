<script src="{{ asset("assets/eah/js/modulos/profesor/pago/formulario.js")}}"></script>
<div class="box-body">
  <div class="form-group">
    {{ Form::label("", "Motivo: ", ["class" => "col-sm-2 control-label"]) }}
    <div class="col-sm-3">
      {{ Form::text("motivo", App\Helpers\Enum\MotivosPago::listar()[App\Helpers\Enum\MotivosPago::Otros], ["class" => "form-control", "disabled" =>""]) }}
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
    {{ Form::hidden("idPago") }}
  </div>        
  <div class="form-group">
    <div class="col-sm-12">
      <br/><span>(*) Campos obligatorios</span>
    </div>
  </div>      
  <div class="form-group">
    <div class="col-sm-12">
      <span>
        <b>Nota: </b> los pagos por clases se pueden registrar en la sección <a href="{{ route("docentes.pagosXClases")}}" target="_blank">Docentes/Pagos por clases</a>
      </span>
    </div>
  </div>      
</div> 