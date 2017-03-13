@include("util.filtroBusqueda", ["idSeccion" => "sec-clase-filtros-busqueda", "incluirEstadosClase" => 1, "incluirEstadosPago" => 1])
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
    var estadosClase = {!!  json_encode(App\Helpers\Enum\EstadosClase::listar()) !!};
  </script>
  <script src="{{ asset("assets/eah/js/modulos/profesor/clase.js")}}"></script>
</div>