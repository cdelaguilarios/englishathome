<div class="row">
  <div class="col-sm-12">
    <div id="sec-men-calendario"></div>
    <div>
      <div class="box-header">
        <h3 class="box-title">Filtros de búsquedad</h3> 
      </div>         
      <div class="box-body form-horizontal">
        <div class="form-group">          
          {{ Form::label("bus-fecha-calendario", "Fecha: ", ["class" => "col-sm-1 control-label"]) }}
          <div class="col-sm-3">            
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>  
              {{ Form::text("", null, ["id" => "bus-fecha-calendario", "class" => "form-control  pull-right", "placeholder" => "dd/mm/aaaa"]) }}
            </div>
          </div>
        </div>
      </div>  
    </div>        
  </div>
</div>
<div class="row">
  <div class="col-sm-12">
    <div id="sec-calendario"></div> 
  </div>
</div>
<script>
  var urlCalendario = "{{ route('calendario', ['idEntidad' => $idEntidad]) }}";
</script>
<script src="{{ asset("assets/eah/js/calendario.js")}}"></script>
