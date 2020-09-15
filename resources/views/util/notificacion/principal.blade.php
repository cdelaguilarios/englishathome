{{--*/ $idSeccionGen = "generales" /*--}}  
<script src="{{ asset("assets/eah/js/modulos/util/notificacion/principal.js")}}"></script>
<script src="{{ asset("assets/eah/js/modulos/util/notificacion/lista.js")}}"></script>  
<script src="{{ asset("assets/eah/js/modulos/util/notificacion/crearEditar.js")}}"></script>
<script src="{{ asset("assets/eah/js/modulos/util/notificacion/formulario.js")}}"></script>
<script>
  var listaNotificacionesGenerales = new ListaNotificacionesGenerales({
    //URL's
    urlListarNotificaciones: "{{ route('notificaciones.listar') }}",
    urlListarNotificacionesNuevas: "{{ route('notificaciones.listar.nuevas') }}",
    urlPerfilEntidad: "{{ route('entidades.perfil', ['id' => 0]) }}",
    urlRevisarMultiple: "{{ route('notificaciones.revisar.multiple') }}",
    urlEliminarNotificacion: "{{ route('notificaciones.eliminar', ['id' => 0]) }}",
    //Datos adicionales
    tipoEntidadUsuario: "{{ App\Helpers\Enum\TiposEntidad::Usuario }}",
    tiposEntidades: {!!  json_encode(App\Helpers\Enum\TiposEntidad::listarTiposBase()) !!},
    tiposNotificaciones: {!!  json_encode(App\Helpers\Enum\TiposNotificacion::listar()) !!},
    idBtnVerNotificaciones: "btn-ver-notificaciones-generales",
    //Módulos
    nombreModuloCrearEditar: "crearEditarNotificacionGeneral"
  }); 
   
  var crearEditarNotificacionGeneral = new CrearEditarNotificacion({
    //URL's
    urlDatosNotificacion: "{{ route('notificaciones.datos', ['id' => 0]) }}",
    //Datos adicionales
    idSeccion: "{{ $idSeccionGen }}",
    //Módulos
    formularioNotificacion: new FormularioNotificacion({
      //Datos adicionales
      idSeccion: "{{ $idSeccionGen }}",
      listaNotificaciones: listaNotificacionesGenerales
    }),
    listaNotificaciones: listaNotificacionesGenerales
  });
  
  var notificacionesGenerales = new Notificaciones({
    listaNotificaciones: listaNotificacionesGenerales
  });  
</script>
<div id="mod-notificaciones-generales" class="modal" data-keyboard="false" style="text-align: initial">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Notificaciones</h4>
      </div>
      <div class="modal-body">   
        <div id="sec-notificaciones-generales-mensajes" tabindex="0"></div>
        @include("util.notificacion.lista") 
        @include("util.notificacion.crearEditar", ["idSeccion" => $idSeccionGen])
      </div>
    </div>
  </div>
</div>