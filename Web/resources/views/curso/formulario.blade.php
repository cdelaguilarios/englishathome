<div class="row">
  <div class="col-sm-12">
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title">Datos</h3>
        <button class="btn btn-default btn-clean pull-right" onclick="limpiarCampos();" type="button">Limpiar campos</button>
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
          @if (isset($curso->imagen) && !empty($curso->imagen) && $curso->imagen != "NULL")
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
            {{ Form::textarea("descripcion", NULL, ["class" => "form-control", "rows" => "10", "maxlength" =>"4000"]) }}
          </div>                                        
        </div>
        <div class="form-group">
          {{ Form::label("modulos", "Módulos (*): ", ["class" => "col-sm-2 control-label"]) }}
          <div class="col-sm-10">
            {{ Form::textarea("modulos", NULL, ["class" => "form-control", "rows" => "10", "maxlength" =>"4000"]) }}
          </div>                                        
        </div>
        <div class="form-group">
          {{ Form::label("metodologia", "Metodología (*): ", ["class" => "col-sm-2 control-label"]) }}
          <div class="col-sm-10">
            {{ Form::textarea("metodologia", NULL, ["class" => "form-control", "rows" => "10", "maxlength" =>"4000"]) }}
          </div>                                        
        </div>
        <div class="form-group">
          {{ Form::label("incluye", "Curso incluye (*): ", ["class" => "col-sm-2 control-label"]) }}
          <div class="col-sm-10">
            {{ Form::textarea("incluye", NULL, ["class" => "form-control", "rows" => "10", "maxlength" =>"4000"]) }}
          </div>                                        
        </div>
        <div class="form-group">
          {{ Form::label("inversion", "Inversión (*): ", ["class" => "col-sm-2 control-label"]) }}
          <div class="col-sm-10 sec-inversion">
            {{ Form::textarea("inversion", NULL, ["class" => "form-control", "rows" => "10", "maxlength" =>"4000"]) }}
          </div>                                        
        </div>
        <div class="form-group">
          {{ Form::label("inversion-cuotas", "Inversión en cuotas (*): ", ["class" => "col-sm-2 control-label"]) }}
          <div class="col-sm-10 sec-inversion">
            {{ Form::textarea("inversionCuotas", NULL, ["id" => "inversion-cuotas", "class" => "form-control", "rows" => "10", "maxlength" =>"4000"]) }}
          </div>                                        
        </div>
        <div class="form-group">
          {{ Form::label("notas-adicionales", "Notas adicionales (*): ", ["class" => "col-sm-2 control-label"]) }}
          <div class="col-sm-10 sec-inversion">
            {{ Form::textarea("notasAdicionales", NULL, ["id" => "notas-adicionales", "class" => "form-control", "rows" => "10", "maxlength" =>"4000"]) }}
          </div>                                        
        </div>
        <div class="form-group">
          <div class="col-sm-7 col-sm-offset-2">
            <div class="checkbox">
              <label class="checkbox-custom" data-initialize="checkbox">
                {{ Form::label("activo", "Activo", ["class" => "checkbox-label"]) }}
                {{ Form::checkbox("activo", NULL, (isset($curso) && $curso->activo == 1 ? TRUE : FALSE)) }}
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
