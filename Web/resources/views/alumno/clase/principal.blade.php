{{----}}
<script type="text/javascript">
  var duracionTotalXClasesPendientes = parseInt("{{ $alumno->duracionTotalXClasesPendientes }}") / 3600;
</script>
<script src="{{ asset("assets/eah/js/modulos/alumno/clase/principal.js")}}"></script>  
<div class="row">
  <div class="col-sm-12">    
    <div class="box-header">  
      {{--TODO: falta botón para descarga clases--}}
      {{--<a href="{{ route("alumnos.clases.descargar.lista", $alumno->id)}}" target="_blank" class="btn btn-primary btn-sm">Descargar lista de clases</a>--}} 
    
      @if(isset($alumno->ultimoPago) && isset($alumno->ultimoPago->idProfesor) && $alumno->duracionTotalXClasesPendientes > 0) 
      @include("alumno.clase.formularioConfirmar") 
      @endif   
    </div> 
  </div>
  @include("alumno.clase.lista")    
</div>