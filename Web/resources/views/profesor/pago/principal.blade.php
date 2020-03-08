{{----}}
<script src="{{ asset("assets/eah/js/modulos/profesor/pago/principal.js")}}"></script>    
<script src="{{ asset("assets/eah/js/modulos/profesor/pago/formulario.js")}}"></script>
<div class="row">
  <div class="col-sm-12">
    <div id="sec-mensajes-pago" tabindex="0"></div>
    @include("profesor.pago.lista")     
    @include("profesor.pago.crear")
    @include("profesor.pago.actualizar")
  </div>
</div>