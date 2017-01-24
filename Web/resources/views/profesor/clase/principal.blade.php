<div id="sec-clase-filtros-busqueda" class="row">
  <div class="col-sm-12">
    <div id="sec-mensajes-clase"></div>
    <div class="">
      <div class="box-header">
        <h3 class="box-title">Filtros de búsquedad</h3> 
      </div>         
      <div class="box-body form-horizontal">
        <div class="form-group">          
          {{ Form::label("bus-estado-clase", "Estado de clase: ", ["class" => "col-sm-2 control-label"]) }}
          <div class="col-sm-3">
            {{ Form::select("estadoClase", App\Helpers\Enum\EstadosClase::listarSimple(), NULL, ["id"=>"bus-estado-clase", "class" => "form-control", "placeholder" => "Todos"]) }}
          </div>
        </div> 
        <div class="form-group">          
          {{ Form::label("bus-estado-clase-pago", "Estado de pago: ", ["class" => "col-sm-2 control-label"]) }}
          <div class="col-sm-3">
            {{ Form::select("estadoClasePago", App\Helpers\Enum\EstadosPago::listarCambio(), NULL, ["id"=>"bus-estado-clase-pago", "class" => "form-control", "placeholder" => "Todos"]) }}
          </div>
        </div> 
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-sm-12">
    <div id="sec-clase-1">      
      <div class="box-body">
        <table id="tab-lista-clases" class="table table-bordered table-hover">
          <thead>
            <tr> 
              <th>Seleccionar</th>  
              <th>Alumno</th>    
              <th>Fecha</th>
              <th>Duración</th>
              <th>Pago por hora</th>
              <th class="all">Estado</th>
            </tr>
          </thead>
        </table>  
      </div>
      <div id="sec-clase-11" style="display: none;">
        <a id="btn-registrar-pago-clase" type="button" class="btn btn-primary btn-sm">Registrar pago</a>                 
      </div>
    </div>    
    <div id="sec-clase-2" style="display: none;">
      {{ Form::open(["url" => route("profesores.clases.pagos.registrar", ["id" => $idProfesor]), "id" => "formulario-pago-clase", "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }}
      @include("profesor.clase.formularioPago") 
      {{ Form::close() }}
    </div>   
  </div>
  <script>
    var urlListarClases = "{{ route('profesores.clases.listar', ['id' => $idProfesor]) }}";
  </script>
  <script src="{{ asset("assets/eah/js/modulos/profesor/clase.js")}}"></script>
</div>