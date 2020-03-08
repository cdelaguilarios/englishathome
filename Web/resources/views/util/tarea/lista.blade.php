<script>
  var urlListarTareas = "{{ route('tareas.listar') }}";
  var urlListarTareasNuevas = "{{ route('tareas.listar.nuevas') }}";
  var urlPerfilEntidad = "{{ route('entidades.perfil', ['id' => 0]) }}";
  var urlActualizarRealizacion = "{{ route('tareas.actualizar.realizacion', ['id' => 0]) }}";
  var urlEliminarTarea = "{{ route('tareas.eliminar', ['id' => 0]) }}";
</script>
<script src="{{ asset("assets/eah/js/modulos/util/tarea/lista.js")}}"></script>     
<div id="sec-tareas-lista" style="display: none">
  <div class="row">
    <div class="col-sm-12">
      <div>
        <div class="box-header">
          <h3 class="box-title">Filtros de búsqueda</h3> 
        </div>         
        <div class="box-body form-horizontal">
          @include("util.filtrosBusquedaFechas", ["idSeccion" => "tareas", "tipoBusquedaDefecto" => App\Helpers\Enum\TiposBusquedaFecha::Dia])
        </div>
      </div>
    </div>
  </div>             
  <div class="row">
    <div class="col-sm-12">
      <div>         
        <div class="box-body">
          <table id="tab-lista-tareas" class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>N°</th>    
                <th class="all">Tarea</th>
                <th class="all">Fecha</th>
                <th class="all">Realizado</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>