{{----}}
<script src="{{ asset("assets/eah/js/modulos/util/tarea/formulario.js")}}"></script>
<div class="box-body">
  <div class="form-group">
    {{ Form::label("titulo", "Título (*): ", ["class" => "col-sm-2 control-label"]) }}
    <div class="col-sm-10">
      {{ Form::text("titulo", null, ["class" => "form-control", "maxlength" =>"100"]) }}
    </div>
  </div>
  <div class="form-group">
    {{ Form::label("mensaje", "Mensaje: ", ["class" => "col-sm-2 control-label"]) }}
    <div class="col-sm-10">
      {{ Form::textarea("mensaje", null, ["class" => "form-control", "rows" => "5", "maxlength" =>"8000"]) }}
    </div>                                        
  </div>  
  @include("util.archivosAdjuntos", ["adjuntos" => [(object)["idCampo" => "Adjuntos", "idHtml" => "adjuntos", "titulo" => "Adjuntos", "archivosRegistrados" => null, "mensajeReferencia" => null, "cantidadMaximaArchivos" => 5, "soloImagenes" => false]]])   
  <div id="sec-tareas-programacion">   
    <div class="form-group">                       
      <div class="col-sm-3">
        <div class="checkbox">
          <label class="checkbox-custom" data-initialize="checkbox">
            {{ Form::label("notificarInmediatamente", "Notificar inmediatamente ", ["class" => "checkbox-label"]) }}
            {{ Form::checkbox("notificarInmediatamente", null, FALSE) }}
          </label>
        </div>
      </div>    
    </div>
    <div id="sec-notificaciones-historial-fecha" class="form-group"> 
      {{ Form::label("fechaProgramada", "Fecha notificación ", ["class" => "col-sm-2 control-label"]) }}
      <div class="col-sm-4">
        <div class="input-group date">
          <div class="input-group-addon">
            <i class="fa fa-calendar"></i>
          </div>                                
          {{ Form::text("fechaProgramada", null, ["class" => "form-control", "placeholder" => "dd/mm/aaaa HH:mm:ss"]) }}
        </div>
      </div> 
      {{ Form::hidden("idTarea") }}
    </div>  
  </div>
  <div class="form-group">
    <div class="col-sm-12">
      <br/><span>(*) Campos obligatorios</span>
    </div>
  </div> 
</div>