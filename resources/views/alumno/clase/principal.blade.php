<script src="{{ asset("assets/eah/js/modulos/alumno/clase/principal.js")}}"></script>  
<div class="row">
  <div class="col-sm-12">    
    <div class="box-header">   
      @if(isset($alumno->profesorActual) && $alumno->duracionTotalXClasesPendientes > 0) 
      @include("alumno.clase.formularioConfirmar") 
      @endif   
    </div> 
  </div>
  @include("alumno.clase.lista")    
</div>