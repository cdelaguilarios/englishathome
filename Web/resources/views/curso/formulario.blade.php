{{----}}
<div class="row">
  <div class="col-sm-12">
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title">Datos</h3>
        <button class="btn btn-default btn-clean pull-right" onclick="utilFormularios.limpiarCampos();" type="button">Limpiar campos</button>
      </div>
      <div class="box-body">
        <div class="form-group">
          {{ Form::label("nombre", "Nombre (*): ", ["class" => "col-sm-2 control-label"]) }}
          <div class="col-sm-10">
            {{ Form::text("nombre", null, ["class" => "form-control", "maxlength" =>"255"]) }}
          </div>
        </div>    
        <div class="form-group">
          {{ Form::label("descripcion", "Descripción curso (*): ", ["class" => "col-sm-2 control-label"]) }}
          <div class="col-sm-10">
            {{ Form::textarea("descripcion", null, ["class" => "form-control", "rows" => "10", "maxlength" =>"8000"]) }}
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
        @include("util.archivosAdjuntos", ["adjuntos" => [(object)["idCampo" => "Adjuntos", "idHtml" => "adjuntos", "titulo" => "Archivos", "archivosRegistrados" => (isset($curso) ? $curso->adjuntos : null), "mensajeReferencia" => "Estos archivos podrán ser enviados a las personas interesadas del curso a través del correo de cotización"]]]) 
        <div class="form-group">
          <div class="col-sm-7 col-sm-offset-2">
            <div class="checkbox">
              <label class="checkbox-custom" data-initialize="checkbox">
                {{ Form::label("activo", "Curso activo", ["class" => "checkbox-label"]) }}
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