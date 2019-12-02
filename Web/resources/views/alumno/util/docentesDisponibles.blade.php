{{----}}
<script>
  var urlListar = "{{ route('docentes.disponibles.listar') }}";
  
  var tipoDocenteProfesor = "{{ App\Helpers\Enum\TiposEntidad::Profesor }}";
</script>
<script src="{{ asset("assets/eah/js/modulos/alumno/util/docentesDisponibles.js")}}"></script>     
<div id="mod-docentes-disponibles-{{ $idSeccion }}" class="modal" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Elegir profesor o postulante disponible</h4>
      </div>
      <div class="modal-body">  
        <div class="form-group">
          <label class="control-label">
            <i class="fa fa-search"></i> Filtros de búsqueda
          </label>
        </div>
        <div class="box-body">
          <div class="row">
            {{ Form::label("tipo-docente-disponible-" . $idSeccion, "Tipo: ", ["class" => "col-sm-1 control-label"]) }}
            <div class="col-sm-5">
              {{ Form::select("tipoDocenteDisponible" . $idSeccion, App\Helpers\Enum\TiposEntidad::listarTiposDocente(), null, ["id" => "tipo-docente-disponible-" . $idSeccion, "class" => "form-control"]) }}
            </div> 
            {{ Form::label("estado-docente-disponible-" . $idSeccion, "Estado: ", ["class" => "col-sm-1 control-label"]) }}
            <div class="col-sm-5">
              {{ Form::select("estadoDocenteDisponible" . $idSeccion, App\Helpers\Enum\EstadosDocente::listarBusqueda(), null, ["id" => "estado-docente-disponible-" . $idSeccion, "placeholder" => "Todos", "class" => "form-control"]) }}
            </div> 
          </div>
          <div class="row">
            {{ Form::label("sexo-docente-disponible-" . $idSeccion, "Sexo: ", ["class" => "col-sm-1 control-label"]) }}
            <div class="col-sm-5">
              {{ Form::select("sexoDocenteDisponible" . $idSeccion, App\Helpers\Enum\SexosEntidad::listar(), null, ["id" => "sexo-docente-disponible-" . $idSeccion, "placeholder" => "Todos", "class" => "form-control"]) }}
            </div> 
            {{ Form::label("id-curso-docente-disponible-" . $idSeccion, "Curso: ", ["class" => "col-sm-1 control-label"]) }}
            <div class="col-sm-5">
              {{ Form::select("idCursoDocenteDisponible" . $idSeccion, App\Models\Curso::listarSimple(), $idCurso, ["id" => "id-curso-docente-disponible-" . $idSeccion, "placeholder" => "Todos", "class" => "form-control"]) }}
            </div> 
          </div>
        </div><br/>
        <div class="form-group">
          <label class="control-label">
            <i class="fa fa-list"></i> Lista de profesores disponibles
          </label>
        </div>
        <div class="row box-body">
          <div class="col-sm-12">
            <table id="tab-lista-docentes-{{ strtolower($idSeccion) }}" class="table table-bordered sub-table">
              <thead>
                <tr>
                  <th>Docente</th>
                  <th>Estado</th>
                  <th class="all text-center">Elegir</th>
                </tr>
              </thead>
            </table>
          </div> 
        </div>
      </div>
      <div class="modal-footer">
        <button id="btn-confirmar-docente-disponible-{{ $idSeccion }}" type="button" class="btn btn-success btn-sm">Confirmar</button>
      </div>
    </div>
  </div>
</div>