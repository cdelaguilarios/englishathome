{{----}}
<div class="box-body">
  <div class="form-group">
    {{ Form::label("titulo-notificacion-" . $idSeccion, "Título (*): ", ["class" => "col-sm-2 control-label"]) }}
    <div class="col-sm-10">
      {{ Form::text("titulo", null, ["id" => "titulo-notificacion-" . $idSeccion, "class" => "form-control", "maxlength" =>"100"]) }}
    </div>
  </div>
  <div class="form-group">
    {{ Form::label("mensaje-notificacion-" . $idSeccion, "Mensaje: ", ["class" => "col-sm-2 control-label"]) }}
    <div class="col-sm-10">
      {{ Form::textarea("mensaje", null, ["id" => "mensaje-notificacion-" . $idSeccion, "class" => "form-control", "rows" => "5", "maxlength" =>"8000"]) }}
    </div>                                        
  </div>  
  @include("util.archivosAdjuntos", ["adjuntos" => [(object)["idCampo" => "AdjuntosNotificacion" .  ucfirst($idSeccion), "idHtml" => "adjuntos-notificacion-" . $idSeccion, "titulo" => "Adjuntos", "archivosRegistrados" => null, "mensajeReferencia" => null, "cantidadMaximaArchivos" => 5, "soloImagenes" => false]]])   
  <div id="sec-notificaciones-{{ $idSeccion }}-programacion"> 
    <div class="form-group">
      <div class="col-sm-3 col-sm-offset-2">
        <div class="checkbox">
          <label class="checkbox-custom" data-initialize="checkbox">
            {{ Form::label("enviar-correo-" . $idSeccion, "Enviar correo ", ["class" => "checkbox-label"]) }}
            {{ Form::checkbox("enviarCorreo", null, FALSE, ["id" => "enviar-correo-" . $idSeccion]) }}
          </label>
          <span class="text-blue tooltip-checkbox" data-toggle="tooltip" title="Envio de correo al administrador ({{ App\Models\VariableSistema::obtenerXLlave("correo") }})"><i class="fa fa-question-circle"></i></span>
        </div>
      </div>                   
      <div class="col-sm-3">
        <div class="checkbox">
          <label class="checkbox-custom" data-initialize="checkbox">
            {{ Form::label("enviar-correo-entidad-" . $idSeccion, "Enviar correo " . (isset($nombreEntidad) ? "al " . $nombreEntidad : "a la entidad") . ": ", ["class" => "checkbox-label"]) }}
            {{ Form::checkbox("enviarCorreoEntidad", null, FALSE, ["id" => "enviar-correo-entidad-" . $idSeccion]) }}
          </label>
        </div>
      </div>         
    </div>   
    <div class="form-group">                  
      <div class="col-sm-3 col-sm-offset-2">
        <div class="checkbox">
          <label class="checkbox-custom" data-initialize="checkbox">
            {{ Form::label("mostrar-en-perfil-" . $idSeccion, "Mostrar en perfil ", ["class" => "checkbox-label"]) }}
            {{ Form::checkbox("mostrarEnPerfil", null, TRUE, ["id" => "mostrar-en-perfil-" . $idSeccion]) }}
          </label>
        </div>
      </div>                 
      <div class="col-sm-3">
        <div class="checkbox">
          <label class="checkbox-custom" data-initialize="checkbox">
            {{ Form::label("notificar-inmediatamente-" . $idSeccion, "Notificar inmediatamente ", ["class" => "checkbox-label"]) }}
            {{ Form::checkbox("notificarInmediatamente", null, FALSE, ["id" => "notificar-inmediatamente-" . $idSeccion]) }}
          </label>
        </div>
      </div>    
    </div>
    <div id="sec-notificaciones-{{ $idSeccion }}-fecha" class="form-group"> 
      {{ Form::label("fecha-programada-notificacion-" . $idSeccion, "Fecha notificación ", ["class" => "col-sm-2 control-label"]) }}
      <div class="col-sm-4">
        <div class="input-group date">
          <div class="input-group-addon">
            <i class="fa fa-calendar"></i>
          </div>                                
          {{ Form::text("fechaProgramada", null, ["id" => "fecha-programada-notificacion-" . $idSeccion, "class" => "form-control", "placeholder" => "dd/mm/aaaa HH:mm:ss"]) }}
        </div>
      </div> 
      {{ Form::hidden("idSeccion", $idSeccion) }}
      {{ Form::hidden("idNotificacion") }}
      {{ Form::hidden("idEntidad", (isset($idEntidad) ? $idEntidad : NULL)) }}
    </div>  
  </div>
  <div class="form-group">
    <div class="col-sm-12">
      <br/><span>(*) Campos obligatorios</span>
    </div>
  </div> 
</div>