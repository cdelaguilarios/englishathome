{{----}}
<div class="box-header">
  <h3 class="box-title with-border">{{ isset($seccionActualizar) ? "Editar" : "Nuevo" }} pago</h3>                
</div>  
<div class="box-body">
  <div class="form-group">
    {{ Form::label("motivo-pago-" . $idSeccion, "Motivo: ", ["class" => "col-sm-2 control-label"]) }}
    <div class="col-sm-3">
      {{ Form::select("motivo", App\Helpers\Enum\MotivosPago::listar(), null, ["id" => "motivo-pago-" . $idSeccion, "class" => "form-control"]) }}
    </div>          
    {{ Form::label("cuenta-pago-" . $idSeccion, "Cuenta: ", ["class" => "col-sm-2 control-label"]) }}
    <div class="col-sm-3">
      {{ Form::select("cuenta", App\Helpers\Enum\CuentasBancoPago::listar(), null, ["id" => "cuenta-pago-" . $idSeccion, "class" => "form-control"]) }}
    </div>
  </div> 
  <div class="form-group">      
    {{ Form::label("fecha-pago-" . $idSeccion, "Fecha de pago (*): ", ["class" => "col-sm-2 control-label"]) }}
    <div class="col-sm-3">
      <div class="input-group date">
        <div class="input-group-addon">
          <i class="fa fa-calendar"></i>
        </div>                                
        {{ Form::text("fecha", null, ["id" => "fecha-pago-" . $idSeccion, "class" => "form-control", "placeholder" => "dd/mm/aaaa"]) }}
      </div>
    </div>
    {{ Form::label("estado-pago-" . $idSeccion, "Estado: ", ["class" => "col-sm-2 control-label"]) }}
    <div class="col-sm-3">
      {{ Form::select("estado", App\Helpers\Enum\EstadosPago::listarDisponibleCambio(), null, ["id" => "estado-pago-" . $idSeccion, "class" => "form-control"]) }}
    </div>   
  </div>
  <div class="form-group">
    {{ Form::label("descripcion-pago-" . $idSeccion, "Descripción: ", ["class" => "col-sm-2 control-label"]) }}
    <div class="col-sm-10">
      {{ Form::text("descripcion", null, ["id" => "descripcion-pago-" . $idSeccion, "class" => "form-control", "maxlength" =>"255"]) }}
    </div>
  </div>
  <div class="form-group">
    {{ Form::label("imagen-comprobante-" . $idSeccion, "Imagen de comprobante: ", ["class" => "col-sm-2 control-label"]) }}
    <div class="col-sm-4">
      {{ Form::file("imagenComprobante", ["id" => "imagen-comprobante-" . $idSeccion]) }}
    </div>
    <div id="sec-pago-imagen-comprobante-actual-{{ $idSeccion }}" class="col-sm-3">
      <a href="{{ route("archivos", ["nombre" => "0"]) }}" target="_blank">
        <img src="{{ route("archivos", ["nombre" => "0"]) }}" width="40"/>
      </a>
    </div>
  </div> 
  <div class="form-group">
    {{ Form::label("monto-pago-" . $idSeccion, "Monto total (*): ", ["class" => "col-sm-2 control-label"]) }}
    <div class="col-sm-3">
      <div class="input-group">
        <span class="input-group-addon">
          <b>S/.</b>
        </span>
        {{ Form::text("monto", null, ["id" => "monto-pago-" . $idSeccion, "class" => "form-control", "maxlength" =>"19"]) }}
      </div>
    </div>
    <div id="sec-pago-saldo-favor-{{ $idSeccion }}" class="col-sm-7" style="display: none">
      <div class="checkbox">
        <label class="checkbox-custom" data-initialize="checkbox">
          {{ Form::label("usar-saldo-favor-pago-" . $idSeccion, "", ["id" => "lbl-usar-saldo-favor-pago-" . $idSeccion, "class" => "checkbox-label"]) }}
          {{ Form::checkbox("usarSaldoFavor", null, FALSE, ["id" => "usar-saldo-favor-pago-" . $idSeccion]) }}
        </label>
      </div>
    </div> 
  </div>                     
  <div id="sec-pago-datos-clases-{{ $idSeccion }}">
    <div class="form-group">  
      {{ Form::label("costo-x-hora-clase-pago-" . $idSeccion, "Costo por hora de clase (*): ", ["class" => "col-sm-2 control-label"]) }}   
      <div class="col-sm-3">
        <div class="input-group">
          <span class="input-group-addon">
            <b>S/.</b>
          </span>
          {{ Form::text("costoXHoraClase", number_format($alumno->costoXHoraClase, 2, ".", ","), ["id" => "costo-x-hora-clase-pago-" . $idSeccion, "class" => "form-control", "maxlength" =>"19"]) }}
        </div>
      </div>
      {{ Form::label("periodo-clases-pago-" . $idSeccion, "Período de clases (*): ", ["class" => "col-sm-2 control-label"]) }}
      <div class="col-sm-2">
        {{ Form::number("periodoClases", (((int) $alumno->numeroPeriodos) +1), ["id" => "periodo-clases-pago-" . $idSeccion, "class" => "form-control", "maxlength" =>"11", "min" =>"1"]) }}
      </div> 
    </div>
    <div class="form-group">
      <div class="col-sm-12">
        <button id="btn-cargar-docentes-disponibles-pago-{{ $idSeccion }}" type="button" class="btn btn-primary btn-sm">Elegir profesor disponible</button> 
      </div>
    </div>
    <div id="sec-pago-datos-docente-{{ $idSeccion }}">
      <div class="form-group">
        {{ Form::label("", "", ["id" => "nombre-docente-pago-" . $idSeccion, "class" => "col-sm-4 control-label"]) }}
      </div>
      <div class="form-group">
        {{ Form::label("pago-x-hora-docente-" . $idSeccion, "Pago por hora al profesor (*): ", ["class" => "col-sm-3 control-label"]) }}
        <div class="col-sm-4">
          <div class="input-group">
            <span class="input-group-addon">
              <b>S/.</b>
            </span>
            {{ Form::text("pagoXHoraProfesor", null, ["id" => "pago-x-hora-docente-" . $idSeccion, "class" => "form-control", "maxlength" =>"19"]) }}
          </div>
        </div>
      </div>
    </div>
    {{ Form::hidden("idPago") }}
    {{ Form::hidden("idDocente") }}
    {{ Form::hidden("seccionActualizar", isset($seccionActualizar) ? 1 : 0) }}
  </div>
  <div class="form-group">
    <div class="col-sm-12">
      <br/><span>(*) Campos obligatorios</span>
    </div>
  </div>  
</div> 
<div class="box-footer">    
  <button id="btn-cancelar-pago-{{ $idSeccion }}" type="button" class="btn btn-default">Cancelar</button>
  <button id="btn-registrar-pago-{{ $idSeccion }}" type="submit" class="btn btn-success pull-right">{{ isset($seccionActualizar) ? "Actualizar" : "Registrar" }} pago</button>
</div> 