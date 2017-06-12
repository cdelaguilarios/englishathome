@extends("layouts.master")
@section("titulo", "Configuración")

@section("section_script")
<script>
  $(document).ready(function () {
    $("#formulario-configuracion").validate({
      ignore: ":hidden",
      rules: {
      @foreach($variablesSistema as $variableSistema)
        @if ($variableSistema->tipo == App\Helpers\Enum\TipoVariableConfiguracion::Password)
          {{ $variableSistema->llave . "_confirmation" }}: {
            equalTo: "#{{ $variableSistema->llave }}"
          },
        @else
          {{ $variableSistema->llave }}: {
            @if($variableSistema->tipo == App\Helpers\Enum\TipoVariableConfiguracion::Correo)
                email: true,    
            @endif 
            @if ($variableSistema->tipo == App\Helpers\Enum\TipoVariableConfiguracion::TextoAreaEditor)
            validarCkEditor: true
            @else
            required: true
            @endif 
          },    
        @endif
      @endForeach
      },
      submitHandler: function (f) {
        if (confirm("¿Está seguro que desea guardar los cambios de estas variables de configuracion?")) {
          $.blockUI({message: "<h4>Guardando datos...</h4>"});
          f.submit();
        }
      },
      highlight: function () {
      },
      unhighlight: function () {
      },
      errorElement: "div",
      errorClass: "help-block-error",
      errorPlacement: function (error, element) {
        if (element.closest("div[class*=col-sm-]").length > 0) {
          element.closest("div[class*=col-sm-]").append(error);
        } else if (element.parent(".input-group").length) {
          error.insertAfter(element.parent());
        } else {
          error.insertAfter(element);
        }
      },
      onfocusout: false,
      onkeyup: false,
      onclick: false
    });
  });  
  @foreach($variablesSistema as $variableSistema)
    @if ($variableSistema->tipo == App\Helpers\Enum\TipoVariableConfiguracion::TextoAreaEditor)
    CKEDITOR.replace("{{ $variableSistema->llave }}");
    @endif 
  @endForeach
</script>
@endsection

@section("breadcrumb")
<li class="active">Configuración</li>
@endsection

@section("content")
@include("partials/errors")
{{ Form::open(["url" => route("configuracion.actualizar"), "id" => "formulario-configuracion", "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }}
<div class="row">
  <div class="col-sm-12">
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title">Variables de configuración</h3>
        <button class="btn btn-default btn-clean pull-right" onclick="limpiarCampos();" type="button">Limpiar campos</button>
      </div>
      <div class="box-body">
        @foreach($variablesSistema as $variableSistema)
        @if($variableSistema->tipo == App\Helpers\Enum\TipoVariableConfiguracion::Password)        
        <div class="form-group">
          {{ Form::label($variableSistema->llave, $variableSistema->titulo . ": ", ["class" => "col-sm-3 control-label"]) }}
          <div class="col-sm-9">
            {{ Form::password($variableSistema->llave, ["class" => "form-control", "maxlength" =>"255", "placeholder" =>"******"]) }}
            @if(isset($variableSistema->recomendacionesAdicionales) && $variableSistema->recomendacionesAdicionales != "")
            <small>{{ $variableSistema->recomendacionesAdicionales }}</small>
            @endif
          </div>
        </div>
        <div class="form-group">
          {{ Form::label($variableSistema->llave . "_confirmation", "Confirmación - " . $variableSistema->titulo  . ": ", ["class" => "col-sm-3 control-label"]) }}
          <div class="col-sm-9">
            {{ Form::password($variableSistema->llave . "_confirmation", ["class" => "form-control", "maxlength" =>"255"]) }}
          </div>
        </div>
        @else
          <div class="form-group">
            {{ Form::label($variableSistema->llave, $variableSistema->titulo . ": ", ["class" => "col-sm-3 control-label"]) }}
            <div class="col-sm-9">
            @if($variableSistema->tipo == App\Helpers\Enum\TipoVariableConfiguracion::Correo)
              {{ Form::email($variableSistema->llave, Crypt::decrypt($variableSistema->valor), ["class" => "form-control", "maxlength" =>"255"]) }}
            @elseif($variableSistema->tipo == App\Helpers\Enum\TipoVariableConfiguracion::Texto)
              {{ Form::text($variableSistema->llave, Crypt::decrypt($variableSistema->valor), ["class" => "form-control", "maxlength" =>"255"]) }}
            @elseif($variableSistema->tipo == App\Helpers\Enum\TipoVariableConfiguracion::TextoArea || $variableSistema->tipo == App\Helpers\Enum\TipoVariableConfiguracion::TextoAreaEditor)
              {{ Form::textarea($variableSistema->llave, Crypt::decrypt($variableSistema->valor), ["class" => "form-control", "rows" => "2", "maxlength" =>"4000"]) }}
            @endif
            @if(isset($variableSistema->recomendacionesAdicionales) && $variableSistema->recomendacionesAdicionales != "")
            <small><b>{{ $variableSistema->recomendacionesAdicionales }}</b></small>
            @endif
            </div>
          </div>
        @endif
        @endForeach
      </div>
      <div class="box-footer">    
        <div class="form-group">
          <div class="col-sm-6">
            <span>(*) Campos obligatorios</span>
          </div>
          <div class="col-sm-6">            
            <button type="submit" class="btn btn-success pull-right">
              Guardar
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
{{ Form::close() }}
@endsection
