<div class="box-header">
  <h3 class="box-title with-border">Nuevo pago</h3>                
</div>  
<div class="box-body">
  <div id="sec-pago-21">
    <div class="form-group">
      {{ Form::label("motivo", "Motivo (*): ", ["class" => "col-sm-2 control-label"]) }}
      <div class="col-sm-3">
        {{ Form::select("motivo", App\Helpers\Enum\MotivosPago::listar(), NULL, ["id" => "motivo-pago", "class" => "form-control"]) }}
      </div>                  
    </div> 
    <div class="form-group">
      {{ Form::label("descripcion", "Descripción: ", ["class" => "col-sm-2 control-label"]) }}
      <div class="col-sm-10">
        {{ Form::text("descripcion", NULL, ["class" => "form-control", "maxlength" =>"255"]) }}
      </div>
    </div>
    <div class="form-group">
      {{ Form::label("imagenComprobante", "Imagen de comprobante: ", ["class" => "col-sm-2 control-label"]) }}
      <div class="col-sm-4">
        {{ Form::file("imagenComprobante", NULL) }}
      </div>
    </div> 
    <div class="form-group">
      {{ Form::label("monto", "Monto total (*): ", ["class" => "col-sm-2 control-label"]) }}
      <div class="col-sm-3">
        <div class="input-group">
          <span class="input-group-addon">
            <b>S/.</b>
          </span>
          {{ Form::text("monto", NULL, ["id" => "monto-pago", "class" => "form-control", "maxlength" =>"19"]) }}
        </div>
      </div>
      @if($totalSaldoFavor > 0)
      <div class="col-sm-7">
        <div class="checkbox">
          <label class="checkbox-custom" data-initialize="checkbox">
            {{ Form::label("usarSaldoFavor", "Utilizar saldo a favor total (S/. " . number_format($totalSaldoFavor, 2, ".", ",") . ")", ["class" => "checkbox-label"]) }}
            {{ Form::checkbox("usarSaldoFavor", NULL, FALSE, ["id" => "usar-saldo-favor"]) }}
          </label>
        </div>
      </div> 
      @endIf
    </div>                     
    <div id="sec-pago-211">
      <div class="form-group">  
        {{ Form::label("costoHoraClase", "Costo por hora (*): ", ["class" => "col-sm-2 control-label"]) }}   
        <div class="col-sm-3">
          <div class="input-group">
            <span class="input-group-addon">
              <b>S/.</b>
            </span>
            {{ Form::text("costoHoraClase", number_format($costoHoraClase, 2, ".", ","), ["id" => "costo-hora-clase-pago", "class" => "form-control", "maxlength" =>"19"]) }}
          </div>
        </div> 
      </div>
      <div class="form-group">
        {{ Form::label("fechaInicioClases", "Inicio de clases (*): ", ["class" => "col-sm-2 control-label"]) }}
        <div class="col-sm-3">
          <div class="input-group date">
            <div class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </div>                                
            {{ Form::text("fechaInicioClases", ((((int) $numeroPeriodos) +1) == 1 ? \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $fechaInicioClase)->format("d/m/Y") : NULL), ["id" => "fecha-inicio-clases-pago", "class" => "form-control", "placeholder" => "dd/mm/aaaa"]) }}
          </div>
        </div>

        {{ Form::label("periodoClases", "Período (*): ", ["class" => "col-sm-2 control-label"]) }}
        <div class="col-sm-2">
          {{ Form::number("periodoClases", (((int) $numeroPeriodos) +1), ["id" => "periodo-clases-pago", "class" => "form-control", "maxlength" =>"11", "min" =>"1"]) }}
        </div>
      </div>
    </div>
    <div class="form-group">
      <div class="col-sm-12">
        <br/><span>(*) Campos obligatorios</span>
      </div>
    </div>
  </div>                
  <div id="sec-pago-22">
    <div class="form-group">
      <div class="col-sm-12"><br/>
        <span>En base a los datos establecidos y el horario del alumno se ha determinado las fechas de las clases del período <b><span id="txt-periodo">{{ (((int) $numeroPeriodos) +1) }}</span></b>.
      </div>                                        
    </div>   
    <div id="sec-lista-clases-pago" class="form-group">
      <div class="col-sm-12">
        <table class="table table-bordered sub-table">
          <thead>
            <tr>
              <th>N°</th>
              <th>Fecha</th>
              <th>Horas</th>
              <th class="text-center">Notificar<br/><small>(Un día antes)</small></th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
      <div id="sec-saldo-favor-pago" class="col-sm-12"></div>
    </div> 
    <div class="form-group">
      <div class="col-sm-12">
        <button id="btn-docentes-disponibles-pago" type="button" class="btn btn-primary btn-sm">Elegir profesor disponible</button> 
      </div>
    </div>
    <div id="sec-pago-221">
      <div class="form-group">
        {{ Form::label("", "", ["id" => "nombre-docente-pago", "class" => "col-sm-4 control-label"]) }}
      </div>
      <div class="form-group">
        {{ Form::label("costoHoraDocente", "Pago por hora al profesor (*): ", ["class" => "col-sm-3 control-label"]) }}
        <div class="col-sm-4">
          <div class="input-group">
            <span class="input-group-addon">
              <b>S/.</b>
            </span>
            {{ Form::text("costoHoraDocente", NULL, ["id" => "costo-hora-docente-pago", "class" => "form-control", "maxlength" =>"19"]) }}
          </div>
        </div>
      </div>
      <div class="form-group">
        <div class="col-sm-12">
          <br/><span>(*) Campos obligatorios</span>
        </div>
      </div>
    </div>
    {{ Form::hidden("idDocente") }} 
    {{ Form::hidden("saldoFavor") }} 
    {{ Form::hidden("datosNotificacionClases") }} 
  </div>
</div> 
<div class="box-footer">    
  <button id="btn-cancelar-pago" type="button" class="btn btn-default">Cancelar</button>
  <button id="btn-generar-clases-pago" type="button" class="btn btn-primary pull-right">Generar clases</button>
  <button id="btn-registrar-pago" type="submit" class="btn btn-success pull-right">Registrar pago</button>
  <button id="btn-anterior-pago"  type="button" class="btn btn-primary pull-right">Anterior</button>
</div> 