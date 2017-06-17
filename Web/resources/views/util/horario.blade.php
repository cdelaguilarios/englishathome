@if(isset($modo) && $modo == "visualizar")
<div id="sec-perfil-horario">  
  <div id="sec-info-horario"></div>
  @if(!(isset($vistaImpresion) && $vistaImpresion))
  <button id="btn-horario" type="button" class="btn btn-primary btn-xs">{{ (isset($textoBotonEdicion) ? $textoBotonEdicion : "Editar horario") }}</button>
  @endif
</div>
{{ Form::hidden("horario", $horario) }} 
@else
<div class="form-group">
  <div class="col-sm-3">
    <button id="btn-horario" type="button" class="btn btn-primary btn-sm">
      <i class="fa fa-fw fa-calendar"></i> {{ (isset($textoBoton) ? $textoBoton : "Establecer horario disponible") }}
    </button>
  </div>    
  <div id="sec-info-horario" class="col-sm-9"></div>
</div>
{{ Form::hidden("horario") }} 
@endif
@if(!(isset($vistaImpresion) && $vistaImpresion))
<div id="mod-horario" class="modal" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">
        @if(isset($tituloModal))
        {{ $tituloModal }}
        @else
        Horario {{ ((isset($modo) && $modo == "visualizar") ? "" : "disponible ") }}para clases
        @endif
        </h4>
      </div>
      <div id="sec-horario">
        <div class="modal-body">
          <p>
            <button id="btn-instrucciones-horario" class="btn btn-primary btn-xs" type="button">
              <i class="fa fa-fw fa-info-circle"></i> {{ ((isset($subSeccion) && $subSeccion == "postulantes" && Auth::guest()) ? "Instructions" : "Instrucciones") }}
            </button>
          </p>
          <div id="sec-calendario-horario"></div>
        </div>
        <div class="modal-footer">
          <div class="sec-btn-limpiar-seleccion">
            <button id="btn-limpiar-seleccion" type="button" class="btn btn-xs"><i class="fa fa-fw fa-eraser"></i> {{ ((isset($subSeccion) && $subSeccion == "postulantes" && Auth::guest()) ? "Clear" : "Limpiar selección") }}</button>
          </div>
          <button id="btn-confirmar-horario" type="button" class="btn btn-success btn-sm">{{ ((isset($subSeccion) && $subSeccion == "postulantes" && Auth::guest()) ? "Confirm" : "Confirmar") }}</button>
        </div>
      </div>
      <div id="sec-instrucciones-horario" style="display:none;">
        <div class="modal-body">
          <div class="row">
            <div class="col-sm-12">- {{ ((isset($subSeccion) && $subSeccion == "postulantes" && Auth::guest()) ? "Click on a start time then on an end time, all the boxes within that time range will be selected." : "Haz click sobre una hora de inicio luego sobre una hora de fin, todas las casillas dentro de ese rango de horario quedaran seleccionadas.") }}</div>
            <div class="col-sm-12">
              <img src="{{ asset("assets/eah/img/instrucciones-horario-disponible-1.gif")}}"/>
            </div>
            <div class="col-sm-12">- {{ ((isset($subSeccion) && $subSeccion == "postulantes" && Auth::guest()) ? "To deselect an hour, just click on the corresponding cell." : "Para deseleccionar una hora solo has click sobre la celda correspondiente.") }}</div>
            <div class="col-sm-12">
              <img src="{{ asset("assets/eah/img/instrucciones-horario-disponible-2.gif")}}"/>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button id="btn-regresar-horario" class="btn btn-primary" type="button">{{ ((isset($subSeccion) && $subSeccion == "postulantes" && Auth::guest()) ? "Return" : "Regresar") }}</button>
        </div>
      </div>
    </div>
  </div>
</div>
@endif
<script type="text/javascript" src="{{ asset("assets/eah/js/horario.js")}}"></script>