<script src="{{ asset("assets/eah/js/modulos/util/filtrosBusquedaFechas.js")}}"></script>
<div id="sec-filtro-busqueda-fechas-{{ $idSeccion }}" class="form-group">      
  {{ Form::label("tipoBusquedaFecha", "Fecha: ", ["class" => "col-sm-2 control-label"]) }} 
  <div class="col-sm-3">
    {{ Form::select('tipoBusquedaFecha', App\Helpers\Enum\TiposBusquedaFecha::listar(), (isset($tipoBusquedaDefecto) ? $tipoBusquedaDefecto : App\Helpers\Enum\TiposBusquedaFecha::Mes), ["class" => "form-control"]) }}
  </div>
  <div id="sec-filtro-busqueda-fechas-{{ App\Helpers\Enum\TiposBusquedaFecha::Dia }}" style="display: none">
    <div class="col-sm-3">            
      <div class="input-group date">
        <div class="input-group-addon">
          <i class="fa fa-calendar"></i>
        </div>  
        {{ Form::text("fechaDia", \Carbon\Carbon::now()->format("d/m/Y"), ["class" => "form-control  pull-right", "placeholder" => "dd/mm/aaaa"]) }}
      </div>
    </div>
  </div>
  <div id="sec-filtro-busqueda-fechas-{{ App\Helpers\Enum\TiposBusquedaFecha::Mes }}" style="display: none">
    <div class="col-sm-3">            
      <div class="input-group date">
        <div class="input-group-addon">
          <i class="fa fa-calendar"></i>
        </div>  
        {{ Form::text("fechaMes", \Carbon\Carbon::now()->format("m/Y"), ["class" => "form-control  pull-right", "placeholder" => "mm/aaaa"]) }}
      </div>
    </div>
  </div>
  <div id="sec-filtro-busqueda-fechas-{{ App\Helpers\Enum\TiposBusquedaFecha::Anio }}" style="display: none">
    <div class="col-sm-3">            
      <div class="input-group date">
        <div class="input-group-addon">
          <i class="fa fa-calendar"></i>
        </div>  
        {{ Form::text("fechaAnio", \Carbon\Carbon::now()->format("Y"), ["class" => "form-control  pull-right", "placeholder" => "aaaa"]) }}
      </div>
    </div>
  </div>
  <div id="sec-filtro-busqueda-fechas-{{ App\Helpers\Enum\TiposBusquedaFecha::RangoDias }}" style="display: none">
    <div class="col-sm-3">
      <div class="input-group date">
        <div class="input-group-addon">
          <i class="fa fa-calendar"></i>
        </div>  
        {{ Form::text("fechaDiaInicio", \Carbon\Carbon::now()->format("d/m/Y"), ["class" => "form-control  pull-right", "placeholder" => "dd/mm/aaaa"]) }}
      </div>
    </div>
    <div class="col-sm-3">
      <div class="input-group date">
        <div class="input-group-addon">
          <i class="fa fa-calendar"></i>
        </div>  
        {{ Form::text("fechaDiaFin", \Carbon\Carbon::now()->format("d/m/Y"), ["class" => "form-control  pull-right", "placeholder" => "dd/mm/aaaa"]) }}
      </div>
    </div>
  </div>
  <div id="sec-filtro-busqueda-fechas-{{ App\Helpers\Enum\TiposBusquedaFecha::RangoMeses }}" style="display: none">
    <div class="col-sm-3">
      <div class="input-group date">
        <div class="input-group-addon">
          <i class="fa fa-calendar"></i>
        </div>  
        {{ Form::text("fechaMesInicio", \Carbon\Carbon::now()->format("m/Y"), ["class" => "form-control  pull-right", "placeholder" => "mm/aaaa"]) }}
      </div>
    </div>
    <div class="col-sm-3">
      <div class="input-group date">
        <div class="input-group-addon">
          <i class="fa fa-calendar"></i>
        </div>  
        {{ Form::text("fechaMesFin", \Carbon\Carbon::now()->format("m/Y"), ["class" => "form-control  pull-right", "placeholder" => "mm/aaaa"]) }}
      </div>
    </div>
  </div>
  <div id="sec-filtro-busqueda-fechas-{{ App\Helpers\Enum\TiposBusquedaFecha::RangoAnios }}" style="display: none">
    <div class="col-sm-3">
      <div class="input-group date">
        <div class="input-group-addon">
          <i class="fa fa-calendar"></i>
        </div>  
        {{ Form::text("fechaAnioInicio", \Carbon\Carbon::now()->format("Y"), ["class" => "form-control  pull-right", "placeholder" => "aaaa"]) }}
      </div>
    </div>
    <div class="col-sm-3">
      <div class="input-group date">
        <div class="input-group-addon">
          <i class="fa fa-calendar"></i>
        </div>  
        {{ Form::text("fechaAnioFin", \Carbon\Carbon::now()->format("Y"), ["class" => "form-control  pull-right", "placeholder" => "aaaa"]) }}
      </div>
    </div>
  </div>
</div> 