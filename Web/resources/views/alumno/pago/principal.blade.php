{{----}}
<script src="{{ asset("assets/eah/js/modulos/alumno/pago/principal.js")}}"></script>    
<script src="{{ asset("assets/eah/js/modulos/alumno/pago/formulario.js")}}"></script>
<div class="row">
  <div class="col-sm-12">
    <div id="sec-pago-mensajes"></div>
    @include("alumno.pago.lista")     
    @include("alumno.pago.crear")
    @include("alumno.pago.actualizar")
  </div>
</div>