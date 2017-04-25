<div class="row">
  <div class="col-sm-12">
    <div class="{{ (isset($incluirClaseBox) ? "box box-info" : "") }}">
      <div id="sec-men-calendario"></div>
      <div class="box-header">
        <h3 class="box-title">Filtros de búsquedad</h3> 
      </div>         
      <div class="box-body form-horizontal">
        <div class="form-group">          
          {{ Form::label("bus-fecha-calendario", "Fecha: ", ["class" => "col-sm-2 control-label"]) }}
          <div class="col-sm-3">            
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>  
              {{ Form::text("", null, ["id" => "bus-fecha-calendario", "class" => "form-control  pull-right", "placeholder" => "dd/mm/aaaa"]) }}
            </div>
          </div>
        </div>
        @if(isset($incluirListasBusqueda) && $incluirListasBusqueda == 1)
        <div class="form-group">          
          {{ Form::label("bus-tipo-filtro-calendario", "Tipo de filtro: ", ["class" => "col-sm-2 control-label"]) }}
          <div class="col-sm-3">
            {{ Form::select("", ["0" => "Filtro por entidad", "1" => "Todas las clases"], null, ["id"=>"bus-tipo-filtro-calendario", "class" => "form-control"]) }}
          </div>
        </div>
        <div id="sec-filtro-entidad-calendario" class="form-group">          
          {{ Form::label("bus-tipo-entidad-calendario", "Entidad: ", ["class" => "col-sm-2 control-label"]) }}
          <div class="col-sm-3">
            {{ Form::select("", ["0" => "Alumno", "1" => "Profesor"], null, ["id"=>"bus-tipo-entidad-calendario", "class" => "form-control"]) }}
          </div>        
          <div id="sec-bus-sel-alumno-calendario" class="col-sm-4">
            {{ Form::select("",App\Models\Alumno::listarBusqueda(), null, ["id"=>"bus-sel-alumno-calendario", "class" => "form-control", "style" => "width: 100%"]) }}
          </div>        
          <div id="sec-bus-sel-profesor-calendario" class="col-sm-4" style="display: none">
            {{ Form::select("",App\Models\Profesor::listarBusqueda(), null, ["id"=>"bus-sel-profesor-calendario", "class" => "form-control", "style" => "width: 100%"]) }}
          </div>
        </div>
        @else
        @if(isset($esEntidadProfesor) && $esEntidadProfesor == 1)
        {{ Form::hidden("", 1, ["id"=>"bus-tipo-entidad-calendario"]) }}
        {{ Form::hidden("", $idEntidad, ["id"=>"bus-sel-profesor-calendario"]) }}
        @else
        {{ Form::hidden("", 0, ["id"=>"bus-tipo-entidad-calendario"]) }}
        {{ Form::hidden("", $idEntidad, ["id"=>"bus-sel-alumno-calendario"]) }}
        @endif
        @endif
      </div>  
    </div>        
  </div>
</div>
<div class="row">
  <div class="col-sm-12">
    <div class="{{ (isset($incluirClaseBox) ? "box box-info" : "") }}">
      <div id="sec-calendario"></div> 
    </div>
  </div>
</div>
@include("util.datosClase") 
<script>
  var urlCalendario = "{{ route('calendario.datos') }}";
</script>
<script src="{{ asset("assets/eah/js/calendario.js")}}"></script>
