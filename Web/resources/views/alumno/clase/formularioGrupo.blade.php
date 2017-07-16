<div class="box-header">
  <h3 class="box-title with-border">Editar grupo de clases</h3>                
</div>  
<div class="box-body"> 
  <div class="form-group">
    <div class="col-sm-3 col-sm-offset-1">
      <div class="checkbox">
        <label class="checkbox-custom" data-initialize="checkbox">
          {{ Form::label("editar-datos-generales-clases", "Editar datos generales", ["class" => "checkbox-label"]) }}
          {{ Form::checkbox("editarDatosGenerales", null, FALSE, ["id" => "editar-datos-generales-clases", "data-seccion" => "sec-clase-41"]) }} 
        </label>
      </div>
    </div>
    <div class="col-sm-3 col-sm-offset-1">
      <div class="checkbox">
        <label class="checkbox-custom" data-initialize="checkbox">
          {{ Form::label("editar-datos-tiempo-clases", "Editar datos de tiempo", ["class" => "checkbox-label"]) }}
          {{ Form::checkbox("editarDatosTiempo", null, FALSE, ["id" => "editar-datos-tiempo-clases", "data-seccion" => "sec-clase-42"]) }} 
        </label>
      </div>
    </div>
  </div>   
  <div class="form-group">
    <div class="col-sm-3 col-sm-offset-1">
      <div class="checkbox">
        <label class="checkbox-custom" data-initialize="checkbox">
          {{ Form::label("editar-datos-pago-clases", "Editar datos de pago", ["class" => "checkbox-label"]) }}
          {{ Form::checkbox("editarDatosPago", null, FALSE, ["id" => "editar-datos-pago-clases", "data-seccion" => "sec-clase-43"]) }} 
        </label>
      </div>
    </div>
    <div class="col-sm-3 col-sm-offset-1">      
      <div class="checkbox">
        <label class="checkbox-custom" data-initialize="checkbox">
          {{ Form::label("editar-datos-profesor-clases", "Editar datos de profesor", ["class" => "checkbox-label"]) }}
          {{ Form::checkbox("editarDatosProfesor", null, FALSE, ["id" => "editar-datos-profesor-clases", "data-seccion" => "sec-clase-44", "data-idcbsecundario" => "editar-datos-tiempo-clases"]) }} 
        </label>
      </div>
    </div>
  </div>
  <div id="sec-clase-41" class="form-group">    
    {{ Form::label("numero-periodo-clases", "Período (*): ", ["class" => "col-sm-2 control-label"]) }}
    <div class="col-sm-3">
      {{ Form::number("numeroPeriodo", "", ["id" => "numero-periodo-clases", "class" => "form-control", "maxlength" =>"11", "min" =>"1"]) }}
    </div>
    {{ Form::label("estado-clases", "Estado: ", ["class" => "col-sm-2 control-label"]) }}
    <div class="col-sm-3">
      {{ Form::select("estado", App\Helpers\Enum\EstadosClase::listarCambio(), null, ["id" => "estado-clases", "class" => "form-control"]) }}
    </div>
  </div>
  <div id="sec-clase-42" class="form-group">    
    {{ Form::label("hora-inicio-clases", "Hora inicio (*): ", ["class" => "col-sm-2 control-label"]) }}
    <div class="col-sm-3">
      <div class="input-group date">
        <div class="input-group-addon">
          <i class="fa  fa-clock-o"></i>
        </div>    
        {{ Form::select("horaInicio", [], null, ["id" => "hora-inicio-clases", "class" => "form-control"]) }}
      </div>
    </div>
    {{ Form::label("duracion-clases", "Duración (*): ", ["class" => "col-sm-2 control-label"]) }}
    <div class="col-sm-3">
      <div class="input-group date">
        <div class="input-group-addon">
          <i class="fa  fa-clock-o"></i>
        </div>    
        {{ Form::select("duracion", [], null, ["id" => "duracion-clases", "class" => "form-control"]) }}
      </div>
    </div>
  </div> 
  <div id="sec-clase-43" class="form-group">  
    {{ Form::label("costo-hora-clases", "Costo por hora (*): ", ["class" => "col-sm-2 control-label"]) }}   
    <div class="col-sm-3">
      <div class="input-group">
        <span class="input-group-addon">
          <b>S/.</b>
        </span>
        {{ Form::text("costoHora", number_format($costoHoraClase, 2, ".", ","), ["id" => "costo-hora-clases", "class" => "form-control", "maxlength" =>"19"]) }}
      </div>
    </div> 
    {{ Form::label("id-pago-clases", "Código de pago: ", ["class" => "col-sm-2 control-label"]) }}
    <div class="col-sm-3">
      {{ Form::select("idPago", App\Models\PagoAlumno::listar($idAlumno, TRUE)->lists("id", "id")->toArray(), null, ["id" => "id-pago-clases", "class" => "form-control", "placeholder" => "Seleccionar código de pago"]) }}
    </div>
    <div class="col-sm-2">
      <a href="javascript:void(0);" onclick="verDatosPagosClase('id-pago-clases');"><i class="fa fa-eye"></i></a>
    </div>
  </div>
  <div id="sec-clase-44" class="box-body">                        
    <div class="form-group">
      <div class="col-sm-12">
        <button type="button" class="btn btn-primary btn-sm btn-docentes-disponibles-clase">Elegir profesor disponible</button> 
      </div>
    </div>
    <div id="sec-clase-441">
      <div class="form-group">
        {{ Form::label("", "", ["class" => "col-sm-4 control-label nombre-docente-clase"]) }}
      </div>
      <div class="form-group">
        {{ Form::label("costo-hora-docente-clases", "Pago por hora al profesor (*): ", ["class" => "col-sm-3 control-label"]) }}
        <div class="col-sm-4">
          <div class="input-group">
            <span class="input-group-addon">
              <b>S/.</b>
            </span>
            {{ Form::text("costoHoraDocente", null, ["id" => "costo-hora-docente-clases", "class" => "form-control", "maxlength" =>"19"]) }}
          </div>
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
  {{ Form::hidden("idsClases") }} 
  {{ Form::hidden("idAlumno", $idAlumno) }} 
  <div class="box-footer">    
    <button type="button" class="btn btn-default  btn-cancelar-clase">Cancelar</button>
    <button type="submit" class="btn btn-success pull-right">Guardar</button>
  </div>
</div>