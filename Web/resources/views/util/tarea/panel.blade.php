<div id="sec-tareas-panel" class="container-fluid" style="display: none">
  <div class="row">
    <div class="form-group">          
      <div class="col-sm-3">
        {{ Form::select("", ["1" => "Mis tareas", "0" => "Tareas creadas por mí"], null, ["id"=>"sel-tipo-tareas", "class" => "form-control"]) }}
      </div>
      <div class="col-md-9">
        <a id="btn-nueva-tarea" class="btn btn-sm btn-primary pull-right">
          <i class="fa fa-plus"></i> Agregar tarea
        </a>
      </div>
    </div>
  </div>
  <hr/>
  <div class="clearfix"></div>
  <div class="row">
    <div class="col-md-12">
      <div id="panel-tareas"></div>
    </div>
  </div>
</div>