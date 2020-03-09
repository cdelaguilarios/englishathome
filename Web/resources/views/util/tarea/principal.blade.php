{{----}}
<script src="{{ asset("assets/eah/js/modulos/util/tarea/principal.js")}}"></script>
<script src="{{ asset("assets/eah/js/modulos/util/tarea/panel.js")}}"></script>  
<script src="{{ asset("assets/eah/js/modulos/util/tarea/crearEditar.js")}}"></script>
<script src="{{ asset("assets/eah/js/modulos/util/tarea/formulario.js")}}"></script>
<script>  
  var panelTareas = new PanelTareas({
    //URL's
    urlListarTareasPanel: "{{ route('tareas.listar.panel') }}",
    urlListarTareasNoRealizadas: "{{ route('tareas.listar.no.realizadas') }}",
    urlActualizarEstadoTarea: "{{ route('tareas.actualizar.estado', ['id' => 0]) }}",
    urlPerfilEntidad: "{{ route('entidades.perfil', ['id' => 0]) }}",
    urlRevisarMultiple: "{{ route('tareas.revisar.multiple') }}",
    urlEliminarTarea: "{{ route('tareas.eliminar', ['id' => 0]) }}",
    //Datos adicionales
    idBtnVerTareas: "btn-ver-tareas",
    estadoTareaPendiente: "{{ App\Helpers\Enum\EstadosTarea::Pendiente }}",
    estadoTareaEnProceso: "{{ App\Helpers\Enum\EstadosTarea::EnProceso }}",
    estadoTareaRealizada: "{{ App\Helpers\Enum\EstadosTarea::Realizada }}",
    //Módulos
    nombreModuloCrearEditar: "crearEditarTarea"
  }); 
   
  var crearEditarTarea = new CrearEditarTarea({
    //URL's
    urlDatosTarea: "{{ route('tareas.datos', ['id' => 0]) }}",
    //Módulos
    formularioTarea: new FormularioTarea({
      //URL's
      urlBuscarUsuarios: "{{ route('usuarios.buscar') }}",
      //Módulos
      panelTareas: panelTareas
    }),
    panelTareas: panelTareas
  });
  
  var tareas = new Tareas({
    panelTareas: panelTareas
  });  
</script>
<div id="mod-tareas" class="modal" data-keyboard="false" style="text-align: initial">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Tareas</h4>
      </div>
      <div class="modal-body">   
        <div id="sec-tareas-mensajes" tabindex="0"></div>
        @include("util.tarea.panel") 
        @include("util.tarea.crearEditar")
      </div>
    </div>
  </div>
</div>