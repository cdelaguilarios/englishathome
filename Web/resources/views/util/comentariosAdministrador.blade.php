{{ Form::open(["url" => route("entidades.actualizar.comentarios.administrador", ["id" => $idEntidad]), "id" => "formulario-comentarios-administrador", "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }}
<div class="row">
  <div class="col-sm-12">
    <div class="box-body">
      <div class="form-group">
        <div class="col-sm-12">
          {{ Form::textarea("comentarioAdministrador", $comentarioAdministrador, ["id" => "comentarios-administrador", "class" => "form-control", "rows" => "10", "maxlength" =>"8000"]) }}
        </div>                                        
      </div>
    </div>
    <div class="box-footer">    
      <div class="form-group">          
        <div class="col-sm-12">               
          <button type="submit" class="btn btn-success pull-right">Guardar</button>
        </div>
      </div>
    </div>
  </div>
</div>
{{ Form::close() }}
<script src="{{ asset("assets/eah/js/comentariosAdministrador.js")}}"></script>