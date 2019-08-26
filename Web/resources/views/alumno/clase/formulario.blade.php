<div class="box-header">
  <h3 id="titulo-formulario" class="box-title with-border">Nueva clase</h3>                
</div>  
<div class="box-body">    
  <div class="form-group">    
    {{ Form::label("numero-periodo-clase", "Período (*): ", ["class" => "col-sm-2 control-label"]) }}
    <div class="col-sm-3">
      {{ Form::number("numeroPeriodo", "", ["id" => "numero-periodo-clase", "class" => "form-control", "maxlength" =>"11", "min" =>"1"]) }}
    </div>
    <div id="sec-estado-clase">
      {{ Form::label("estado-clase", "Estado: ", ["class" => "col-sm-2 control-label"]) }}
      <div class="col-sm-3">
        {{ Form::select("estado", App\Helpers\Enum\EstadosClase::listarDisponibleCambio(), null, ["id" => "estado-clase", "class" => "form-control"]) }}
      </div>
    </div>
    <div id="sec-notificar-clase" class="col-sm-2">
      <div class="checkbox">
        <label class="checkbox-custom" data-initialize="checkbox">
          {{ Form::label("notificar-clase", "Notificar", ["class" => "checkbox-label"]) }}
          {{ Form::checkbox("notificar", null, FALSE, ["id" => "notificar-clase"]) }} 
        </label>
        <span class="text-blue tooltip-checkbox" data-toggle="tooltip" title="Notificar un día antes de la clase"><i class="fa fa-question-circle"></i></span>
      </div>
    </div> 
  </div>
  <div class="form-group"> 
    {{ Form::label("fecha-clase", "Fecha (*): ", ["class" => "col-sm-2 control-label"]) }}
    <div class="col-sm-3">
      <div class="input-group date">
        <div class="input-group-addon">
          <i class="fa fa-calendar"></i>
        </div>                                
        {{ Form::text("fecha", null, ["id" => "fecha-clase", "class" => "form-control", "placeholder" => "dd/mm/aaaa"]) }}
      </div>
    </div> 
  </div>
  <div class="form-group">    
    {{ Form::label("hora-inicio-clase", "Hora inicio: ", ["class" => "col-sm-2 control-label"]) }}
    <div class="col-sm-3">
      <div class="input-group date">
        <div class="input-group-addon">
          <i class="fa  fa-clock-o"></i>
        </div>    
        {{ Form::select("horaInicio", [], null, ["id" => "hora-inicio-clase", "class" => "form-control"]) }}
      </div>
    </div>
    {{ Form::label("duracion-clase", "Duración: ", ["class" => "col-sm-2 control-label"]) }}
    <div class="col-sm-3">
      <div class="input-group date">
        <div class="input-group-addon">
          <i class="fa  fa-clock-o"></i>
        </div>    
        {{ Form::select("duracion", [], null, ["id" => "duracion-clase", "class" => "form-control"]) }}
      </div>
    </div>
  </div>
  <div class="form-group">  
    {{ Form::label("costo-hora-clase", "Costo por hora (*): ", ["class" => "col-sm-2 control-label"]) }}   
    <div class="col-sm-3">
      <div class="input-group">
        <span class="input-group-addon">
          <b>S/.</b>
        </span>
        {{ Form::text("costoHora", number_format($costoHoraClase, 2, ".", ","), ["id" => "costo-hora-clase", "class" => "form-control", "maxlength" =>"19"]) }}
      </div>
    </div> 
    {{ Form::label("id-pago-clase", "Código de pago: ", ["class" => "col-sm-2 control-label"]) }}
    <div class="col-sm-3">
      {{ Form::select("idPago", App\Models\PagoAlumno::listar($idAlumno, TRUE)->lists("id", "id")->toArray(), null, ["id" => "id-pago-clase", "class" => "form-control", "placeholder" => "Seleccionar código de pago"]) }}
    </div>
    <div class="col-sm-2">
      <a href="javascript:void(0);" onclick="verDatosPagosClase('id-pago-clase');"><i class="fa fa-eye"></i></a>
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
        {{ Form::label("", "", ["class" => "col-sm-4 control-label nombre-docente-clase"]) }}
      </div>
      <div class="form-group">
        {{ Form::label("costo-hora-docente", "Pago por hora al profesor (*): ", ["class" => "col-sm-3 control-label"]) }}
        <div class="col-sm-4">
          <div class="input-group">
            <span class="input-group-addon">
              <b>S/.</b>
            </span>
            {{ Form::text("costoHoraDocente", null, ["id" => "costo-hora-docente", "class" => "form-control", "maxlength" =>"19"]) }}
          </div>
        </div>
      </div>       
    </div>
    <div class="form-group">
      <div class="col-sm-12">
        <br/><span>(*) Campos obligatorios</span>
      </div>
    </div>
    {{ Form::hidden("idDocente", "", ["class" => "id-docente-clase"]) }} 
    {{ Form::hidden("idClase") }} 
    {{ Form::hidden("idAlumno", $idAlumno) }}
  </div> 
  <div class="box-footer">    
    <button type="button" class="btn btn-default btn-cancelar-clase">Cancelar</button>
    <button id="btn-guardar-clase" type="submit" class="btn btn-success pull-right">Registrar</button>
  </div>
</div>