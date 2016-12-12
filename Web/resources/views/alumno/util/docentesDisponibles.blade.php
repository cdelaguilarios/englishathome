<div id="mod-docentes-disponibles-{!! $seccion !!}" class="modal" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
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
                        {!! Form::label('tipoDocenteDisponible' . $seccion, 'Tipo: ', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-4">
                            {!! Form::select('tipoDocenteDisponible' . $seccion, $tiposDocente, null, ['id' => 'tipo-docente-disponible-' . $seccion, 'class' => 'form-control']) !!}
                        </div> 
                    </div>
                    <div class="row">
                        {!! Form::label('generoDocenteDisponible' . $seccion, 'Genero: ', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-4">
                            {!! Form::select('generoDocenteDisponible' . $seccion, $generos, null, ['id' => 'genero-docente-disponible-' . $seccion, 'placeholder' => 'Todos', 'class' => 'form-control']) !!}
                        </div> 
                    </div>
                    <div class="row">
                        {!! Form::label('idCursoDocenteDisponible' . $seccion, 'Curso: ', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-10">
                            {!! Form::select('idCursoDocenteDisponible' . $seccion, $cursos, null, ['id' => 'id-curso-docente-disponible-' . $seccion, 'placeholder' => 'Todos', 'class' => 'form-control']) !!}
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
                        <table id="tab-lista-docentes-{!! strtolower($seccion) !!}" class="table table-bordered sub-table">
                            <thead>
                                <tr>
                                    <th>Profesor/Docente</th>
                                    <th>Elegir</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div> 
                </div>
            </div>
            <div class="modal-footer">
                <button id="btn-confirmar-docente-disponible-{!! $seccion !!}" type="button" class="btn btn-success btn-sm">Confirmar</button>
            </div>
        </div>
    </div>
</div>