<script src="{{ asset("assets/eah/js/modulos/filtrosBusqueda.js")}}"></script>
<div id="{{ (isset($idSeccion) ? $idSeccion : "") }}"class="row">
  <div class="col-sm-12">
    <div class="{{ (isset($incluirClaseBox) ? "box box-info" : "") }}">
      <div class="box-header">
        <h3 class="box-title">Filtros de búsquedad</h3> 
      </div>         
      <div class="box-body form-horizontal">
        @if(isset($incluirEstadosClase) && $incluirEstadosClase == 1)
        <div class="form-group">          
          {{ Form::label("bus-estado-clase", "Estado de la clase: ", ["class" => "col-sm-2 control-label"]) }}
          <div class="col-sm-3">
            {{ Form::select("estadoClase", App\Helpers\Enum\EstadosClase::listarBusqueda(), null, ["id"=>"bus-estado-clase", "class" => "form-control", "placeholder" => "Todos"]) }}
          </div>
        </div>
        @endif 
        @if(isset($incluirEstadosPago) && $incluirEstadosPago == 1)
        <div class="form-group">          
          {{ Form::label("bus-estado-pago", "Estado del pago: ", ["class" => "col-sm-2 control-label"]) }}
          <div class="col-sm-3">
            {{ Form::select("estadoPago", App\Helpers\Enum\EstadosPago::listarSimple(), null, ["id"=>"bus-estado-pago", "class" => "form-control", "placeholder" => "Todos"]) }}
          </div>
        </div>
        @endif
        @if(isset($incluirTipoPago) && $incluirTipoPago == 1)
        <div class="form-group">          
          {{ Form::label("bus-tipo-pago", "Tipo de pago: ", ["class" => "col-sm-2 control-label"]) }}
          <div class="col-sm-3">
            {{ Form::select("tipoPago", ["0" => "Pago de alumnos", "1" => "Pago a profesores"], null, ["id"=>"bus-tipo-pago", "class" => "form-control"]) }}
          </div>
        </div>
        @endif
        <div class="form-group">      
          {{ Form::label("bus-tipo-fecha", "Fecha: ", ["class" => "col-sm-2 control-label"]) }} 
          <div class="col-sm-3">
            {{ Form::select('tipoFecha', App\Helpers\Enum\TiposBusquedaFecha::listar(), App\Helpers\Enum\TiposBusquedaFecha::Mes, ["id" => "bus-tipo-fecha", "class" => "form-control"]) }}
          </div>
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
          <div id="sec-bus-fecha-{{ App\Helpers\Enum\TiposBusquedaFecha::Mes }}" style="display: none;">
            <div class="col-sm-3">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>  
                {{ Form::text("fechaMesInicio", \Carbon\Carbon::now()->format("m/Y"), ["id" => "bus-fecha-mes-inicio", "class" => "form-control  pull-right", "placeholder" => "mm/aaaa"]) }}
              </div>
            </div>
            <div class="col-sm-3">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>  
                {{ Form::text("fechaMesFin", \Carbon\Carbon::now()->format("m/Y"), ["id" => "bus-fecha-mes-fin", "class" => "form-control  pull-right", "placeholder" => "mm/aaaa"]) }}
              </div>
            </div>
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
            <div class="col-sm-3">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>  
                {{ Form::text("fechaAnhoFin", \Carbon\Carbon::now()->format("Y"), ["id" => "bus-fecha-anho-fin", "class" => "form-control  pull-right", "placeholder" => "aaaa"]) }}
              </div>
            </div>
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