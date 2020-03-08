{{----}}
{{--*/ $idSeccionHis = "historial" /*--}}  
<script src="{{ asset("assets/eah/js/modulos/util/notificacion/principal.js")}}"></script>
<script src="{{ asset("assets/eah/js/modulos/util/notificacion/listaHistorial.js")}}"></script>
<script src="{{ asset("assets/eah/js/modulos/util/notificacion/crearEditar.js")}}"></script>
<script src="{{ asset("assets/eah/js/modulos/util/notificacion/formulario.js")}}"></script>
<script>
  var listaNotificacionesHistorial = new ListaNotificacionesHistorial({
    //URL's
    urlListarNotificacionesHistorial: "{{ route('notificaciones.listar.historial', ['idEntidad' => $idEntidad]) }}",
    urlEliminarNotificacion: "{{ route('notificaciones.eliminar', ['id' => 0]) }}",
    //Módulos
    nombreModuloCrearEditar: "crearEditarNotificacionHistorial"
  });
  
  var crearEditarNotificacionHistorial = new CrearEditarNotificacion({
    //URL's
    urlDatosNotificacion: "{{ route('notificaciones.datos', ['id' => 0]) }}",
    //Datos adicionales
    idSeccion: "{{ $idSeccionHis }}",
    //Módulos
    formularioNotificacion: new FormularioNotificacion({
      //Datos adicionales
      idSeccion: "{{ $idSeccionHis }}"
    }),
    listaNotificaciones: listaNotificacionesHistorial
  });
  
  var notificacionesHistorial = new Notificaciones({    
    listaNotificaciones: listaNotificacionesHistorial
  }); 
</script>
<div class="row">
  <div class="col-sm-12">
    <div id="sec-notificaciones-historial-mensajes" tabindex="0"></div>
    @include("util.notificacion.listaHistorial")   
    @include("util.notificacion.crearEditar", ["idSeccion" => $idSeccionHis])
  </div>
</div>