<div id="sec-notificaciones-historial-lista" style="display: none">
  <div>
    <a id="btn-nueva-notificacion-historial" class="btn btn-sm btn-primary pull-right">
      <i class="fa fa-plus"></i> Agregar evento
    </a>
  </div>
  <div class="clearfix"></div>
  <ul class="lista timeline timeline-inverse"></ul>
  <div class="sec-btn-carga-mas" style="display:none">
    <a class="btn btn-sm btn-primary" onclick="listaNotificacionesHistorial.cargarDatos()">
      <i class="fa fa-angle-double-down"></i> Mostrar más
    </a>
  </div>
  {{ Form::hidden("numeroCarga", 0) }} 
</div>