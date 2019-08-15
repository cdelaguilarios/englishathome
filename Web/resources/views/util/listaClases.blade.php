<div id="sec-men-lista-clases"></div><br/>
<div class="row">
  <div class="col-sm-12">
    <div>       
      <div class="box-body">
        <table id="tab-lista-clases" class="table table-bordered table-hover">
          <thead>
            <tr>
              <th>N°</th>    
              <th class="all">Datos</th>
              <th>Estado</th>
              <th>Comentarios</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>
<div id="mod-comentarios" class="modal" data-keyboard="false" style="text-align: initial">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Comentarios</h4>
      </div>
      {{ Form::open(["url" => route("alumnos.clases.actualizar.comentarios"), "id" => "formulario-comentarios", "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }}
      <div class="modal-body">
        <div class="row">
          <div class="col-sm-12">
            <div class="box-body">
              <div class="form-group">
                <div class="col-sm-12">
                  {{ Form::textarea("comentario", null, ["class" => "form-control", "rows" => "10", "maxlength" =>"8000"]) }}
                </div>
              </div>
              {{ Form::hidden("idClase") }}
              {{ Form::hidden("idAlumno") }}
              {{ Form::hidden("tipo") }}
            </div>   
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success btn-sm">Guardar</button>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>
<script>
  var urlListarClases = "{{ route('alumnos.clases.listar', ['id' => 0]) }}";
</script>