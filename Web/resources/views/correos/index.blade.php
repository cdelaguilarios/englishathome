@extends("layouts.master")
@section("titulo", "Correos masivos")

@section("section_script")
<script>
  var tiposEntidades = {!!  json_encode(App\Helpers\Enum\TiposEntidad::listar()) !!};
  var urlBuscarEntidades = "{{ route('correos.entidades') }}";
</script>
<script src="{{ asset("assets/eah/js/modulos/correos.js") }}"></script>
@endsection

@section("breadcrumb")
<li class="active">Correos masivos</li>
@endsection

@section("content")
@include("partials/errors")
{{ Form::open(["url" => route("correos.registrar"), "id" => "formulario-correos", "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }}
<div class="row">
  <div class="col-sm-12">
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title">Envio de correos masivos</h3>
        <button class="btn btn-default btn-clean pull-right" onclick="limpiarCampos();" type="button">Limpiar campos</button>
      </div>
      <div class="box-body">            
        <div class="form-group">
          {{ Form::label("titulo", "Título (*): ", ["class" => "col-sm-2 control-label"]) }}
          <div class="col-sm-10">
            {{ Form::text("titulo", null, ["class" => "form-control", "maxlength" =>"255"]) }}
          </div>
        </div>            
        <div class="form-group">
          {{ Form::label("asunto", "Asunto (*): ", ["class" => "col-sm-2 control-label"]) }}
          <div class="col-sm-10">
            {{ Form::text("asunto", null, ["class" => "form-control", "maxlength" =>"255"]) }}
          </div>
        </div>         
        <div class="form-group">
          {{ Form::label("mensaje", "Mensaje (*): ", ["class" => "col-sm-2 control-label"]) }}
          <div class="col-sm-10">
            {{ Form::textarea("mensaje", null, ["class" => "form-control", "rows" => "4", "maxlength" =>"4000"]) }}
          </div>
        </div>           
        <div class="form-group">
          {{ Form::label("tipo-entidad-correos", "Enviar a: ", ["class" => "col-sm-2 control-label"]) }}
          <div class="col-sm-2">
            {{ Form::select("tipoEntidad", App\Helpers\Enum\TiposEntidad::listarSeccionCorreos(), null, ["id" => "tipo-entidad-correos", "class" => "form-control", "placeholder" => "Entidades seleccionadas"]) }}
          </div>
        </div>          
        <div id="sec-entidades-seleccionadas-correos" class="form-group">
          {{ Form::label("entidades-seleccionadas-correos", "Seleccionar entidades: ", ["class" => "col-sm-2 control-label"]) }}
          <div class="col-sm-10">
            {{ Form::select("idsEntidadesSeleccionadas", [], null, ["id" => "entidades-seleccionadas-correos", "class" => "form-control", "multiple" => "multiple", "style" => "width: 100%"]) }}
          </div>
        </div>         
        <div class="form-group">
          {{ Form::label("correos-adicionales-correos", "Correos adicionales: ", ["class" => "col-sm-2 control-label"]) }}
          <div class="col-sm-10">
            {{ Form::textarea("correosAdicionales", null, ["id" => "correos-adicionales-correos", "class" => "form-control", "rows" => "4", "maxlength" =>"4000"]) }}
          </div>
        </div>        
        <div class="form-group">
          <div class="col-sm-10 col-sm-offset-2">
            Puede ingresar correos electrónicos adicionales separados por comas (ejm: juan@gmail.com<b>,</b>juana@gmail.com<b>,</b>ju...). Considerar que los correos con un formato incorrecto serán excluidos.
          </div>
        </div>
      </div>
      <div class="box-footer">    
        <div class="form-group">
          <div class="col-sm-6">
            <span>(*) Campos obligatorios</span>
          </div>
          <div class="col-sm-6">            
            <button type="submit" class="btn btn-success pull-right">
              Enviar correos
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div
{{ Form::close() }}
@endsection
