<script src="{{ asset("assets/eah/js/modulos/util/tarea/principal.js")}}"></script>
<script src="{{ asset("assets/eah/js/modulos/util/tarea/panel.js")}}"></script>  
<script src="{{ asset("assets/eah/js/modulos/util/tarea/crearEditar.js")}}"></script>
<script src="{{ asset("assets/eah/js/modulos/util/tarea/formulario.js")}}"></script>
<script>  
  //Módulo panel
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
    idUsuarioActual: {{ (Auth::guest() ? 0 : $usuarioActual->id) }},
    //Módulos
    nombreModuloPanel: "panelTareas",
    nombreModuloCrearEditar: "crearEditarTarea"
  }); 
  
  //Módulo principal (wrapper)
  var tareas = new Tareas({
    panelTareas: panelTareas
  });  
     
  //Módulo crear editar
  var crearEditarTarea = new CrearEditarTarea({
    //URL's
    urlDatosTarea: "{{ route('tareas.datos', ['id' => 0]) }}",
    //Módulos
    formularioTarea: new FormularioTarea({
      //URL's
      urlBuscarUsuarios: "{{ route('usuarios.buscar') }}"
    })
  });
  crearEditarTarea._args.formularioTarea._args.panelTareas = panelTareas;
  crearEditarTarea._args.moduloPrincipal = tareas;
</script>
@if($usuarioActual->rol == App\Helpers\Enum\RolesUsuario::Principal)  
  <script src="{{ asset("assets/eah/js/modulos/util/tarea/lista.js")}}"></script>
  <script> 
    var listaTareas = new ListaTareas({
      //URL's
      urlListarTareas: "{{ route('tareas.listar') }}",
      urlEliminarTarea: "{{ route('tareas.eliminar', ['id' => 0]) }}",
      //Módulos
      nombreModuloCrearEditar: "crearEditarTarea"
    }); 
    crearEditarTarea._args.formularioTarea._args.listaTareas = listaTareas;
    crearEditarTarea._args.listaTareas = listaTareas;
    tareas._args.listaTareas = listaTareas;
  </script>
@endif
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
        @if($usuarioActual->rol == App\Helpers\Enum\RolesUsuario::Principal)     
        <div class="nav-tabs-custom">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#pest-panel-tareas" data-toggle="tab">Panel</a></li>
            <li><a href="#pest-lista-tareas" data-toggle="tab">Lista</a></li>
          </ul>
          <div class="tab-content">
            <div id="pest-panel-tareas" class="active tab-pane">
              @include("util.tarea.panel") 
            </div>
            <div id="pest-lista-tareas" class="tab-pane">
              @include("util.tarea.lista")      
            </div>
          </div>
        </div>
        @else
        @include("util.tarea.panel") 
        @endif
        @include("util.tarea.crearEditar")
      </div>
    </div>
  </div>
</div>