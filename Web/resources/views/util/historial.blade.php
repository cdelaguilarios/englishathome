@if (Session::has("historial"))
<script>
  var registroHistorial = true;
</script>
@endif
<div id="sec-men-historial"></div>
<div id="sec-historial-1">
  @if(!(isset($observador) && $observador == 1))
  <div>
    <a id="btn-nuevo-evento-historial" class="btn btn-sm btn-primary pull-right">
      <i class="fa fa-plus"></i> Agregar evento
    </a>
  </div>
  <div class="clearfix"></div>
  @endif
  <ul id="sec-historial" class="timeline timeline-inverse"></ul>
  <div id="sec-boton-carga-mas-historial" style="display:none">
    <a class="btn btn-sm btn-primary" onclick="cargarListaHistorial()">
      <i class="fa fa-angle-double-down"></i> Mostrar más
    </a>
  </div>
  {{ Form::hidden("numeroCarga", 0) }} 
</div>
@if(!(isset($observador) && $observador == 1))
<div id="sec-historial-2" style="display: none">
  {{ Form::open(["url" => route("historial.registrar", ["idEntidad" => $idEntidad]), "id" => "formulario-registrar-historial", "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }}
  <div class="box-header">
    <h3 class="box-title with-border">Nuevo evento</h3>                
  </div>  
  <div class="box-body">
    <div class="form-group">
      {{ Form::label("titulo-evento-historial", "Título (*): ", ["class" => "col-sm-2 control-label"]) }}
      <div class="col-sm-10">
        {{ Form::text("titulo", null, ["id" => "titulo-evento-historial", "class" => "form-control", "maxlength" =>"100"]) }}
      </div>
    </div>
    <div class="form-group">
      {{ Form::label("mensaje-evento-historial", "Mensaje: ", ["class" => "col-sm-2 control-label"]) }}
      <div class="col-sm-10">
        {{ Form::textarea("mensaje", null, ["id" => "fmensaje-evento-historial", "class" => "form-control", "rows" => "5", "maxlength" =>"4000"]) }}
      </div>                                        
    </div>    
    <div class="form-group">
      <div class="col-sm-3 col-sm-offset-2">
        <div class="checkbox">
          <label class="checkbox-custom" data-initialize="checkbox">
            {{ Form::label("enviar-correo-evento-historial", "Enviar correo: ", ["class" => "checkbox-label"]) }}
            {{ Form::checkbox("enviarCorreo", null, FALSE, ["id" => "enviar-correo-evento-historial"]) }}
          </label>
        </div>
      </div>                        
      <div class="col-sm-3">
        <div class="checkbox">
          <label class="checkbox-custom" data-initialize="checkbox">
            {{ Form::label("mostrar-perfil-evento-historial", "Mostrar en perfil: ", ["class" => "checkbox-label"]) }}
            {{ Form::checkbox("mostrarEnPerfil", null, TRUE, ["id" => "mostrar-perfil-evento-historial"]) }}
          </label>
        </div>
      </div>                        
      <div class="col-sm-4">
        <div class="checkbox">
          <label class="checkbox-custom" data-initialize="checkbox">
            {{ Form::label("notificar-inmediatamente-evento-historial", "Notificar inmediatamente: ", ["class" => "checkbox-label"]) }}
            {{ Form::checkbox("notificarInmediatamente", null, TRUE, ["id" => "notificar-inmediatamente-evento-historial"]) }}
          </label>
        </div>
      </div>
    </div>
    <div id="sec-historial-21" style="display: none" class="form-group"> 
      {{ Form::label("fecha-notificacion-evento-historial", "Fecha notificación: ", ["class" => "col-sm-2 control-label"]) }}
      <div class="col-sm-3">
        <div class="input-group date">
          <div class="input-group-addon">
            <i class="fa fa-calendar"></i>
          </div>                                
          {{ Form::text("fechaNotificacion", null, ["id" => "fecha-notificacion-evento-historial", "class" => "form-control", "placeholder" => "dd/mm/aaaa"]) }}
        </div>
      </div> 
    </div>
  </div>
  <div class="box-footer">    
    <button id="btn-cancelar-evento-historial" type="button" class="btn btn-default">Cancelar</button>
    <button type="submit" class="btn btn-success pull-right">Registrar</button>
  </div>
  {{ Form::close() }}
</div>
@endif
<script>
  var urlCargarHistorial = "{{ route('historial.perfil', ['idEntidad' => $idEntidad, 'observador' => $observador]) }}";
</script>
<script src="{{ asset("assets/eah/js/historial.js")}}"></script>