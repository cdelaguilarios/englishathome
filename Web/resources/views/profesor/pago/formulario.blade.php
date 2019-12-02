{{----}}
<div class="box-header">
  <h3 class="box-title with-border">{{ isset($seccionActualizar) ? "Editar" : "Nuevo" }} pago</h3>                
</div>  
<div class="box-body">
  <div class="form-group">
    {{ Form::label("", "Motivo: ", ["class" => "col-sm-2 control-label"]) }}
    <div class="col-sm-3">
      {{ Form::text("motivo", App\Helpers\Enum\MotivosPago::listar()[App\Helpers\Enum\MotivosPago::Otros], ["class" => "form-control", "disabled" =>""]) }}
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
    {{ Form::hidden("idPago") }}
    {{ Form::hidden("seccionActualizar", isset($seccionActualizar) ? 1 : 0) }}
  </div>        
  <div class="form-group">
    <div class="col-sm-12">
      <br/><span>(*) Campos obligatorios</span>
    </div>
  </div>      
  <div class="form-group">
    <div class="col-sm-12">
      <span>
        <b>Nota: </b> los pagos por clases se pueden registrar en la sección <a href="{{ route("docentes.pagosXClases")}}" target="_blank">Docentes/Pagos por clases</a>
      </span>
    </div>
  </div>        
</div> 
<div class="box-footer">    
  <button id="btn-cancelar-pago-{{ $idSeccion }}" type="button" class="btn btn-default">Cancelar</button>
  <button id="btn-registrar-pago-{{ $idSeccion }}" type="submit" class="btn btn-success pull-right">{{ isset($seccionActualizar) ? "Actualizar" : "Registrar" }} pago</button>
</div> 