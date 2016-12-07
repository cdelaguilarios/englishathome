window.addEventListener("load", verificarJqueryPeriodo, false);
function verificarJqueryPeriodo() {
    ((window.jQuery && jQuery.ui) ? cargarPeriodos() : window.setTimeout(verificarJqueryPeriodo, 100));
}

function  cargarPeriodos() {
    urlListarPeriodos = (typeof (urlListarPeriodos) === "undefined" ? "" : urlListarPeriodos);
    if (urlListarPeriodos !== "") {
        $('#tab-lista-periodos-clases').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": urlListarPeriodos,
                "type": "POST",
                "data": function (d) {
                    d._token = $('meta[name=_token]').attr("content");
                }
            },
            autoWidth: false,
            order: [[0, "desc"]],
            columns: [
                {data: 'numeroPeriodo', name: 'numeroPeriodo'},
                {data: 'fechaInicio', name: 'fechaInicio'},
                {data: 'fechaFin', name: 'fechaFin'},
                {data: 'numeroHorasTotal', name: 'numeroHorasTotal'},
                {data: 'numeroPeriodo', name: 'numeroPeriodo', orderable: false, "searchable": false}
            ],
            "createdRow": function (r, d, i) {
                var fechaActual = new Date();
                var fechaInicioSel = new Date(d.fechaInicio);
                var fechaFinSel = new Date(d.fechaFin);

                //Código                        
                $('td', r).eq(0).addClass('text-center');
                $('td', r).eq(0).html(d.numeroPeriodo + ((fechaActual >= fechaInicioSel && fechaActual <= fechaFinSel) ? ' (Actual)' : ''));

                //Fecha de inicio
                $('td', r).eq(1).html(formatoFecha(d.fechaInicio));

                //Fecha de fin
                $('td', r).eq(2).html(formatoFecha(d.fechaFin));

                //Numero horas total
                $('td', r).eq(3).html(formatoHora(d.numeroHorasTotal));

                //Opciones
                $('td', r).eq(4).html('<a href="javascript:void(0);" onclick="mostrarOcultarClases(this);" class="btn btn-primary btn-xs" data-periodo="' + d.numeroPeriodo + '"><i class="fa fa-eye"></i> Ver clases</button>');
            }
        });
    }
    mostrarSeccionClase([1]);
    establecerCalendario("fechaClaseReprogramada", false, true);
    //Registrar clase
    $("#btn-nuevo-clase, #btnAnteriorClase").click(function () {
        if ($(this).attr("id").indexOf("Nuevo") !== -1) {
            limpiarCamposClase();
        }
        $("#btnAnteriorClase, #btnRegistrarClase").hide();
        $("#btnSiguienteClase").show();
        mostrarSeccionClase([2, 1]);
    });
    $("#btnSiguienteClase").click(function () {
        $("#btnSiguienteClase").hide();
        $("#btnAnteriorClase, #btnRegistrarClase").show();
        mostrarSeccionPago([2, 2]);
    });
    //Cancelar clase
    $("#btnAnteriorCanClase").click(function () {
        $("#btnAnteriorCanClase, #btnRegistrarCanClase").hide();
        $("#btnSiguienteCanClase").show();
        mostrarSeccionClase([3, 1]);
    });
    $("#btnSiguienteCanClase").click(function () {
        $("#btnSiguienteCanClase").hide();
        $("#btnAnteriorCanClase, #btnRegistrarCanClase").show();
        mostrarSeccionPago([3, 2]);
    });
    //Cancelar registros
    $("#btnCancelarClase, #btnCancelarCanClase").click(function () {
        mostrarSeccionClase([1]);
    });

    $("#btnDocentesDisponiblesClase").click(function () {
        cargarDocentesDisponiblesClase(false);
    });
    $("#generoDocenteDisponibleClase, #idCursoDocenteDisponibleClase, #tipoDocenteDisponibleClase").change(function () {
        cargarDocentesDisponiblesClase(true);
    });
}

function mostrarOcultarClases(elemento, forzarMostrar) {
    var tr = $(elemento).closest('tr');
    var fila = $("#tab-lista-periodos-clases").DataTable().row(tr);
    if (fila.child.isShown() && !forzarMostrar) {
        fila.child.hide();
        tr.find("a:eq(0)").html(tr.find("a:eq(0)").html().replace('<i class="fa fa-eye-slash"></i> Ocultar', '<i class="fa fa-eye"></i> Ver'));
    } else {
        listarClases(tr, fila, fila.data());
    }
}
function listarClases(tr, fila, datosFila) {
    urlListarClases = (typeof (urlListarClases) === "undefined" ? "" : urlListarClases);
    urlEliminarClase = (typeof (urlEliminarClase) === "undefined" ? "" : urlEliminarClase);
    urlPerfilProfesorClase = (typeof (urlPerfilProfesorClase) === "undefined" ? "" : urlPerfilProfesorClase);
    estadosClase = (typeof (estadosClase) === "undefined" ? "" : estadosClase);

    if (urlListarClases !== "" && urlEliminarClase !== "" && urlPerfilProfesorClase !== "" && estadosClase !== "") {
        $.blockUI({message: '<h4>Cargando...</h4>'});
        var numeroPeriodo = datosFila.numeroPeriodo;
        llamadaAjax(((urlListarClases.replace("/0", "/" + numeroPeriodo))), "POST", {}, true,
                function (d) {
                    if (d.length > 0) {
                        var htmlListaClases = "";
                        for (var i = 0; i < d.length; i++) {
                            htmlListaClases +=
                                    '<tr>' +
                                    '<td>' + (i + 1) + '</td>' +
                                    '<td>' +
                                    '<b>Fecha:</b> ' + formatoFecha(d[i].fechaInicio) + ' - De ' + formatoFecha(d[i].fechaInicio, false, true) + ' a ' + formatoFecha(d[i].fechaFin, false, true) + '<br/>'
                                    + '<b>Duración:</b> ' + formatoHora(d[i].duracion) + '<br/>'
                                    + '<b>Profesor:</b> ' + (d[i].idProfesor !== null ? '<a target="_blank" href="' + urlPerfilProfesorClase.replace("/0", "/" + d[i].idProfesor) + '">' + d[i].nombreProfesor + ' ' + d[i].apellidoProfesor + '</a>' : 'Sin profesor asignado') +
                                    '</td>' +
                                    '<td>' +
                                    '<input type="checkbox" disabled="disabled"' + (d[i].idHistorial !== null ? ' checked="checked"' : '') + '/>' +
                                    '</td>' +
                                    '<td>' +
                                    '<span class="label ' + estadosClase[d[i].estado][1] + ' btn_estado">' + estadosClase[d[i].estado][0] + '</span>' +
                                    '</td>' +
                                    '<td>' +
                                    '<ul class="buttons">' +
                                    '<li>' +
                                    '<a href="javascript:void(0);" onclick="cancelarClase(' + d[i].id + ');" title="Cancelar clase"><i class="fa fa-remove"></i></a>' +
                                    '</li>' +
                                    "<li>" +
                                    '<a href="javascript:void(0);" title="Eliminar clase" onclick="eliminarElemento(this, \'¿Está seguro que desea eliminar los datos de esta clase?\', null, true, function(){mostrarOcultarClases($(\'a[data-periodo=' + d[i].numeroPeriodo + ']\'), true);})" data-id="' + d[i].id + '" data-urleliminar="' + ((urlEliminarClase.replace('/0', '/' + d[i].id))) + '">' +
                                    "<i class='fa fa-trash'></i>" +
                                    "</a>" +
                                    "</li>" +
                                    '</ul>' +
                                    '</td>' +
                                    '</tr>';
                        }
                        $('body').unblock({
                            onUnblock: function () {
                                fila.child('<div class="box-body">' +
                                        '<div id="secMensajesPeriodo' + d[0].numeroPeriodo + '"></div>' +
                                        '<table id="tab_lista_clases_' + d[0].numeroPeriodo + '" class="table table-bordered sub-table">' +
                                        '<thead>' +
                                        '<tr>' +
                                        '<th>N°</th>' +
                                        '<th>Datos</th>' +
                                        '<th class="col-md-1">Notificar</th>' +
                                        '<th>Estado</th>' +
                                        '<th></th>' +
                                        '</tr>' +
                                        '</thead>' +
                                        '<tbody>' + htmlListaClases + '</tbody>' +
                                        '</table>' +
                                        '</div>').show();
                                tr.find("a:eq(0)").html(tr.find("a:eq(0)").html().replace('<i class="fa fa-eye"></i> Ver', '<i class="fa fa-eye-slash"></i> Ocultar'));
                                $('#tab_lista_clases_' + d[0].numeroPeriodo).DataTable({
                                    paginate: false,
                                    columnDefs: [
                                        {targets: [2, 4], orderable: false, searchable: false}
                                    ]
                                });
                            }
                        });
                    } else {
                        $('body').unblock();
                    }
                },
                function (d) {
                },
                function (de) {
                    $('body').unblock({
                        onUnblock: function () {
                            agregarMensaje("errores",
                                    ((de.responseJSON !== undefined && de.responseJSON["mensaje"] !== undefined) ?
                                            de["responseJSON"]["mensaje"] :
                                            "Ocurrió un problema durante la carga de lista de clases del período seleccionado. Por favor inténtelo nuevamente."), true, "#sec-mensajes-clase");
                        }
                    });
                }
        );
    }
}

function cancelarClase(idClase) {
    limpiarCamposCancelacionClase();
    $("#btnAnteriorCanClase, #btnRegistrarCanClase").hide();
    $("#btnSiguienteCanClase").show();
    mostrarSeccionClase([3, 1]);
}

//Util
function cargarDocentesDisponiblesClase(recargarLista) {
    var camposFormularioCancelarClase = $("#formulario-cancelar-clase").find("#fechaClaseReprogramada");
    if (!camposFormularioCancelarClase.valid()) {
        return false;
    }

    $('#modDocentesDisponiblesClase').modal('show');
    if ($.fn.DataTable.isDataTable('#tab_lista_docentes_clase')) {
        if (recargarLista) {
            $('#tab_lista_docentes_clase').DataTable().ajax.reload();
        }
    } else {
        urlListarDocentesDisponiblesClase = (typeof (urlListarDocentesDisponiblesClase) === "undefined" ? "" : urlListarDocentesDisponiblesClase);
        urlPerfilProfesorClase = (typeof (urlPerfilProfesorClase) === "undefined" ? "" : urlPerfilProfesorClase);
        if (urlListarDocentesDisponiblesClase !== "" && urlListarDocentesDisponiblesClase !== "") {
            $('#tab_lista_docentes_clase').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": urlListarDocentesDisponiblesClase,
                    "type": "POST",
                    "data": function (d) {
                        d.docentesDisponibles = "1";
                        d._token = $('meta[name=_token]').attr("content");
                        d.generoDocenteClase = $("#generoDocenteDisponibleClase").val();
                        d.idCursoDocenteClase = $("#idCursoDocenteDisponibleClase").val();
                        d.tipoDocenteClase = $("#tipoDocenteDisponibleClase").val();
                        d.fechaClaseReprogramada = $("#fechaClaseReprogramada").val();
                        d.horaInicioClaseReprogramada = "09:00:00";
                        d.duracionClaseReprogramada = "2";
                    }
                },
                autoWidth: false,
                columns: [
                    {data: 'nombreCompleto', name: 'nombreCompleto'},
                    {data: 'id', name: 'id', orderable: false, "searchable": false, width: "10%"}
                ],
                "createdRow": function (r, d, i) {
                    //Motivo              
                    $('td', r).eq(0).html(d.nombreCompleto + ' <a href=' + (urlPerfilProfesorClase.replace("/0", "/" + d.id)) + ' title="Ver perfil del profesor" target="_blank"><i class="fa fa-eye"></i></a>');

                    //Elegir
                    $('td', r).eq(1).addClass('text-center');
                    $('td', r).eq(1).html('<input type="radio" name="idDocenteDisponibleClase" value="' + d.id + '" data-nombrecompleto="' + d.nombreCompleto + '"' + (i === 0 ? ' checked="checked"' : '') + '>');
                }
            });
        }
    }
}
function mostrarSeccionClase(numSecciones) {
    $('[id*="sec-clase-"]').hide();
    var auxSec = "";
    for (var i = 0; i < numSecciones.length; i++) {
        $("#sec-clase-" + auxSec + "" + numSecciones[i]).show();
        auxSec += "" + numSecciones[i];
    }
}
function limpiarCamposClase() {

}
function limpiarCamposCancelacionClase() {

}