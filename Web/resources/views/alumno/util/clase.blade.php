<div class="row">
    <div class="col-xs-12">
        <div id="sec-mensajes-clase"></div>
        <div id="sec-clase-1">
            <div class="box-header">
                <a id="btn-nuevo-clase" type="button" class="btn btn-primary btn-sm pull-right">Nueva clase</a>   
            </div>         
            <div class="box-body">
                <table id="tab-lista-periodos-clases" class="table table-bordered table-hover">
                    <thead>
                        <tr> 
                            <th class="col-md-2">Período</th>    
                            <th>Fecha de inicio</th>
                            <th>Fecha de fin</th>
                            <th>Total de horas</th>
                            <th class="col-md-1">&nbsp;</th> 
                        </tr>
                    </thead>
                </table>
            </div>
        </div> 
        <div id="sec-clase-2">                    
            {{ Form::open(['url' => route('alumnos.clases.cancelar', ['id' => $idAlumno]), 'id' => 'formulario-cancelar-clase', 'class' => 'form-horizontal', 'novalidate' => 'novalidate', 'files' => true]) }}
            @include('partials/errors')
            <div id="sec-clase-21">
                <div class="box-header">
                    <h3 class="box-title with-border">Cancelar clase</h3>                
                </div>  
                <div class="box-body">
                    <div class="form-group">
                        <div class="col-sm-4">
                            {{ Form::select('tipoCancelacion', [0 => 'Clase cancelada por el alumno', 1 => 'Clase cancelada por el profesor'], null, ['class' => 'form-control']) }}
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12">
                            <label class="control-label">
                                <i class="fa fa-edit"></i> Datos de cancelación
                            </label>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            {{ Form::label('pagoProfesorClaseCancelada', 'Pago al profesor por clase cancelada: ', ['class' => 'col-sm-4 control-label']) }}
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <b>S/.</b>
                                    </span>
                                    {{ Form::text('pagoProfesorClaseCancelada', null, ['class' => 'form-control', 'maxlength' =>'19']) }}
                                </div>
                            </div>
                            <div class="col-sm-5">
                                <div class="checkbox">
                                    <label class="checkbox-custom" data-initialize="checkbox">
                                        {{ Form::label('reprogramarClaseCancelada', 'Reprogramar clase', ['class' => 'checkbox-label']) }}
                                        {{ Form::checkbox('reprogramarClaseCancelada', null, TRUE) }}
                                    </label>
                                </div>
                            </div> 
                        </div>
                    </div>         
                </div>  
            </div>            
            <div id="sec-clase-22">
                <div class="box-header">
                    <h3 class="box-title with-border">Nueva clase</h3>                
                </div>  
                <div class="box-body">
                    <div class="form-group">
                        <div class="col-sm-12">
                            <br/><span>(*) Campos obligatorios</span>
                        </div>
                    </div>
                </div> 
            </div>
            <div class="box-footer">    
                <button id="btnCancelarCanClase" type="button" class="btn btn-default">Cancelar</button>
                <button id="btnRegistrarCanClase" type="submit" class="btn btn-success pull-right">Registrar</button>
                <button id="btnSiguienteCanClase" type="button" class="btn btn-success pull-right">Siguiente</button>
                <button id="btnAnteriorCanClase"  type="button" class="btn btn-primary pull-right">Anterior</button>
            </div>
            {{ Form::close() }}
        </div> 
    </div>
</div>
@include('alumno.util.docentesDisponibles', ['seccion' => 'Clase'])
@if(isset($idAlumno))
<script>
    var urlListarPeriodos = "{{ route('alumnos.periodosClases.listar', ['id' => $idAlumno]) }}";
    var urlEliminarClase = "{{ route('alumnos.clases.eliminar', ['id' => $idAlumno, 'idClase' => 0]) }}";
    var urlListarClases = "{{ route('alumnos.periodo.clases.listar', ['id' => $idAlumno, 'numeroPeriodo' => 0]) }}";
    var urlPerfilProfesorClase = "{{ route('profesores.perfil', ['id' => 0]) }}";
    var urlListarDocentesDisponiblesClase = "{{ route('alumnos.clases.docentesDisponibles.listar', ['id' => $idAlumno]) }}";
    var estadosClase = {!!  json_encode(App\Helpers\Enum\EstadosClase::Listar()) !!};
</script>
<script src='{{ asset('assets/eah/js/clase.js')}}'></script>
@endif