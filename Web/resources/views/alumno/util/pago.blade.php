<div class="row">
    <div class="col-md-12">
        <div id="sec-mensajes-pago"></div>
        <div id="sec-pago-1">
            <div class="box-header">
                <a id="btn-nuevo-pago" type="button" class="btn btn-primary btn-sm pull-right">Nuevo pago</a>  
            </div>         
            <div class="box-body">
                <table id="tab-lista-pagos" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Motivo</th>     
                            <th>Monto</th>
                            <th>Fecha de registro</th>
                            <th>Estado</th>
                            <th class="col-md-1">&nbsp;</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        {{ Form::open(["url" => route("alumnos.pagos.registrar", ["id" => $idAlumno]), "id" => "formulario-pago", "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }}
        @include("partials/errors")
        <div id="sec-pago-2">
            <div class="box-header">
                <h3 class="box-title with-border">Nuevo pago</h3>                
            </div>  
            <div class="box-body">
                <div id="sec-pago-21">
                    <div class="form-group">
                        {{ Form::label("motivo", "Motivo (*): ", ["class" => "col-sm-2 control-label"]) }}
                        <div class="col-sm-3">
                            {{ Form::select("motivo", $motivosPago, null, ["id" => "motivo-pago", "class" => "form-control"]) }}
                        </div>                  
                    </div> 
                    <div class="form-group">
                        {{ Form::label("descripcion", "Descripción: ", ["class" => "col-sm-2 control-label"]) }}
                        <div class="col-sm-10">
                            {{ Form::text("descripcion", null, ["class" => "form-control", "maxlength" =>"255"]) }}
                        </div>
                    </div>
                    <div class="form-group">
                        {{ Form::label("imagenComprobante", "Imagen de comprobante: ", ["class" => "col-sm-2 control-label"]) }}
                        <div class="col-sm-4">
                            {{ Form::file("imagenComprobante", null) }}
                        </div>
                    </div> 
                    <div class="form-group">
                        {{ Form::label("monto", "Monto total (*): ", ["class" => "col-sm-2 control-label"]) }}
                        <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <b>S/.</b>
                                </span>
                                {{ Form::text("monto", null, ["id" => "monto-pago", "class" => "form-control", "maxlength" =>"19"]) }}
                            </div>
                        </div>
                        @if($totalSaldoFavor > 0)
                        <div class="col-sm-7">
                            <div class="checkbox">
                                <label class="checkbox-custom" data-initialize="checkbox">
                                    {{ Form::label("usarSaldoFavor", "Utilizar saldo a favor total (S/. " . number_format($totalSaldoFavor, 2, ".", ",") . ")", ["class" => "checkbox-label"]) }}
                                    {{ Form::checkbox("usarSaldoFavor", null, FALSE, ["id" => "usar-saldo-favor"]) }}
                                </label>
                            </div>
                        </div> 
                        @endIf
                    </div>                     
                    <div id="sec-pago-211">
                        <div class="form-group">  
                            {{ Form::label("costoHoraClase", "Costo por hora (*): ", ["class" => "col-sm-2 control-label"]) }}   
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <b>S/.</b>
                                    </span>
                                    {{ Form::text("costoHoraClase", number_format($costoHoraClase, 2, ".", ","), array("id" => "costo-hora-clase-pago", "class" => "form-control", "maxlength" =>"19")) }}
                                </div>
                            </div> 
                        </div>
                        <div class="form-group">
                            {{ Form::label("fechaInicioClases", "Inicio de clases (*): ", ["class" => "col-sm-2 control-label"]) }}
                            <div class="col-sm-3">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>                                
                                    {{ Form::text("fechaInicioClases", \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $fechaInicioClase)->format("d/m/Y"), ["id" => "fecha-inicio-clases-pago", "class" => "form-control  pull-right"]) }}
                                </div>
                            </div>

                            {{ Form::label("periodoClases", "Período (*): ", ["class" => "col-sm-2 control-label"]) }}
                            <div class="col-sm-2">
                                {{ Form::number("periodoClases", (((int) $numeroPeriodos) +1), ["id" => "periodo-clases-pago", "class" => "form-control", "maxlength" =>"11", "min" =>"1"]) }}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12">
                            <br/><span>(*) Campos obligatorios</span>
                        </div>
                    </div>
                </div>                
                <div id="sec-pago-22">
                    <div class="form-group">
                        <div class="col-sm-12"><br/>
                            <span>En base a los datos establecidos y el horario del alumno se ha determinado las fechas de las clases del período <b><span id="txt-periodo">{{ (((int) $numeroPeriodos) +1) }}</span></b>.
                        </div>                                        
                    </div>   
                    <div id="sec-lista-clases-pago" class="form-group">
                        <div class="col-sm-12">
                            <table class="table table-bordered sub-table">
                                <thead>
                                    <tr>
                                        <th class="text-center">N°</th>
                                        <th>Fecha</th>
                                        <th class="text-center">Horas</th>
                                        <th class="text-center">Notificar<br/><small>(Un día antes)</small></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div id="sec-saldo-favor-pago" class="col-sm-12"></div>
                    </div> 
                    <div class="form-group">
                        <div class="col-sm-12">
                            <button id="btn-docentes-disponibles-pago" type="button" class="btn btn-primary btn-sm">Elegir profesor disponible</button> 
                        </div>
                    </div>
                    <div id="sec-pago-221">
                        <div class="form-group">
                            {{ Form::label("", "", ["id" => "nombre-docente-pago", "class" => "col-sm-3 control-label"]) }}
                        </div>
                        <div class="form-group">
                            {{ Form::label("costoHoraDocente", "Pago por hora al profesor (*): ", ["class" => "col-sm-3 control-label"]) }}
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <b>S/.</b>
                                    </span>
                                    {{ Form::text("costoHoraDocente", null, ["id" => "costo-hora-docente-pago", "class" => "form-control", "maxlength" =>"19"]) }}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <br/><span>(*) Campos obligatorios</span>
                            </div>
                        </div>
                    </div>
                    {{ Form::hidden("idDocente") }} 
                    {{ Form::hidden("saldoFavor") }} 
                    {{ Form::hidden("datosNotificacionClases") }} 
                </div>
            </div> 
            <div class="box-footer">    
                <button id="btn-cancelar-pago" type="button" class="btn btn-default">Cancelar</button>
                <button id="btn-generar-clases-pago" type="button" class="btn btn-primary pull-right">Generar clases</button>
                <button id="btn-registrar-pago" type="submit" class="btn btn-success pull-right">Registrar pago</button>
                <button id="btn-anterior-pago"  type="button" class="btn btn-primary pull-right">Anterior</button>
            </div> 
        </div>
        {{ Form::close() }}
    </div>
</div>
@include("alumno.util.docentesDisponibles", ["seccion" => "pago"])
<div id="mod-datos-pago" class="modal" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Datos de pago</h4>
            </div>
            <div class="modal-body"> 
                <div class="row">
                    <div class="col-md-6">
                        <div id="sec-mensajes-mod-datos-pago"></div>
                        <div class="box-body">
                            <strong><i class="fa  fa-check-square margin-r-5"></i> Motivo</strong>
                            <p id="dat-motivo-pago" class="text-muted"></p>
                            <div id="sec-descripcion-pago">
                                <strong><i class="fa  fa-check-square margin-r-5"></i> Descripción</strong>
                                <p id="dat-descripcion-pago" class="text-muted"></p>
                            </div>
                            <strong><i class="fa  fa-check-square margin-r-5"></i> Monto</strong>
                            <p id="dat-monto-pago" class="text-muted"></p>
                            <strong><i class="fa  fa-check-square margin-r-5"></i> Estado</strong>
                            <p id="dat-estado-pago" class="text-muted"></p>   
                            <strong><i class="fa  fa-check-square margin-r-5"></i> Fecha registro</strong>
                            <p id="dat-fecha-registro-pago" class="text-muted"></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <a id="dat-imagen-comprobante-pago" href="{{ route('imagenes', ['rutaImagen' => '0']) }}" target="_blank">
                            <img class="img-responsive" src="{{ route('imagenes', ['rutaImagen' => '0']) }}" alt="Imagen comprobante"> 
                        </a>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var urlListarPagos = "{{ route('alumnos.pagos.listar', ['id' => $idAlumno]) }}";
    var urlEliminarPago = "{{ route('alumnos.pagos.eliminar', ['id' => $idAlumno, 'idPago' => 0]) }}";
    var urlGenerarClasesPago = "{{ route('alumnos.pagos.generarClases', ['id' => $idAlumno]) }}";
    var urlListarDocentesDisponiblesPago = "{{ route('alumnos.pagos.docentesDisponibles.listar', ['id' => $idAlumno]) }}";
    var urlPerfilProfesorPago = "{{ route('profesores.perfil', ['id' => 0]) }}";
    var urlDatosPago = "{{ route('alumnos.pagos.datos', ['id' => $idAlumno, 'idPago' => 0]) }}";
    var urlImagenesPago = "{{ route('imagenes', ['rutaImagen' => '0']) }}";
    
    var motivosPago = {!!  json_encode(App\Helpers\Enum\MotivosPago::Listar()) !!};
    var saldoFavorTotal = {{ ($totalSaldoFavor != "" ? $totalSaldoFavor : 0) }};
    var estadosPago = {!!  json_encode(App\Helpers\Enum\EstadosPago::Listar()) !!};
</script>
<script src='{{ asset('assets/eah/js/pago.js')}}'></script>