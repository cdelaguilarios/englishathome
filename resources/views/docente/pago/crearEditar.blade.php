<script src="{{ asset("assets/eah/js/modulos/docente/pago/crearEditar.js")}}"></script>  
<div class="col-sm-12">
  <div id="mod-pago" class="modal" data-keyboard="false" style="text-align: initial">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title">Editar pago al profesor(a)</h4>
        </div>
        {{ Form::open(["url" => route("docentes.pagosXClases.registrarActualizar"), "id" => "formulario-pago", "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }}
        <div class="modal-body">
          <div class="row">
            <div class="col-sm-12">
              @include("docente.pago.formulario")
            </div>
          </div>
        </div>
        <div class="modal-footer">         
          <button type="submit" class="btn btn-success">Guardar cambios</button>
        </div>
        {{ Form::close() }}
      </div>
    </div>
  </div>
</div>