@if (isset($modo) && $modo == "visualizar")
<a id="btn-ubicacion-mapa" href="javascript:void(0);" title="Ubicación mapa"><i class="fa fa-street-view"></i> Ver ubicación en el mapa</a>
<div id="mod-ubicacion-mapa" class="modal" data-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Ubicación mapa</h4>
      </div>
      <div class="modal-body sec-mapa">
        <div id="mapa"></div>
      </div>
    </div>
  </div>
</div>
{{ Form::hidden("geoLatitud", (isset($geoLatitud) ? $geoLatitud : 0)) }} 
{{ Form::hidden("geoLongitud", (isset($geoLongitud) ? $geoLongitud : 0)) }} 
@elseif (isset($modo) && $modo == "ficha")
<div id="mapa"></div>
{{ Form::hidden("geoLatitud", (isset($geoLatitud) ? $geoLatitud : 0)) }} 
{{ Form::hidden("geoLongitud", (isset($geoLongitud) ? $geoLongitud : 0)) }} 
@else
<input id="mapa-bus" class="controls" type="text" placeholder="{{ ((isset($subSeccion) && $subSeccion == "postulantes" && Auth::guest()) ? "Search" : "Buscar") }}">
<div id="mapa"></div>
@endif
<script type="text/javascript">
  var modoVisualizarMapa = {{ (isset($modo) && ($modo === "visualizar" || $modo === "ficha")) ? "true" : "false" }};
</script>
<script type="text/javascript" src="{{ asset("assets/eah/js/ubicacionMapa.js") }}"></script>
<script src="//maps.googleapis.com/maps/api/js?key=AIzaSyD3y8Vvx3X9BquN5iaZcLQdD-768cy0ADY&libraries=places&callback=verificarJqueryUbicacionMapa{{ ((isset($subSeccion) && $subSeccion == "postulantes" && Auth::guest()) ? "&language=en" : "") }}" async defer></script>