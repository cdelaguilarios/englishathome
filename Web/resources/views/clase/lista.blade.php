@extends("layouts.master")
@section("titulo", "Clases")

@section("section_script")
<script>
  var urlListar = "{{ route('clases.listar') }}";
  var estados = {!!  json_encode(App\Helpers\Enum\EstadosClase::listar()) !!};</script>
<script src="{{ asset("assets/eah/js/modulos/clase.js")}}"></script>
@endsection

@section("breadcrumb")
<li class="active">Clases</li>
@endsection

@section("content")
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
            {{ Form::select("estado", App\Helpers\Enum\EstadosClase::listarSimple(), NULL, ["id"=>"bus-estado", "class" => "form-control", "placeholder" => "Todos"]) }}
          </div>
        </div> 
        <div class="form-group">          
          {{ Form::label("bus-tipo-fecha", "Fecha: ", ["class" => "col-sm-2 control-label"]) }} 
          <div class="col-sm-3">
            {{ Form::select('tipoFecha', App\Helpers\Enum\TiposBusquedaFecha::listar(), App\Helpers\Enum\TiposBusquedaFecha::Dia, ["id"=>"bus-tipo-fecha", "class" => "form-control"]) }}
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
                {{ Form::text("fechaMes", \Carbon\Carbon::now()->format("m/Y"), ["id" => "bus-fecha-mes", "class" => "form-control  pull-right", "placeholder" => "mm/aaaa"]) }}
              </div>
            </div>
          </div>
          <div id="sec-bus-fecha-{{ App\Helpers\Enum\TiposBusquedaFecha::Anho }}" style="display: none;">
            <div class="col-sm-3">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>  
                {{ Form::text("fechaAnho", \Carbon\Carbon::now()->format("Y"), ["id" => "bus-fecha-anho", "class" => "form-control  pull-right", "placeholder" => "aaaa"]) }}
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
<div class="row">
  <div class="col-sm-12">
    <div class="box box-info">
      <div class="box-header">
        <h3 class="box-title">Lista de clases</h3> 
      </div>         
      <div class="box-body">
        <table id="tab-lista" class="table table-bordered table-hover">
          <thead>
            <tr>
              <th>Período</th> 
              <th>Alumno</th>  
              <th>Profesor</th>    
              <th>Fecha</th>
              <th>Duración</th>
              <th>Pago por hora al profesor</th>
              <th class="all">Estado</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
