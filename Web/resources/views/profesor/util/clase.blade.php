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
            {{ Form::open(['url' => route("alumnos.clases.cancelar", ["id" => $idAlumno]), "id" => "formulario-cancelar-clase", "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }}
            @include('partials/errors')
            <div class="box-header">
                <h3 class="box-title with-border">Cancelar clase</h3>                
            </div>  
            <div class="box-body">
                <div class="form-group">
                    <div class="col-sm-4">
                        {{ Form::select("tipoCancelacion", App\Helpers\Enum\TiposCancelacionClase::listar(), NULL, ["id" => "tipo-cancelacion-clase", "class" => "form-control"]) }}
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <label class="control-label">
                            <i class="fa fa-edit"></i> Datos de cancelación
                        </label>
                    </div>
                </div>
                <div id="sec-clase-21" class="box-body">
                    <div class="form-group">
                        {{ Form::label("pagoProfesor", "Pago al profesor por clase cancelada: ", ["class" => "col-sm-4 control-label"]) }}
                        <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <b>S/.</b>
                                </span>
                                {{ Form::text("pagoProfesor", NULL, ["class" => "form-control", "maxlength" => "19"]) }}
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="checkbox">
                                <label class="checkbox-custom" data-initialize="checkbox">
                                    {{ Form::label("reprogramar-clase-can-alu", "Reprogramar clase ") }}
                                    {{ Form::checkbox("reprogramarCancelacionAlumno", null, FALSE, ["id" => "reprogramar-clase-can-alu"]) }}
                                </label>
                            </div>
                        </div> 
                    </div>
                </div>      
                <div id="sec-clase-22" class="box-body">
                    <div class="form-group">
                        <div class="col-sm-3">
                            <div class="checkbox">
                                <label class="checkbox-custom" data-initialize="checkbox">
                                    {{ Form::label("reprogramar-clase-can-pro", "Reprogramar clase ") }}
                                    {{ Form::checkbox("reprogramarCancelacionProfesor", null, FALSE, ["id" => "reprogramar-clase-can-pro"]) }}
                                </label>
                            </div>
                        </div> 
                    </div>
                </div>    
                <div id="sec-clase-23">
                    <div class="form-group">
                        <div class="col-sm-12">
                            <label class="control-label">
                                <i class="fa fa-calendar-check-o"></i> Nueva clase (reprogramación)
                            </label>
                        </div>
                    </div>           
                    <div class="form-group">
                        {{ Form::label("fecha", "Fecha: ", ["class" => "col-sm-2 control-label"]) }}
                        <div class="col-sm-3">
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>                                
                                {{ Form::text("fecha", NULL, ["id" => "fecha-clase-reprogramada", "class" => "form-control  pull-right"]) }}
                            </div>
                        </div>                        
                    </div>
                    <div class="form-group">
                        {{ Form::label("horaInicio", "Hora inicio: ", ["class" => "col-sm-2 control-label"]) }}
                        <div class="col-sm-3">
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa  fa-clock-o"></i>
                                </div>    
                                {{ Form::select("horaInicio", [], NULL, ["id" => "hora-inicio-clase-reprogramada", "class" => "form-control"]) }}
                            </div>
                        </div>
                        {{ Form::label("duracion", "Duración: ", ["class" => "col-sm-1 control-label"]) }}
                        <div class="col-sm-2">
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa  fa-clock-o"></i>
                                </div>    
                                {{ Form::select("duracion", [], NULL, ["id" => "duracion-clase-reprogramada", "class" => "form-control"]) }}
                            </div>
                        </div>
                    </div>
                    <div class="box-body">                        
                        <div class="form-group">
                            <div class="col-sm-12">
                                <button type="button" class="btn btn-primary btn-sm btn-docentes-disponibles-clase">Elegir profesor disponible</button> 
                            </div>
                        </div>
                        <div id="sec-clase-231">
                            <div class="form-group">
                                {{ Form::label("", "", ["class" => "col-sm-3 control-label nombre-docente-clase"]) }}
                            </div>
                            <div class="form-group">
                                {{ Form::label("costoHoraDocente", "Pago por hora al profesor (*): ", ["class" => "col-sm-3 control-label"]) }}
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <b>S/.</b>
                                        </span>
                                        {{ Form::text("costoHoraDocente", null, ["class" => "form-control", "maxlength" =>"19"]) }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <br/><span>(*) Campos obligatorios</span>
                                </div>
                            </div>
                        </div>
                        {{ Form::hidden("idDocente", "", ["id" => "id-docente-clase-reprogramada"]) }} 


                        {{ Form::hidden("cancelar", 1) }} 
                        {{ Form::hidden("idClase") }} 
                        {{ Form::hidden("idAlumno", $idAlumno) }} 
                        {{ Form::hidden("idProfesor") }} 
                    </div>
                </div>
            </div>      
            <div class="box-footer">    
                <button type="button" class="btn btn-default btn-cancelar-clase">Cancelar</button>
                <button type="submit" class="btn btn-success pull-right">Registrar</button>
            </div>
            {{ Form::close() }}
        </div> 
        <div id="sec-clase-3">                    
            {{ Form::open(['url' => route("alumnos.clases.registrar", ["id" => $idAlumno]), "id" => "formulario-registrar-clase", "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }}
            @include('partials/errors')
            <div class="box-header">
                <h3 class="box-title with-border">Nueva clase</h3>                
            </div>  
            <div class="box-body">           
                <div class="form-group">
                    {{ Form::label("fecha", "Fecha: ", ["class" => "col-sm-2 control-label"]) }}
                    <div class="col-sm-3">
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>                                
                            {{ Form::text("fecha", NULL, ["id" => "fecha-clase", "class" => "form-control  pull-right"]) }}
                        </div>
                    </div>                        
                </div>
                <div class="form-group">
                    {{ Form::label("horaInicio", "Hora inicio: ", ["class" => "col-sm-2 control-label"]) }}
                    <div class="col-sm-3">
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa  fa-clock-o"></i>
                            </div>    
                            {{ Form::select("horaInicio", [], NULL, ["id" => "hora-inicio-clase", "class" => "form-control"]) }}
                        </div>
                    </div>
                    {{ Form::label("duracion", "Duración: ", ["class" => "col-sm-1 control-label"]) }}
                    <div class="col-sm-2">
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa  fa-clock-o"></i>
                            </div>    
                            {{ Form::select("duracion", [], NULL, ["id" => "duracion-clase", "class" => "form-control"]) }}
                        </div>
                    </div>
                </div>
                <div class="form-group">  
                    {{ Form::label("costoHora", "Costo por hora (*): ", ["class" => "col-sm-2 control-label"]) }}   
                    <div class="col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <b>S/.</b>
                            </span>
                            {{ Form::text("costoHora", number_format($costoHoraClase, 2, ".", ","), array("class" => "form-control", "maxlength" =>"19")) }}
                        </div>
                    </div> 
                    {{ Form::label("numeroPeriodo", "Período (*): ", ["class" => "col-sm-2 control-label"]) }}
                    <div class="col-sm-2">
                        {{ Form::number("numeroPeriodo", "", ["class" => "form-control", "maxlength" =>"11", "min" =>"1"]) }}
                    </div>
                    <div class="col-sm-3">
                            <div class="checkbox">
                                <label class="checkbox-custom" data-initialize="checkbox">
                                    {{ Form::label("notificar", "Notificar", ["class" => "checkbox-label"]) }}
                                    {{ Form::checkbox("notificar", null, FALSE) }}
                                </label>
                            </div>
                        </div> 
                </div>
                <div class="box-body">                        
                    <div class="form-group">
                        <div class="col-sm-12">
                            <button type="button" class="btn btn-primary btn-sm btn-docentes-disponibles-clase">Elegir profesor disponible</button> 
                        </div>
                    </div>
                    <div id="sec-clase-31">
                        <div class="form-group">
                            {{ Form::label("", "", ["class" => "col-sm-3 control-label nombre-docente-clase"]) }}
                        </div>
                        <div class="form-group">
                            {{ Form::label("costoHoraDocente", "Pago por hora al profesor (*): ", ["class" => "col-sm-3 control-label"]) }}
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <b>S/.</b>
                                    </span>
                                    {{ Form::text("costoHoraDocente", null, ["class" => "form-control", "maxlength" =>"19"]) }}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <br/><span>(*) Campos obligatorios</span>
                            </div>
                        </div>
                        {{ Form::hidden("idDocente", "", ["id" => "id-docente-clase-registrar"]) }} 

                        {{ Form::hidden("registrar", 1) }} 
                        {{ Form::hidden("idClase") }} 
                    </div>
                </div> 
                <div class="box-footer">    
                    <button type="button" class="btn btn-default btn-cancelar-clase">Cancelar</button>
                    <button type="submit" class="btn btn-success pull-right">Registrar</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    @include('alumno.util.docentesDisponibles', ['seccion' => 'clase'])
    @if(isset($idAlumno))
    <script>
        var urlListarPeriodos = "{{ route('alumnos.periodosClases.listar', ['id' => $idAlumno]) }}";
        var urlListarClases = "{{ route('alumnos.periodo.clases.listar', ['id' => $idAlumno, 'numeroPeriodo' => 0]) }}";
        var urlEliminarClase = "{{ route('alumnos.clases.eliminar', ['id' => $idAlumno, 'idClase' => 0]) }}";
        var urlPerfilProfesorClase = "{{ route('profesores.perfil', ['id' => 0]) }}";
        var urlListarDocentesDisponiblesClase = "{{ route('alumnos.clases.docentesDisponibles.listar', ['id' => $idAlumno]) }}";
        var estadosClase = {!!  json_encode(App\Helpers\Enum\EstadosClase::Listar()) !!};
        var estadosClaseProgramada = "{{ App\Helpers\Enum\EstadosClase::Programada }}";
        var tipoCancelacionAlumno = "{{ App\Helpers\Enum\TiposCancelacionClase::CancelacionAlumno }}";
    </script>
    <script src='{{ asset('assets/eah/js/modulos/alumno/clase.js')}}'></script>
    @endif