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
                <th>Tarea</th>
                <th>Fecha de notificación</th>
                <th>Estado</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>