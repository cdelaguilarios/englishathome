<div id="sec-tareas-crear-editar" style="display: none">
  <div class="box-header">
    <h3 class="box-title with-border"></h3>                
  </div> 
  {{ Form::open(["url" => route("tareas.registrar.actualizar"), "id" => "formulario-tarea", "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }} 
  @include("util.tarea.formulario")
  <div class="box-footer">    
    <button id="btn-cancelar-tarea" type="button" class="btn btn-default">Cancelar</button>
    <button id="btn-registrar-tarea" type="submit" class="btn btn-success pull-right">Guardar cambios</button>
  </div> 
  {{ Form::close() }}
</div>