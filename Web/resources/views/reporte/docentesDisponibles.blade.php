@extends("layouts.master")
@section("titulo", "Docentes disponibles")

@section("section_script")
<script src="{{ asset("assets/eah/js/modulos/reporte/docenteDisponible.js")}}"></script>
@endsection

@section("breadcrumb")
<li class="active">Docentes disponibles</li>
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
          {{ Form::label("bus-tipo-docente", "Tipo: ", ["class" => "col-sm-1 control-label"]) }}
          <div class="col-sm-3">
            {{ Form::select("tipoDocente", $tiposDocente, NULL, ["id" => "bus-tipo-docente", "class" => "form-control", "placeholder" => "Todos"]) }}
          </div>
          {{ Form::label("bus-sexo-docente", "Sexo: ", ["class" => "col-sm-1 control-label"]) }}
          <div class="col-sm-3">
            {{ Form::select("sexoDocente", $sexos, NULL, ["id" => "bus-sexo-docente", "class" => "form-control", "placeholder" => "Todos"]) }}
          </div>   
        </div> 
        <div class="form-group">          
          {{ Form::label("bus-id-curso", "Curso: ", ["class" => "col-sm-1 control-label"]) }}
          <div class="col-sm-3">
            {{ Form::select("idCursoDocente", $cursos, NULL, ["id" => "bus-id-curso", "class" => "form-control", "placeholder" => "Todos"]) }}
          </div> 
          {{ Form::label("bus-fecha", "Curso: ", ["class" => "col-sm-1 control-label"]) }}
          <div>
            <div class="col-sm-3">            
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>  
                {{ Form::text("fecha", \Carbon\Carbon::now()->format("d/m/Y"), ["id" => "bus-fecha", "class" => "form-control  pull-right", "placeholder" => "dd/mm/aaaa"]) }}
              </div>
            </div>
          </div>
        </div> 
      </div>
    </div>
  </div>
</div>
@endsection
