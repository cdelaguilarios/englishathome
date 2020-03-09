{{----}}
<div class="box-body">
  <div class="form-group">
    {{ Form::label("titulo-tarea", "Título (*): ", ["class" => "col-sm-2 control-label"]) }}
    <div class="col-sm-10">
      {{ Form::text("titulo", null, ["id" => "titulo-tarea", "class" => "form-control", "maxlength" =>"100"]) }}
    </div>
  </div>
  <div class="form-group">
    {{ Form::label("mensaje-tarea", "Mensaje: ", ["class" => "col-sm-2 control-label"]) }}
    <div class="col-sm-10">
      {{ Form::textarea("mensaje", null, ["id" => "mensaje-tarea", "class" => "form-control", "rows" => "5", "maxlength" =>"8000"]) }}
    </div>                                        
  </div>  
  <div class="form-group">
    {{ Form::label("sel-usuario-tarea", "Usuario asignado (*): ", ["class" => "col-sm-2 control-label"]) }}
    <div class="col-sm-10">
      {{ Form::select("idUsuarioAsignado", [], null, ["id"=>"sel-usuario-tarea", "class" => "form-control", "data-seccion" => "perfil", "style" => "width: 100%"]) }}
    </div>                                        
  </div>
  @include("util.archivosAdjuntos", ["adjuntos" => [(object)["idCampo" => "AdjuntosTarea", "idHtml" => "adjuntos-tarea", "titulo" => "Adjuntos", "archivosRegistrados" => null, "mensajeReferencia" => null, "cantidadMaximaArchivos" => 5, "soloImagenes" => false]]])   
  <div id="sec-tareas-programacion">   
    <div class="form-group">                       
      <div class="col-sm-3 col-sm-offset-2">
        <div class="checkbox">
          <label class="checkbox-custom" data-initialize="checkbox">
            {{ Form::label("notificar-inmediatamente-tarea", "Notificar inmediatamente ", ["class" => "checkbox-label"]) }}
            {{ Form::checkbox("notificarInmediatamente", null, FALSE, ["id" => "notificar-inmediatamente-tarea"]) }}
          </label>
        </div>
      </div>    
    </div>
    <div id="sec-tareas-fecha" class="form-group"> 
      {{ Form::label("fecha-programada-tarea", "Fecha notificación ", ["class" => "col-sm-2 control-label"]) }}
      <div class="col-sm-4">
        <div class="input-group date">
          <div class="input-group-addon">
            <i class="fa fa-calendar"></i>
          </div>                                
          {{ Form::text("fechaProgramada", null, ["id" => "fecha-programada-tarea", "class" => "form-control", "placeholder" => "dd/mm/aaaa HH:mm:ss"]) }}
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