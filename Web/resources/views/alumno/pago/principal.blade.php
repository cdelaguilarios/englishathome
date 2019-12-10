{{----}}
<script src="{{ asset("assets/eah/js/modulos/alumno/pago/principal.js")}}"></script>    
<div class="row">
  <div class="col-sm-12">
    <div id="sec-pago-mensajes"></div>
    @include("alumno.pago.lista")     
    @include("alumno.pago.crearEditar")
  </div>
</div>