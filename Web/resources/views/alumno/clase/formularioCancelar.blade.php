
<div class="box-header">
  <h3 class="box-title with-border">Cancelar clase</h3>                
</div>  
<div class="box-body">
  <div class="form-group">
    <div class="col-sm-4">
      {{ Form::select("tipoCancelacion", App\Helpers\Enum\TiposCancelacionClase::listar(), NULL, ["id" => "tipo-cancelacion-clase", "class" => "form-control"]) }}
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-12">
      <label class="control-label">
        <i class="fa fa-edit"></i> Datos de cancelación
      </label>
    </div>
  </div>
  <div id="sec-clase-31" class="box-body">
    <div class="form-group">
      {{ Form::label("pagoProfesor", "Pago al profesor por clase cancelada: ", ["class" => "col-sm-4 control-label"]) }}
      <div class="col-sm-3">
        <div class="input-group">
          <span class="input-group-addon">
            <b>S/.</b>
          </span>
          {{ Form::text("pagoProfesor", NULL, ["class" => "form-control", "maxlength" => "19"]) }}
        </div>
      </div>
      <div class="col-sm-3">
        <div class="checkbox">
          <label class="checkbox-custom" data-initialize="checkbox">
            {{ Form::label("reprogramar-clase-can-alu", "Reprogramar clase ") }}
            {{ Form::checkbox("reprogramarCancelacionAlumno", null, FALSE, ["id" => "reprogramar-clase-can-alu"]) }}
          </label>
        </div>
      </div> 
    </div>
  </div>      
  <div id="sec-clase-32" class="box-body">
    <div class="form-group">
      <div class="col-sm-3">
        <div class="checkbox">
          <label class="checkbox-custom" data-initialize="checkbox">
            {{ Form::label("reprogramar-clase-can-pro", "Reprogramar clase ") }}
            {{ Form::checkbox("reprogramarCancelacionProfesor", null, FALSE, ["id" => "reprogramar-clase-can-pro"]) }}
          </label>
        </div>
      </div> 
    </div>
  </div>    
  <div id="sec-clase-33">
    <div class="form-group">
      <div class="col-sm-12">
        <label class="control-label">
          <i class="fa fa-calendar-check-o"></i> Nueva clase (reprogramación)
        </label>
      </div>
    </div>           
    <div class="form-group">
      {{ Form::label("fecha", "Fecha: ", ["class" => "col-sm-2 control-label"]) }}
      <div class="col-sm-3">
        <div class="input-group date">
          <div class="input-group-addon">
            <i class="fa fa-calendar"></i>
          </div>                                
          {{ Form::text("fecha", NULL, ["id" => "fecha-clase-reprogramada", "class" => "form-control  pull-right"]) }}
        </div>
      </div>                        
    </div>
    <div class="form-group">
      {{ Form::label("horaInicio", "Hora inicio: ", ["class" => "col-sm-2 control-label"]) }}
      <div class="col-sm-3">
        <div class="input-group date">
          <div class="input-group-addon">
            <i class="fa  fa-clock-o"></i>
          </div>    
          {{ Form::select("horaInicio", [], NULL, ["id" => "hora-inicio-clase-reprogramada", "class" => "form-control"]) }}
        </div>
      </div>
      {{ Form::label("duracion", "Duración: ", ["class" => "col-sm-1 control-label"]) }}
      <div class="col-sm-2">
        <div class="input-group date">
          <div class="input-group-addon">
            <i class="fa  fa-clock-o"></i>
          </div>    
          {{ Form::select("duracion", [], NULL, ["id" => "duracion-clase-reprogramada", "class" => "form-control"]) }}
        </div>
      </div>
    </div>
    <div class="box-body">                        
      <div class="form-group">
        <div class="col-sm-12">
          <button type="button" class="btn btn-primary btn-sm btn-docentes-disponibles-clase">Elegir profesor disponible</button> 
        </div>
      </div>
      <div id="sec-clase-331">
        <div class="form-group">
          {{ Form::label("", "", ["class" => "col-sm-4 control-label nombre-docente-clase"]) }}
        </div>
        <div class="form-group">
          {{ Form::label("costoHoraDocente", "Pago por hora al profesor (*): ", ["class" => "col-sm-3 control-label"]) }}
          <div class="col-sm-4">
            <div class="input-group">
              <span class="input-group-addon">
                <b>S/.</b>
              </span>
              {{ Form::text("costoHoraDocente", null, ["class" => "form-control", "maxlength" =>"19"]) }}
            </div>
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-12">
            <br/><span>(*) Campos obligatorios</span>
          </div>
        </div>
      </div>
      {{ Form::hidden("idDocente", "", ["class" => "id-docente-clase"]) }} 
      {{ Form::hidden("idClase") }} 
      {{ Form::hidden("idAlumno", $idAlumno) }} 
      {{ Form::hidden("idProfesor") }} 
    </div>
  </div>
</div>      
<div class="box-footer">    
  <button type="button" class="btn btn-default btn-cancelar-clase">Cancelar</button>
  <button type="submit" class="btn btn-success pull-right">Registrar</button>
</div>