<div class="box-header">
  <h3 class="box-title with-border">Nueva clase</h3>                
</div>  
<div class="box-body">           
  <div class="form-group">
    {{ Form::label("fecha", "Fecha: ", ["class" => "col-sm-2 control-label"]) }}
    <div class="col-sm-3">
      <div class="input-group date">
        <div class="input-group-addon">
          <i class="fa fa-calendar"></i>
        </div>                                
        {{ Form::text("fecha", NULL, ["id" => "fecha-clase", "class" => "form-control  pull-right"]) }}
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
        {{ Form::select("horaInicio", [], NULL, ["id" => "hora-inicio-clase", "class" => "form-control"]) }}
      </div>
    </div>
    {{ Form::label("duracion", "Duración: ", ["class" => "col-sm-1 control-label"]) }}
    <div class="col-sm-2">
      <div class="input-group date">
        <div class="input-group-addon">
          <i class="fa  fa-clock-o"></i>
        </div>    
        {{ Form::select("duracion", [], NULL, ["id" => "duracion-clase", "class" => "form-control"]) }}
      </div>
    </div>
  </div>
  <div class="form-group">  
    {{ Form::label("costoHora", "Costo por hora (*): ", ["class" => "col-sm-2 control-label"]) }}   
    <div class="col-sm-3">
      <div class="input-group">
        <span class="input-group-addon">
          <b>S/.</b>
        </span>
        {{ Form::text("costoHora", number_format($costoHoraClase, 2, ".", ","), array("class" => "form-control", "maxlength" =>"19")) }}
      </div>
    </div> 
    {{ Form::label("numeroPeriodo", "Período (*): ", ["class" => "col-sm-2 control-label"]) }}
    <div class="col-sm-2">
      {{ Form::number("numeroPeriodo", "", ["class" => "form-control", "maxlength" =>"11", "min" =>"1"]) }}
    </div>
    <div class="col-sm-3">
      <div class="checkbox">
        <label class="checkbox-custom" data-initialize="checkbox">
          {{ Form::label("notificar", "Notificar", ["class" => "checkbox-label"]) }}
          {{ Form::checkbox("notificar", null, FALSE) }}
        </label>
      </div>
    </div> 
  </div>
  <div class="box-body">                        
    <div class="form-group">
      <div class="col-sm-12">
        <button type="button" class="btn btn-primary btn-sm btn-docentes-disponibles-clase">Elegir profesor disponible</button> 
      </div>
    </div>
    <div id="sec-clase-21">
      <div class="form-group">
        {{ Form::label("", "", ["class" => "col-sm-3 control-label nombre-docente-clase"]) }}
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
      {{ Form::hidden("idDocente", "", ["id" => "id-docente-clase-registrar"]) }} 
      {{ Form::hidden("idClase") }} 
    </div>
  </div> 
  <div class="box-footer">    
    <button type="button" class="btn btn-default btn-cancelar-clase">Cancelar</button>
    <button type="submit" class="btn btn-success pull-right">Registrar</button>
  </div>
</div>