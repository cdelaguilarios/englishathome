<div class="row">
  <div class="col-sm-12">
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title">Datos</h3>
      </div>
      <div class="box-body">
        <div class="form-group">
          {{ Form::label("nombre", "Nombre (*): ", ["class" => "col-sm-2 control-label"]) }}
          <div class="col-sm-10">
            {{ Form::text("nombre", null, ["class" => "form-control", "maxlength" =>"255"]) }}
          </div>
        </div>   
        <div class="form-group">          
          {{ Form::label("imagen", "Imagen (1170 X 500 px): ", ["class" => "col-sm-2 control-label"]) }}
          <div class="col-sm-4">
            {{ Form::file("imagen", null) }}
          </div>
          @if (isset($curso->imagen) && !empty($curso->imagen) && $curso->imagen != "null")
          <div class="col-sm-3">
            <a href="{{ route("archivos", ["nombre" => $curso->imagen]) }}" target="_blank">
              <img src="{{ route("archivos", ["nombre" => $curso->imagen]) }}" width="120"/>
            </a>
          </div>
          @endif          
        </div>   
        <div class="form-group">
          {{ Form::label("descripcion", "Descripción curso (*): ", ["class" => "col-sm-2 control-label"]) }}
          <div class="col-sm-10">
            {{ Form::textarea("descripcion", null, ["class" => "form-control", "rows" => "10", "maxlength" =>"8000"]) }}
          </div>                                        
        </div>
        <div class="form-group">
          {{ Form::label("modulos", "Módulos (*): ", ["class" => "col-sm-2 control-label"]) }}
          <div class="col-sm-10">
            {{ Form::textarea("modulos", null, ["class" => "form-control", "rows" => "10", "maxlength" =>"8000"]) }}
          </div>                                        
        </div>
        <div class="form-group">
          {{ Form::label("metodologia", "Metodología (*): ", ["class" => "col-sm-2 control-label"]) }}
          <div class="col-sm-10">
            {{ Form::textarea("metodologia", null, ["class" => "form-control", "rows" => "10", "maxlength" =>"8000"]) }}
          </div>                                        
        </div>
        <div class="form-group">
          {{ Form::label("incluye", "Curso incluye (*): ", ["class" => "col-sm-2 control-label"]) }}
          <div class="col-sm-10">
            {{ Form::textarea("incluye", null, ["class" => "form-control", "rows" => "10", "maxlength" =>"8000"]) }}
          </div>                                        
        </div>
        <div class="form-group">
          {{ Form::label("inversion", "Inversión (*): ", ["class" => "col-sm-2 control-label"]) }}
          <div class="col-sm-10 sec-inversion">
            {{ Form::textarea("inversion", null, ["class" => "form-control", "rows" => "10", "maxlength" =>"8000"]) }}
          </div>                                        
        </div>
        <div class="form-group">
          <div class="col-sm-7 col-sm-offset-2">
            <div class="checkbox">
              <label class="checkbox-custom" data-initialize="checkbox">
                {{ Form::label("incluir-inversion-cuotas", "Incluir inversión en cuotas", ["class" => "checkbox-label"]) }}
                {{ Form::checkbox("incluirInversionCuotas", null, (isset($curso) && $curso->incluirInversionCuotas == 1 ? TRUE : FALSE), ["id" => "incluir-inversion-cuotas"]) }}
              </label>
            </div>
          </div>
        </div>
        <div id="sec-inversion-cuotas" class="form-group">
          {{ Form::label("inversion-cuotas", "Inversión en cuotas (*): ", ["class" => "col-sm-2 control-label"]) }}
          @include("util.calculadoraInversionCuotas") 
          <div class="col-sm-10 col-sm-offset-2 sec-inversion">
            {{ Form::textarea("inversionCuotas", null, ["id" => "inversion-cuotas", "class" => "form-control", "rows" => "10", "maxlength" =>"8000"]) }}
          </div>                                        
        </div>
        <div class="form-group">
          {{ Form::label("notas-adicionales", "Notas adicionales (*): ", ["class" => "col-sm-2 control-label"]) }}
          <div class="col-sm-10 sec-inversion">
            {{ Form::textarea("notasAdicionales", null, ["id" => "notas-adicionales", "class" => "form-control", "rows" => "10", "maxlength" =>"8000"]) }}
          </div>                                        
        </div>
        <div class="form-group">
          <div class="col-sm-7 col-sm-offset-2">
            <div class="checkbox">
              <label class="checkbox-custom" data-initialize="checkbox">
                {{ Form::label("activo", "Activo", ["class" => "checkbox-label"]) }}
                {{ Form::checkbox("activo", null, (isset($curso) && $curso->activo == 1 ? TRUE : FALSE)) }}
              </label>
            </div>
          </div>
        </div>
        {{ Form::hidden("id") }}
      </div>
      <div class="box-footer">    
        <div class="form-group">
          <div class="col-sm-6">
            <span>(*) Campos obligatorios</span>
          </div>
          <div class="col-sm-6">               
            <button id="btn-guardar" type="submit" class="btn btn-success pull-right">
              {{ ((isset($modo) && $modo == "registrar") ? "Registrar" : "Guardar") }}
            </button>
            <a href="{{ route("cursos") }}" type="button" class="btn btn-default pull-right" >Cancelar</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
