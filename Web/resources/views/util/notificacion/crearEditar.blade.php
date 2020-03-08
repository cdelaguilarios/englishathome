<div id="sec-notificaciones-{{ $idSeccion }}-crear-editar" style="display: none">
  <div class="box-header">
    <h3 class="box-title with-border"></h3>                
  </div> 
  {{ Form::open(["url" => route("notificaciones.registrar.actualizar"), "id" => "formulario-notificacion-" . $idSeccion, "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }} 
  @include("util.notificacion.formulario")
  <div class="box-footer">    
    <button id="btn-cancelar-notificacion-{{ $idSeccion }}" type="button" class="btn btn-default">Cancelar</button>
    <button id="btn-registrar-notificacion-{{ $idSeccion }}" type="submit" class="btn btn-success pull-right">Guardar cambios</button>
  </div> 
  {{ Form::close() }}
</div>