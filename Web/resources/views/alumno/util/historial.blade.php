<ul id="sec-historial" class="timeline timeline-inverse"></ul>
<div id="sec-boton-carga-mas-historial" style="display:none">
    <a class="btn btn-sm btn-primary" onclick="cargarHistorial()">
        <i class="fa fa-angle-double-down"></i> Mostrar más
    </a>
</div>

{{ Form::hidden("numeroCarga", 0) }} 
@if(isset($idAlumno))
<script>
    var urlCargarHistorial = "{{ route('alumnos.historial', ['id' => $idAlumno]) }}";
    var urlImagenesHistorial = "{{ route('imagenes', ['rutaImagen' => '0']) }}";
</script>
<script src="{{ asset("assets/eah/js/historial.js")}}"></script>
@endif