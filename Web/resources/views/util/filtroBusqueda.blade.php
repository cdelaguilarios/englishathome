<div class="row">
  <div class="col-sm-12">
    <div class="box box-info">
      <div class="box-header">
        <h3 class="box-title">Filtros de búsquedad</h3> 
      </div>         
      <div class="box-body form-horizontal">
        <div class="form-group">          
          {{ Form::label("bus-estado", "Estado: ", ["class" => "col-sm-2 control-label"]) }}
          <div class="col-sm-3">
            {{ Form::select("estado", $estados, NULL, ["id"=>"bus-estado", "class" => "form-control", "placeholder" => "Todos"]) }}
          </div>
        </div> 
        @if(isset($incluirTipoPago) && $incluirTipoPago == 1)
        <div class="form-group">          
          {{ Form::label("bus-tipo-pago", "Tipo de pago: ", ["class" => "col-sm-2 control-label"]) }}
          <div class="col-sm-3">
            {{ Form::select("tipoPago", ["0" => "Pago de alumnos", "1" => "Pago a profesores"], NULL, ["id"=>"bus-tipo-pago", "class" => "form-control"]) }}
          </div>
        </div>
        @endif
        <div class="form-group">      
          {{ Form::label("bus-tipo-fecha", "Fecha: ", ["class" => "col-sm-2 control-label"]) }} 
          <div class="col-sm-3">
            {{ Form::select('tipoFecha', App\Helpers\Enum\TiposBusquedaFecha::listar((isset($seccionReporte) ? $seccionReporte : 0)), App\Helpers\Enum\TiposBusquedaFecha::Dia, ["id" => "bus-tipo-fecha", "class" => "form-control"]) }}
          </div>
          @if(!(isset($seccionReporte) && $seccionReporte == 1))
          <div id="sec-bus-fecha-{{ App\Helpers\Enum\TiposBusquedaFecha::Dia }}">
            <div class="col-sm-3">            
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>  
                {{ Form::text("fechaDia", \Carbon\Carbon::now()->format("d/m/Y"), ["id" => "bus-fecha-dia", "class" => "form-control  pull-right", "placeholder" => "dd/mm/aaaa"]) }}
              </div>
            </div>
          </div>
          @endif
          <div id="sec-bus-fecha-{{ App\Helpers\Enum\TiposBusquedaFecha::Mes }}" style="display: none;">
            <div class="col-sm-3">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>  
                {{ Form::text("fechaMesInicio", \Carbon\Carbon::now()->format("m/Y"), ["id" => "bus-fecha-mes-inicio", "class" => "form-control  pull-right", "placeholder" => "mm/aaaa"]) }}
              </div>
            </div>
            @if(isset($seccionReporte) && $seccionReporte == 1)
            <div class="col-sm-3">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>  
                {{ Form::text("fechaMesFin", \Carbon\Carbon::now()->format("m/Y"), ["id" => "bus-fecha-mes-fin", "class" => "form-control  pull-right", "placeholder" => "mm/aaaa"]) }}
              </div>
            </div>
            @endif
          </div>
          <div id="sec-bus-fecha-{{ App\Helpers\Enum\TiposBusquedaFecha::Anho }}" style="display: none;">
            <div class="col-sm-3">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>  
                {{ Form::text("fechaAnhoInicio", \Carbon\Carbon::now()->format("Y"), ["id" => "bus-fecha-anho-inicio", "class" => "form-control  pull-right", "placeholder" => "aaaa"]) }}
              </div>
            </div>
            @if(isset($seccionReporte) && $seccionReporte == 1)
            <div class="col-sm-3">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>  
                {{ Form::text("fechaAnhoFin", \Carbon\Carbon::now()->format("Y"), ["id" => "bus-fecha-anho-fin", "class" => "form-control  pull-right", "placeholder" => "aaaa"]) }}
              </div>
            </div>
            @endif
          </div>
          <div id="sec-bus-fecha-{{ App\Helpers\Enum\TiposBusquedaFecha::RangoFecha }}" style="display: none;">
            <div class="col-sm-3">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>  
                {{ Form::text("fechaInicio", \Carbon\Carbon::now()->format("d/m/Y"), ["id" => "bus-fecha-inicio", "class" => "form-control  pull-right", "placeholder" => "dd/mm/aaaa"]) }}
              </div>
            </div>
            <div class="col-sm-3">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>  
                {{ Form::text("fechain", \Carbon\Carbon::now()->format("d/m/Y"), ["id" => "bus-fecha-fin", "class" => "form-control  pull-right", "placeholder" => "dd/mm/aaaa"]) }}
              </div>
            </div>
          </div>
        </div> 
      </div>
    </div>
  </div>
</div>