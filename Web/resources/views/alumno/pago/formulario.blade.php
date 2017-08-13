<div class="box-header">
  <h3 class="box-title with-border">Nuevo pago</h3>                
</div>  
<div class="box-body">
  <div id="sec-pago-21">
    <div class="form-group">
      {{ Form::label("motivo", "Motivo: ", ["class" => "col-sm-2 control-label"]) }}
      <div class="col-sm-3">
        {{ Form::select("motivo", App\Helpers\Enum\MotivosPago::listar(), null, ["id" => "motivo-pago", "class" => "form-control"]) }}
      </div>          
      {{ Form::label("cuenta", "Cuenta: ", ["class" => "col-sm-2 control-label"]) }}
      <div class="col-sm-3">
        {{ Form::select("cuenta", App\Helpers\Enum\CuentasBancoPago::listar(), null, ["class" => "form-control"]) }}
      </div>
    </div> 
    <div class="form-group">      
      {{ Form::label("fecha-pago", "Fecha de pago (*): ", ["class" => "col-sm-2 control-label"]) }}
      <div class="col-sm-3">
        <div class="input-group date">
          <div class="input-group-addon">
            <i class="fa fa-calendar"></i>
          </div>                                
          {{ Form::text("fecha", null, ["id" => "fecha-pago", "class" => "form-control", "placeholder" => "dd/mm/aaaa"]) }}
        </div>
      </div>
      {{ Form::label("estado", "Estado: ", ["class" => "col-sm-2 control-label"]) }}
      <div class="col-sm-3">
        {{ Form::select("estado", App\Helpers\Enum\EstadosPago::listarCambio(), null, ["class" => "form-control"]) }}
      </div>   
    </div>
    <div class="form-group">
      {{ Form::label("descripcion", "Descripción: ", ["class" => "col-sm-2 control-label"]) }}
      <div class="col-sm-10">
        {{ Form::text("descripcion", null, ["class" => "form-control", "maxlength" =>"255"]) }}
      </div>
    </div>
    <div class="form-group">
      {{ Form::label("imagenComprobante", "Imagen de comprobante: ", ["class" => "col-sm-2 control-label"]) }}
      <div class="col-sm-4">
        {{ Form::file("imagenComprobante", null) }}
      </div>
    </div> 
    <div class="form-group">
      {{ Form::label("monto", "Monto total (*): ", ["class" => "col-sm-2 control-label"]) }}
      <div class="col-sm-3">
        <div class="input-group">
          <span class="input-group-addon">
            <b>S/.</b>
          </span>
          {{ Form::text("monto", null, ["class" => "monto-pago form-control", "maxlength" =>"19", "data-modo" =>"registrar"]) }}
        </div>
      </div>
      <div id="sec-saldo-favor" class="col-sm-7" style="display: none">
        <div class="checkbox">
          <label class="checkbox-custom" data-initialize="checkbox">
            {{ Form::label("usarSaldoFavor", "", ["id" => "lbl-usar-saldo-favor", "class" => "checkbox-label"]) }}
            {{ Form::checkbox("usarSaldoFavor", null, FALSE, ["class" => "usar-saldo-favor", "data-modo" =>"registrar"]) }}
          </label>
        </div>
      </div> 
    </div>                     
    <div id="sec-pago-211">
      <div class="form-group">  
        {{ Form::label("costo-hora-clase-pago", "Costo por hora (*): ", ["class" => "col-sm-2 control-label"]) }}   
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
        {{ Form::label("fecha-inicio-clases-pago", "Inicio de clases (*): ", ["class" => "col-sm-2 control-label"]) }}
        <div class="col-sm-3">
          <div class="input-group date">
            <div class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </div>                                
            {{ Form::text("fechaInicioClases", ((((int) $numeroPeriodos) +1) == 1 && !empty($fechaInicioClase) ? \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $fechaInicioClase)->format("d/m/Y") : null), ["id" => "fecha-inicio-clases-pago", "class" => "form-control", "placeholder" => "dd/mm/aaaa"]) }}
          </div>
        </div>

        {{ Form::label("periodo-clases-pago", "Período (*): ", ["class" => "col-sm-2 control-label"]) }}
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
        {{ Form::label("costo-hora-docente-pago", "Pago por hora al profesor (*): ", ["class" => "col-sm-3 control-label"]) }}
        <div class="col-sm-4">
          <div class="input-group">
            <span class="input-group-addon">
              <b>S/.</b>
            </span>
            {{ Form::text("costoHoraDocente", null, ["id" => "costo-hora-docente-pago", "class" => "form-control", "maxlength" =>"19"]) }}
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
    {{ Form::hidden("saldoFavorAdicional") }} 
    {{ Form::hidden("datosNotificacionClases") }} 
    {{ Form::hidden("registrarSinGenerarClases", null) }}
  </div>
</div> 
<div class="box-footer">    
  <button type="button" class="btn-cancelar-pago btn btn-default">Cancelar</button>
  <button id="btn-registrar-sin-generar-clases-pago" type="button" class="btn btn-success pull-right">Registrar pago sin generar clases</button>
  <button id="btn-generar-clases-pago" type="button" class="btn btn-primary pull-right">Generar clases</button>
  <button id="btn-registrar-pago" type="submit" class="btn btn-success pull-right">Registrar pago</button>
  <button id="btn-anterior-pago"  type="button" class="btn btn-primary pull-right">Anterior</button>
</div> 