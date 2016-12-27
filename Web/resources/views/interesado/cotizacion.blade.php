@extends("layouts.master")
@section("titulo", "Interesados")

@section("section_script")
<script type="text/javascript">
  var urlDatosCurso = "{{ route("cursos.datos", ["id" => 0]) }}";
</script>
<script src="{{ asset("assets/eah/js/modulos/interesado.js")}}"></script>
@endsection

@section("breadcrumb")
<li><a href="{{ route("interesados") }}">Interesados</a></li>
<li class="active">Envio de cotización</li>
@endsection

@section("content") 
@include("partials/errors")
{!! Form::open(["url" => route("interesados.cotizacion", ["id" => $interesado->idEntidad]), "id" => "formulario-interesado-cotizacion", "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) !!}
<div class="row">
  <div class="col-sm-12">
    <div class="box-header">
      <h2 class="box-title">Datos de la persona interesada</h2>   
    </div>  
    <div class="col-sm-3">
      <div class="box-body sec-datos">
        <strong><i class="fa  fa-check-square margin-r-5"></i> Nombre</strong>
        <p class="text-muted">{{ $interesado->nombre . " " . $interesado->apellido }}</p>
        <strong><i class="fa  fa-check-square margin-r-5"></i> Correo electrónico</strong>
        <p class="text-muted">{{ $interesado->correoElectronico }}</p>
      </div>
    </div>
    <div class="col-sm-3">
      <div class="box-body sec-datos">
        <strong><i class="fa  fa-check-square margin-r-5"></i> Teléfono</strong>
        <p class="text-muted">{{ $interesado->telefono }}</p>
        <strong><i class="fa  fa-check-square margin-r-5"></i> Estado</strong>
        <p class="text-muted">{{ $estadosInteresado[$interesado->estado][0] }}</p> 
      </div> 
    </div>
  </div>
</div>
<div class="row">
  <div class="col-sm-12">
    <div class="box-header">
      <h2 class="box-title">Datos de cotización</h2>   
    </div>    
    <div class="form-group">
      {{ Form::label("idCurso", "Curso de interes: ", ["class" => "col-sm-2 control-label"]) }}
      <div class="col-sm-3">
        {{ Form::select("idCurso", $cursos, NULL, ["id" => "id-curso", "class" => "form-control"]) }}
      </div>                   
    </div>
    <div class="form-group">
      {{ Form::label("descripcionCurso", "Descripción curso: ", ["class" => "col-sm-2 control-label"]) }}
      <div class="col-sm-10">
        {{ Form::textarea("descripcionCurso", NULL, ["id" => "descripcion-curso", "class" => "form-control", "rows" => "10", "maxlength" =>"4000"]) }}
      </div>                                        
    </div>
    <div class="form-group">
      {{ Form::label("metodologia", "Metodología: ", ["class" => "col-sm-2 control-label"]) }}
      <div class="col-sm-10">
        {{ Form::textarea("metodologia", NULL, ["id" => "metodologia", "class" => "form-control", "rows" => "10", "maxlength" =>"4000"]) }}
      </div>                                        
    </div>
    <div class="form-group">
      {{ Form::label("cursoIncluye", "Curso incluye: ", ["class" => "col-sm-2 control-label"]) }}
      <div class="col-sm-10">
        {{ Form::textarea("cursoIncluye", NULL, ["id" => "curso-incluye", "class" => "form-control", "rows" => "10", "maxlength" =>"4000"]) }}
      </div>                                        
    </div>
    <div class="form-group">
      {{ Form::label("numeroHorasInversion", "Número de horas: ", ["class" => "col-sm-2 control-label"]) }}
      <div class="col-sm-1">
        {{ Form::text("numeroHorasInversion", 60, ["class" => "form-control", "maxlength" => "4"]) }}
      </div>                   
    </div>
    <div class="form-group">
      {{ Form::label("costoMaterialesIversion", "Costo de materiales: ", ["class" => "col-sm-2 control-label"]) }}
      <div class="col-sm-1">
        {{ Form::text("costoMaterialesIversion", 70, ["class" => "form-control", "maxlength" =>"19"]) }}
      </div>                   
    </div>
    <div class="form-group">
      {{ Form::label("totalInversion", "Inversión total: ", ["class" => "col-sm-2 control-label"]) }}
      <div class="col-sm-1">
        {{ Form::text("totalInversion", 2590, ["class" => "form-control", "maxlength" =>"19", ""]) }}
      </div>                   
    </div>
  </div>
  <div class="col-sm-12">
    <button id="btn-envio-cotización" type="button" class="btn btn-success pull-right">Enviar cotización</button> 
    <button id="btn-envio-cotización-prueba" type="button" class="btn btn-primary pull-right">
      <i class="fa fa-shield"></i> Enviar cotización de prueba
    </button> 
  </div> 
</div>
<div id="mod-correo-cotizacion-prueba" class="modal" data-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Envio de cotización de prueba</h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
          {{ Form::label("correoCotizacionPrueba", "Correo: ", ["class" => "col-sm-2 control-label"]) }}
          <div class="col-sm-5">
            {{ Form::text("correoCotizacionPrueba", null, ["id" => "correo-cotizacion-prueba", "class" => "form-control", "maxlength" =>"245", ""]) }}
          </div> 
          <div class="col-sm-5">
            <button type="submit" class="btn btn-primary pull-right">
              <i class="fa fa-shield"></i> Enviar cotización de prueba
            </button> 
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
{!! Form::close() !!}
@endsection