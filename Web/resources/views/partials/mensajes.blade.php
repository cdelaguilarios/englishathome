{{--*/ $mensajes = Mensajes::obtenerMensajes() /*--}}  
<div class="box-default">
  <div class="box-body contenedor-alerta">
    @foreach($mensajes["exitosos"] as $mensaje)   
    <div class="alert alert-success alert-dismissible" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
      {!! $mensaje !!}
    </div>        
    @endforeach
    @foreach($mensajes["advertencias"] as $mensaje)   
    <div class="alert alert-warning alert-dismissible" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
      {!! $mensaje !!}
    </div>        
    @endforeach
    @foreach($mensajes["alertas"] as $mensaje)   
    <div class="alert alert-info alert-dismissible" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
      {!! $mensaje !!}
    </div>        
    @endforeach
    @foreach($mensajes["errores"] as $mensaje)   
    <div class="alert alert-danger alert-dismissible" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
      {!! $mensaje !!}
    </div>        
    @endforeach
  </div>
</div>
{{--*/ Mensajes::limpiar() /*--}}