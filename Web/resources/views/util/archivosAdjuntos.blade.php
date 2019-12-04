{{----}}
<script>
  var adjuntos = {!!  json_encode($adjuntos) !!};
</script>
<script src="{{ asset("assets/eah/js/modulos/util/archivosAdjuntos.js")}}"></script>
@foreach ($adjuntos as $adjunto)
<div class="form-group">
  {{ Form::label($adjunto->idCampo, $adjunto->titulo .  ": ", ["class" => "col-sm-2 control-label"]) }}   
  <div class="col-sm-10">
    <div id="{{ $adjunto->idHtml }}">{{ (Auth::guest() ? "Upload file" : "Subir archivo") }}</div>
    @if(isset($adjunto->archivosRegistrados) && $adjunto->archivosRegistrados != null)
      @php
        $archivosRegistrados = explode(",", $adjunto->archivosRegistrados);
        $contadorArchivosReg = 0;
      @endphp   
      @foreach ($archivosRegistrados as $archivoReg)
        @php    
        $datosArchivoReg = ($archivoReg != "" ? explode(":", $archivoReg) : []);
        @endphp       
        
        @if(count($datosArchivoReg) == 2)
          @php ($rutaArchivo = route("archivos", ["nombre" => $datosArchivoReg[0], "esDocumentoPersonal" => 1]))
          @if(strpos(@get_headers($rutaArchivo)[0],'200')!==false)
          <div class="ajax-file-upload-container">
            <div class="ajax-file-upload-statusbar" style="width: 400px;">
              <div class="ajax-file-upload-filename">
                @if(getimagesize($rutaArchivo))
                <a href="{{ $rutaArchivo }}" target="_blank"><img src="{{ $rutaArchivo }}" alt="{{ $datosArchivoReg[1] }}" width="200" /></a>
                @else
                <a href="{{ $rutaArchivo }}" download="{{ $datosArchivoReg[1] }}">{{ $datosArchivoReg[1] }}</a>
                @endif
              </div>
              <div class="ajax-file-upload-progress">
                <div class="ajax-file-upload-bar" style="width: 100%;"></div>
              </div>
              <div class="ajax-file-upload-red" onclick="archivosAdjuntos.eliminar(this, '{{ $adjunto->idHtml }}', '{{ $datosArchivoReg[0] }}')">{{ (Auth::guest() ? "Delete" : "Eliminar") }}</div>
            </div>
          </div>
          @php ($contadorArchivosReg = $contadorArchivosReg + 1)
          @endif
        @endif
        @if (isset($adjunto->cantidadMaximaArchivos) && ($contadorArchivosReg + 1) > $adjunto->cantidadMaximaArchivos)
          @break
        @endif
      @endforeach 
    @endif
    {{ Form::hidden("nombresArchivos" . $adjunto->idCampo, "", ["id" => "nombres-archivos-" . $adjunto->idHtml]) }}
    {{ Form::hidden("nombresOriginalesArchivos" . $adjunto->idCampo, "", ["id" => "nombres-originales-archivos-" . $adjunto->idHtml]) }}
    {{ Form::hidden("nombresArchivos" . $adjunto->idCampo . "Eliminados", "", ["id" => "nombres-archivos-" . $adjunto->idHtml . "-eliminados"]) }}
  </div>
  @if(isset($adjunto->mensajeReferencia))
  <div class="col-sm-10 col-sm-offset-2">
    <b>{{ $adjunto->mensajeReferencia }}</b>
  </div>
  @endif
  <div class="clearfix"></div>
</div>
@endforeach  