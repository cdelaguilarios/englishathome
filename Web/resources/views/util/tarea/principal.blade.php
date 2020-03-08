{{----}}
<script src="{{ asset("assets/eah/js/modulos/util/tarea/principal.js")}}"></script>
<div id="mod-tareas" class="modal" data-keyboard="false" style="text-align: initial">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Tareas</h4>
      </div>
      <div class="modal-body">   
        <div id="sec-notificaciones-mensajes" tabindex="0"></div>
        @include("util.tarea.lista") 
      </div>
    </div>
  </div>
</div>