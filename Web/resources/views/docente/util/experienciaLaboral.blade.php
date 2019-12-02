{{----}}
<script src="{{ asset("assets/eah/js/modulos/docente/experienciaLaboral.js")}}"></script>
{{ Form::model($docente, ["method" => "PATCH", "action" => ["DocenteController@actualizarExperienciaLaboral", $docente->id], "id" => "formulario-experiencia-laboral-docente", "class" => "form-horizontal", "files" => true]) }}
<div class="row">
  <div class="col-sm-12">
    <div class="box-body">
      <div class="form-group">
        {{ Form::label("ultimosTrabajos", "Últimos dos trabajos como docente: ", ["class" => "col-sm-2 control-label"]) }}            
        <div class="col-sm-10">
          {{ Form::textarea("ultimosTrabajos", null, ["class" => "form-control", "rows" => "4", "maxlength" =>"1000"]) }}
        </div>               
      </div> 
      <div class="form-group">
        {{ Form::label("experienciaOtrosIdiomas", "Experiencia como docente de otros idiomas: ", ["class" => "col-sm-2 control-label"]) }}            
        <div class="col-sm-10">
          {{ Form::textarea("experienciaOtrosIdiomas", null, ["class" => "form-control", "rows" => "4", "maxlength" =>"1000"]) }}
        </div>               
      </div>
      <div class="form-group">
        {{ Form::label("descripcionPropia", "Descripción propia como docente: ", ["class" => "col-sm-2 control-label"]) }}            
        <div class="col-sm-10">
          {{ Form::textarea("descripcionPropia", null, ["class" => "form-control", "rows" => "4", "maxlength" =>"1000"]) }}
        </div>               
      </div>
      <div class="form-group">
        {{ Form::label("ensayo", "Ensayo: ", ["class" => "col-sm-2 control-label"]) }}            
        <div class="col-sm-10">
          {{ Form::textarea("ensayo", null, ["class" => "form-control", "rows" => "4", "maxlength" =>"1000"]) }}
        </div>              
      </div>
          @include("util.archivosAdjuntos", ["adjuntos" => [
          (object)["idCampo" => "DocumentoPersonalCv", "idHtml" => "documento-personal-cv", "titulo" => "CV", "archivosRegistrados" => $docente->cv, "mensajeReferencia" => null, "cantidadMaximaArchivos" => 1],
          (object)["idCampo" => "DocumentoPersonalCertificadoInternacional", "idHtml" => "documento-personal-certificado-internacional", "titulo" => "Certificado internacional", "archivosRegistrados" => $docente->certificadoInternacional, "mensajeReferencia" => null, "cantidadMaximaArchivos" => 1],
          (object)["idCampo" => "DocumentoPersonalImagenDocumentoIdentidad", "idHtml" => "documento-personal-imagen-documento-identidad", "titulo" => "Imagen del documento de identidad", "archivosRegistrados" => $docente->imagenDocumentoIdentidad, "mensajeReferencia" => null, "cantidadMaximaArchivos" => 1]
          ]]) 
      <div class="form-group">
        {{ Form::label("audio", "Audio de presentación: ", ["class" => "col-sm-2 control-label"]) }} 
        <div class="col-sm-10">
          {{ Form::file("audio", null) }}
        </div>  
      </div>
      @if (isset($docente) && isset($docente->audio) && !empty($docente->audio))
      <div class="form-group">
        <div class="col-sm-offset-1 col-sm-10">
          <audio controls>
            <source src="{{ route("archivos", ["nombre" => ($docente->audio), "esAudio" => 1]) }}">
            Tu explorador no soporta este elemento de audio
          </audio>
        </div>            
      </div>
      @endif
    </div>
    <div class="box-footer">    
      <div class="form-group">          
        <div class="col-sm-12">               
          <button type="submit" class="btn btn-success pull-right">Guardar</button>
        </div>
      </div>
    </div>
  </div>
</div>
{{ Form::close() }}