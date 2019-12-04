{{----}}
<script src="{{ asset("assets/eah/js/modulos/util/filtrosBusquedaFechas.js")}}"></script>
<div class="form-group">      
  {{ Form::label("filtro-busqueda-fechas-tipo", "Fecha: ", ["id" => "lbl-filtro-busqueda-fechas-tipo", "class" => "col-sm-2 control-label"]) }} 
  <div class="col-sm-3">
    {{ Form::select('tipoBusquedaFecha', App\Helpers\Enum\TiposBusquedaFecha::listar(), App\Helpers\Enum\TiposBusquedaFecha::Mes, ["id" => "filtro-busqueda-fechas-tipo", "class" => "form-control"]) }}
  </div>
  <div id="sec-filtro-busqueda-fechas-{{ App\Helpers\Enum\TiposBusquedaFecha::Dia }}" style="display: none">
    <div class="col-sm-3">            
      <div class="input-group date">
        <div class="input-group-addon">
          <i class="fa fa-calendar"></i>
        </div>  
        {{ Form::text("fechaDia", \Carbon\Carbon::now()->format("d/m/Y"), ["id" => "filtro-busqueda-fechas-dia", "class" => "form-control  pull-right", "placeholder" => "dd/mm/aaaa"]) }}
      </div>
    </div>
  </div>
  <div id="sec-filtro-busqueda-fechas-{{ App\Helpers\Enum\TiposBusquedaFecha::Mes }}">
    <div class="col-sm-3">            
      <div class="input-group date">
        <div class="input-group-addon">
          <i class="fa fa-calendar"></i>
        </div>  
        {{ Form::text("fechaMes", \Carbon\Carbon::now()->format("m/Y"), ["id" => "filtro-busqueda-fechas-mes", "class" => "form-control  pull-right", "placeholder" => "mm/aaaa"]) }}
      </div>
    </div>
  </div>
  <div id="sec-filtro-busqueda-fechas-{{ App\Helpers\Enum\TiposBusquedaFecha::Anio }}" style="display: none">
    <div class="col-sm-3">            
      <div class="input-group date">
        <div class="input-group-addon">
          <i class="fa fa-calendar"></i>
        </div>  
        {{ Form::text("fechaAnio", \Carbon\Carbon::now()->format("Y"), ["id" => "filtro-busqueda-fechas-anio", "class" => "form-control  pull-right", "placeholder" => "aaaa"]) }}
      </div>
    </div>
  </div>
  <div id="sec-filtro-busqueda-fechas-{{ App\Helpers\Enum\TiposBusquedaFecha::RangoDias }}" style="display: none">
    <div class="col-sm-3">
      <div class="input-group date">
        <div class="input-group-addon">
          <i class="fa fa-calendar"></i>
        </div>  
        {{ Form::text("fechaDiaInicio", \Carbon\Carbon::now()->format("d/m/Y"), ["id" => "filtro-busqueda-fechas-dia-inicio", "class" => "form-control  pull-right", "placeholder" => "dd/mm/aaaa"]) }}
      </div>
    </div>
    <div class="col-sm-3">
      <div class="input-group date">
        <div class="input-group-addon">
          <i class="fa fa-calendar"></i>
        </div>  
        {{ Form::text("fechaDiaFin", \Carbon\Carbon::now()->format("d/m/Y"), ["id" => "filtro-busqueda-fechas-dia-fin", "class" => "form-control  pull-right", "placeholder" => "dd/mm/aaaa"]) }}
      </div>
    </div>
  </div>
  <div id="sec-filtro-busqueda-fechas-{{ App\Helpers\Enum\TiposBusquedaFecha::RangoMeses }}" style="display: none">
    <div class="col-sm-3">
      <div class="input-group date">
        <div class="input-group-addon">
          <i class="fa fa-calendar"></i>
        </div>  
        {{ Form::text("fechaMesInicio", \Carbon\Carbon::now()->format("m/Y"), ["id" => "filtro-busqueda-fechas-mes-inicio", "class" => "form-control  pull-right", "placeholder" => "mm/aaaa"]) }}
      </div>
    </div>
    <div class="col-sm-3">
      <div class="input-group date">
        <div class="input-group-addon">
          <i class="fa fa-calendar"></i>
        </div>  
        {{ Form::text("fechaMesFin", \Carbon\Carbon::now()->format("m/Y"), ["id" => "filtro-busqueda-fechas-mes-fin", "class" => "form-control  pull-right", "placeholder" => "mm/aaaa"]) }}
      </div>
    </div>
  </div>
  <div id="sec-filtro-busqueda-fechas-{{ App\Helpers\Enum\TiposBusquedaFecha::RangoAnios }}" style="display: none">
    <div class="col-sm-3">
      <div class="input-group date">
        <div class="input-group-addon">
          <i class="fa fa-calendar"></i>
        </div>  
        {{ Form::text("fechaAnioInicio", \Carbon\Carbon::now()->format("Y"), ["id" => "filtro-busqueda-fechas-anio-inicio", "class" => "form-control  pull-right", "placeholder" => "aaaa"]) }}
      </div>
    </div>
    <div class="col-sm-3">
      <div class="input-group date">
        <div class="input-group-addon">
          <i class="fa fa-calendar"></i>
        </div>  
        {{ Form::text("fechaAnioFin", \Carbon\Carbon::now()->format("Y"), ["id" => "filtro-busqueda-fechas-anio-fin", "class" => "form-control  pull-right", "placeholder" => "aaaa"]) }}
      </div>
    </div>
  </div>
</div> 