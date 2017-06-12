<div class="form-group">
  {{ Form::label("documentoCv", (Auth::guest() ? "CV" : "CV") .  ": ", ["class" => "col-sm-2 control-label"]) }}   
  <div class="col-sm-10">
    <div id="documento-personal-cv">{{ (Auth::guest() ? "Upload" : "Subir") }}</div>
    @if(isset($docente) && $docente->cv != null)
    @php
    $cv = explode(",", $docente->cv);
    $datosCv = ((count($cv) > 0 && $cv[0] != "") ? explode(":", $cv[0]) : []);
    @endphp       
    @if(count($datosCv) == 2)
    <div class="ajax-file-upload-container">
      <div class="ajax-file-upload-statusbar" style="width: 400px;">
        <div class="ajax-file-upload-filename">
          <a href="{{ route("archivos", ["nombre" => $datosCv[0]]) }}" download="{{ $datosCv[1] }}">{{ $datosCv[1] }}</a>
        </div>
        <div class="ajax-file-upload-progress">
          <div class="ajax-file-upload-bar" style="width: 100%;"></div>
        </div>
        <div class="ajax-file-upload-red" onclick="eliminarDocumentoPersonalDocente(this, 'cv', '{{ $datosCv[0] }}')">Eliminar</div>
      </div>
    </div>
    @endif
    @endif
    {{ Form::hidden("nombreDocumentoCv", "", ["id" => "nombres-archivos-documento-personal-cv"]) }}
    {{ Form::hidden("nombreOriginalDocumentoCv", "", ["id" => "nombres-originales-archivos-documento-personal-cv"]) }}
    {{ Form::hidden("nombreDocumentoCvEliminado", "", ["id" => "nombres-archivos-documento-personal-cv-eliminado"]) }}
  </div>
  <div class="clearfix"></div>
</div>
<div class="form-group">
  {{ Form::label("documentoCertificadoInternacional", (Auth::guest() ? "International Certificate" : "Certificado internacioal") .  ": ", ["class" => "col-sm-2 control-label"]) }}   
  <div class="col-sm-10">
    <div id="documento-personal-certificado-internacional">{{ (Auth::guest() ? "Upload" : "Subir") }}</div>              
    @if(isset($docente) && $docente->certificadoInternacional != null)
    @php
    $certificadoInternacional = explode(",", $docente->certificadoInternacional);
    $datosCertificadoInternacional = ((count($certificadoInternacional) > 0 && $certificadoInternacional[0] != "") ? explode(":", $certificadoInternacional[0]) : []);
    @endphp       
    @if(count($datosCertificadoInternacional) == 2)
    <div class="ajax-file-upload-container">
      <div class="ajax-file-upload-statusbar" style="width: 400px;">
        <div class="ajax-file-upload-filename">
          <a href="{{ route("archivos", ["nombre" => $datosCertificadoInternacional[0]]) }}" download="{{ $datosCertificadoInternacional[1] }}">{{ $datosCertificadoInternacional[1] }}</a>
        </div>
        <div class="ajax-file-upload-progress">
          <div class="ajax-file-upload-bar" style="width: 100%;"></div>
        </div>
        <div class="ajax-file-upload-red" onclick="eliminarDocumentoPersonalDocente(this, 'certificado-internacional', '{{ $datosCertificadoInternacional[0] }}')">Eliminar</div>
      </div>
    </div>
    @endif
    @endif
    {{ Form::hidden("nombreDocumentoCertificadoInternacional", "", ["id" => "nombres-archivos-documento-personal-certificado-internacional"]) }}
    {{ Form::hidden("nombreOriginalDocumentoCertificadoInternacional", "", ["id" => "nombres-originales-archivos-documento-personal-certificado-internacional"]) }}
    {{ Form::hidden("nombreDocumentoCertificadoInternacionalEliminado", "", ["id" => "nombres-archivos-documento-personal-certificado-internacional-eliminado"]) }}
  </div>
  <div class="clearfix"></div>
</div>
<div class="form-group">
  {{ Form::label("imagenDocumentoIdentidad", (Auth::guest() ? "ID Card image" : "Imagen del documento de identidad") .  ": ", ["class" => "col-sm-2 control-label"]) }}   
  <div class="col-sm-10">
    <div id="documento-personal-imagen-documento-identidad">{{ (Auth::guest() ? "Upload" : "Subir") }}</div>              
    @if(isset($docente) && $docente->imagenDocumentoIdentidad != null)
    @php
    $imagenDocumentoIdentidad = explode(",", $docente->imagenDocumentoIdentidad);
    $datosImagenDocumentoIdentidad = ((count($imagenDocumentoIdentidad) > 0 && $imagenDocumentoIdentidad[0] != "") ? explode(":", $imagenDocumentoIdentidad[0]) : []);
    @endphp       
    @if(count($datosImagenDocumentoIdentidad) == 2)
    <div class="ajax-file-upload-container">
      <div class="ajax-file-upload-statusbar" style="width: 400px;">
        <div class="ajax-file-upload-filename">
          <a href="{{ route("archivos", ["nombre" => $datosImagenDocumentoIdentidad[0]]) }}" download="{{ $datosImagenDocumentoIdentidad[1] }}">{{ $datosImagenDocumentoIdentidad[1] }}</a>
        </div>
        <div class="ajax-file-upload-progress">
          <div class="ajax-file-upload-bar" style="width: 100%;"></div>
        </div>
        <div class="ajax-file-upload-red" onclick="eliminarDocumentoPersonalDocente(this, 'imagen-documento-identidad', '{{ $datosImagenDocumentoIdentidad[0] }}')">Eliminar</div>
      </div>
    </div>
    @endif
    @endif
    {{ Form::hidden("nombreImagenDocumentoIdentidad", "", ["id" => "nombres-archivos-documento-personal-imagen-documento-identidad"]) }}
    {{ Form::hidden("nombreOriginalImagenDocumentoIdentidad", "", ["id" => "nombres-originales-archivos-documento-personal-imagen-documento-identidad"]) }}
    {{ Form::hidden("nombreImagenDocumentoIdentidadEliminado", "", ["id" => "nombres-archivos-documento-personal-imagen-documento-identidad-eliminado"]) }}
  </div>
  <div class="clearfix"></div>
</div>
<script src="{{ asset("assets/eah/js/documentosPersonalesDocente.js")}}"></script>