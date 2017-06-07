<div class="row">
  <div class="col-sm-12">
    <div class="box-body">
      <div class="form-group">
        <div class="col-sm-12">
          {{ Form::textarea("comentarioAdministrador", $comentarioAdministrador, ["class" => "form-control", "rows" => "10", "maxlength" =>"8000"]) }}
        </div>                                        
      </div>
      {{ Form::hidden("id", $idEntidad) }}
    </div>
    <div class="box-footer">    
      <div class="form-group">          
        <div class="col-sm-12">               
          <button type="submit" class="btn btn-success pull-right">"Guardar"</button>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="{{ asset("assets/eah/js/comentariosAdministrador.js")}}"></script>