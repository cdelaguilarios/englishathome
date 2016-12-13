@if(isset($modo) && $modo == "visualizar")
{!! Form::hidden("horario", $horario) !!} 
<div id="sec-info-horario"></div>
@else
<div class="form-group">
    <div class="col-sm-3">
        <button id="btnHorario" type="button" class="btn btn-primary btn-sm">
            <i class="fa fa-fw fa-calendar"></i> Establecer horario disponible
        </button>
    </div>    
    <div id="sec-info-horario" class="col-sm-9"></div>
</div>
<div id="mod-horario" class="modal" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Horario disponible para clases</h4>
            </div>
            <div class="modal-body">
                <div id="calendario"></div>
            </div>
            <div class="modal-footer">
                <div class="sec-btn-limpiar-seleccion">
                    <button id="btn-limpiar-seleccion" type="button" class="btn btn-xs"><i class="fa fa-fw fa-eraser"></i> Limpiar selección</button>
                </div>
                <button id="btn-confirmar-horario" type="button" class="btn btn-success btn-sm">Confirmar</button>
            </div>
        </div>
    </div>
</div>
{!! Form::hidden("horario") !!} 
@endif
<script type="text/javascript" src="{{ asset("assets/eah/js/horario.js")}}"></script>