window.addEventListener("load", verificarJqueryClase, false);
function verificarJqueryClase() {
    ((window.jQuery && jQuery.ui) ? cargarSeccionClases() : window.setTimeout(verificarJqueryClase, 100));
}
function  cargarSeccionClases() {
    cargarListaClase();
    cargarFormularioClase();
    cargarFormularioCancelarClase();
    mostrarSeccionClase();

    //Común    
    $(".btn-docentes-disponibles-clase").click(cargarDocentesDisponiblesClase);
    $("#genero-docente-disponible-clase, #id-curso-docente-disponible-clase, #tipo-docente-disponible-clase").change(function () {
        cargarDocentesDisponiblesClase(true);
    });
    $("#btn-confirmar-docente-disponible-clase").click(function () {
        urlPerfilProfesorClase = (typeof (urlPerfilProfesorClase) === "undefined" ? "" : urlPerfilProfesorClase);
        if (urlPerfilProfesorClase !== "") {
            var docenteDisponibleClase = $("input[name='idDocenteDisponibleClase']:checked");
            limpiarCamposClase(true);
            if (docenteDisponibleClase.length > 0) {
                var esFormularioCancelar = ($("#formulario-cancelar-clase").is(":visible"));
                $("#id-docente-clase-" + (esFormularioCancelar ? "reprogramada" : "registrar")).val(docenteDisponibleClase.val());
                $(".nombre-docente-clase").html((docenteDisponibleClase.val() !== '' ? '<i class="fa flaticon-teach"></i> <b>' + docenteDisponibleClase.data('nombrecompleto') + '</b> <a href=' + (urlPerfilProfesorClase.replace('/0', '/' + docenteDisponibleClase.val())) + ' title="Ver perfil del profesor" target="_blank"><i class="fa fa-eye"></i></a>' : ''));
                if (!esFormularioCancelar) {
                    mostrarSeccionClase([2, 1]);
                }
            }
            verificarSeccionReprogramarClase();
        }
        $('#mod-docentes-disponibles-clase').modal("hide");
    });
    $(".btn-cancelar-clase").click(function () {
        mostrarSeccionClase();
    });
}

//Lista
function cargarListaClase() {
    urlListarPeriodos = (typeof (urlListarPeriodos) === "undefined" ? "" : urlListarPeriodos);
    if (urlListarPeriodos !== "") {
        $("#tab-lista-periodos-clases").DataTable({
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
                {data: "numeroPeriodo", name: "numeroPeriodo"},
                {data: "fechaInicio", name: "fechaInicio"},
                {data: "fechaFin", name: "fechaFin"},
                {data: "horasTotal", name: "horasTotal"},
                {data: "numeroPeriodo", name: "numeroPeriodo", orderable: false, searchable: false}
            ],
            "createdRow": function (r, d, i) {
                var fechaActual = new Date();
                var fechaInicioSel = new Date(d.fechaInicio);
                var fechaFinSel = new Date(d.fechaFin);

                //Código                        
                $("td", r).eq(0).html(d.numeroPeriodo + ((fechaActual >= fechaInicioSel && fechaActual <= fechaFinSel) ? " (Actual)" : ""));

                //Fecha de inicio
                $("td", r).eq(1).html(formatoFecha(d.fechaInicio));

                //Fecha de fin
                $("td", r).eq(2).html(formatoFecha(d.fechaFin));

                //Horas total
                $("td", r).eq(3).html(formatoHora(d.horasTotal));

                //Opciones
                $('td', r).eq(4).html('<a href="javascript:void(0);" onclick="mostrarOcultarClases(this);" class="btn btn-primary btn-xs" data-periodo="' + d.numeroPeriodo + '"><i class="fa fa-eye"></i> Ver clases</button>');
            }
        });
    }
}
function mostrarOcultarClases(elemento, forzarCargaClases) {
    var tr = $(elemento).closest("tr");
    var fila = $("#tab-lista-periodos-clases").DataTable().row(tr);
    if (fila.child.isShown() && !forzarCargaClases) {
        fila.child.hide();
        tr.find('a:eq(0)').html(tr.find('a:eq(0)').html().replace('<i class="fa fa-eye-slash"></i> Ocultar', '<i class="fa fa-eye"></i> Ver'));
    } else {
        listarClases(tr, fila, fila.data());
    }
}
function listarClases(tr, fila, datosFila) {
    urlListarClases = (typeof (urlListarClases) === "undefined" ? "" : urlListarClases);
    urlEliminarClase = (typeof (urlEliminarClase) === "undefined" ? "" : urlEliminarClase);
    urlPerfilProfesorClase = (typeof (urlPerfilProfesorClase) === "undefined" ? "" : urlPerfilProfesorClase);
    estadosClase = (typeof (estadosClase) === "undefined" ? "" : estadosClase);
    estadosClaseProgramada = (typeof (estadosClaseProgramada) === "undefined" ? "" : estadosClaseProgramada);

    if (urlListarClases !== "" && urlEliminarClase !== "" && urlPerfilProfesorClase !== "" && estadosClase !== "" && estadosClaseProgramada !== "") {
        $.blockUI({message: "<h4>Cargando...</h4>"});
        llamadaAjax(((urlListarClases.replace("/0", "/" + datosFila.numeroPeriodo))), "POST", {}, true,
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
                                    (d[i].estado !== estadosClaseProgramada ? '' :
                                            '<li>' +
                                            '<a href="javascript:void(0);" onclick="cancelarClase(' + d[i].id + ', ' + d[i].idProfesor + ', \'' + d[i].fechaInicio + '\', ' + d[i].duracion + ');" title="Cancelar clase"><i class="fa fa-remove"></i></a>' +
                                            '</li>') +
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
                                        '<div id="sec-mensajes-periodo-' + d[0].numeroPeriodo + '"></div>' +
                                        '<table id="tab-lista-clases-' + d[0].numeroPeriodo + '" class="table table-bordered sub-table">' +
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
                                tr.find('a:eq(0)').html(tr.find('a:eq(0)').html().replace('<i class="fa fa-eye"></i> Ver', '<i class="fa fa-eye-slash"></i> Ocultar'));
                                $("#tab-lista-clases-" + d[0].numeroPeriodo).DataTable({
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

//Formulario
function cargarFormularioClase() {
    $("#formulario-registrar-clase").validate({
        ignore: ":hidden",
        rules: {
            fecha: {
                required: true
            },
            horaInicio: {
                required: true,
                validarDecimal: true,
                range: [(minHorario * 3600), (maxHorario * 3600)]
            },
            duracion: {
                required: true,
                validarDecimal: true,
                range: [(minHorasClase * 3600), (maxHorasClase * 3600)]
            },
            costoHora: {
                required: true,
                validarDecimal: true
            },
            costoHoraDocente: {
                required: true,
                validarDecimal: true
            },
            numeroPeriodo: {
                required: true,
                validarEntero: true
            }
        },
        submitHandler: function (form) {
            if (confirm("¿Está seguro que desea registrar esta clase?")) {
                form.submit();
            }
        },
        highlight: function () {
        },
        unhighlight: function () {
        },
        errorElement: "div",
        errorClass: "help-block-error",
        errorPlacement: function (error, element) {
            if (element.closest("div[class*=col-sm-]").length > 0) {
                element.closest("div[class*=col-sm-]").append(error);
            } else if (element.parent(".input-group").length) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        }
    });
    //Registrar
    establecerCalendario("fecha-clase", false, true);
    establecerCampoHorario("hora-inicio-clase");
    establecerCampoDuracion("duracion-clase");
    $("#btn-nuevo-clase").click(function () {
        limpiarCamposClase();
        mostrarSeccionClase([2]);
    });
}

//Formulario Cancelar
function cargarFormularioCancelarClase() {
    $("#formulario-cancelar-clase").validate({
        ignore: ":hidden",
        rules: {
            pagoProfesor: {
                required: true,
                validarDecimal: true
            },
            fecha: {
                required: true
            },
            horaInicio: {
                required: true,
                validarDecimal: true,
                range: [(minHorario * 3600), (maxHorario * 3600)]
            },
            duracion: {
                required: true,
                validarDecimal: true,
                range: [(minHorasClase * 3600), (maxHorasClase * 3600)]
            },
            costoHoraDocente: {
                required: true,
                validarDecimal: true
            }
        },
        submitHandler: function (form) {
            if (confirm("¿Está seguro que desea cancelar esta clase?")) {
                form.submit();
            }
        },
        highlight: function () {
        },
        unhighlight: function () {
        },
        errorElement: "div",
        errorClass: "help-block-error",
        errorPlacement: function (error, element) {
            if (element.closest("div[class*=col-sm-]").length > 0) {
                element.closest("div[class*=col-sm-]").append(error);
            } else if (element.parent(".input-group").length) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        }
    });
    establecerCalendario("fecha-clase-reprogramada", false, true);
    $("#tipo-cancelacion-clase").change(function () {
        tipoCancelacionAlumno = (typeof (tipoCancelacionAlumno) === "undefined" ? "" : tipoCancelacionAlumno);
        (($(this).val() === tipoCancelacionAlumno) ? mostrarSeccionClase([3, 1]) : mostrarSeccionClase([3, 2]));
        verificarSeccionReprogramarClase();
    });
    $("#reprogramar-clase-can-alu, #reprogramar-clase-can-pro").change(verificarSeccionReprogramarClase);
}
function cancelarClase(idClase, idProfesor, fechaInicio, duracionClase) {
    limpiarCamposClase();
    mostrarSeccionClase([3, 1, 1]);
    $("input[name='idClase']").val(idClase);
    if (idProfesor !== null) {
        $("input[name='idProfesorClaseCancelada']").val(idClase);
    }
    establecerCampoHorario("hora-inicio-clase-reprogramada", tiempoSegundos(fechaInicio));
    establecerCampoDuracion("duracion-clase-reprogramada", duracionClase);
}
function verificarSeccionReprogramarClase() {
    var repClaseAlu = $("#reprogramar-clase-can-alu");
    var repClasePro = $("#reprogramar-clase-can-pro");
    (((repClaseAlu.is(":visible") && repClaseAlu.is(":checked")) || (repClasePro.is(":visible") && repClasePro.is(":checked"))) ? $("#sec-clase-33").show() : $("#sec-clase-33").hide());
    (($("#sec-clase-33").is(":visible") && $("input[name='idDocente']").val() !== "") ? $("#sec-clase-331").show() : $("#sec-clase-331").hide());
}

//Común - Util
function cargarDocentesDisponiblesClase(recargarListaClase) {
    var formulario = ($("#formulario-cancelar-clase").is(":visible") ? $("#formulario-cancelar-clase") : $("#formulario-registrar-clase"));

    var camposFormularioCancelarClase = formulario.find(":input, select").not(":hidden, input[name='pagoProfesor'], input[name='costoHoraDocente'], input[name='costoHora'], input[name='numeroPeriodo']");
    if (!camposFormularioCancelarClase.valid()) {
        return false;
    }

    $('#mod-docentes-disponibles-clase').modal('show');
    if ($.fn.DataTable.isDataTable('#tab-lista-docentes-clase')) {
        if (recargarListaClase) {
            $('#tab-lista-docentes-clase').DataTable().ajax.reload();
        }
    } else {
        urlListarDocentesDisponiblesClase = (typeof (urlListarDocentesDisponiblesClase) === "undefined" ? "" : urlListarDocentesDisponiblesClase);
        urlPerfilProfesorClase = (typeof (urlPerfilProfesorClase) === "undefined" ? "" : urlPerfilProfesorClase);
        if (urlListarDocentesDisponiblesClase !== "" && urlPerfilProfesorClase !== "") {
            $('#tab-lista-docentes-clase').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": urlListarDocentesDisponiblesClase,
                    "type": "POST",
                    "data": function (d) {
                        d.tipoDocente = $("#tipo-docente-disponible-clase").val();
                        d.generoDocente = $("#genero-docente-disponible-clase").val();
                        d.idCursoDocente = $("#id-curso-docente-disponible-clase").val();

                        var fDatos = formulario.serializeArray();
                        $(fDatos).each(function (i, o) {
                            d[o.name] = o.value;
                        });
                    }
                },
                autoWidth: false,
                columns: [
                    {data: 'nombreCompleto', name: 'nombreCompleto'},
                    {data: 'id', name: 'id', orderable: false, "searchable": false, width: "10%"}
                ],
                "createdRow": function (r, d, i) {
                    //Nombre completo               
                    $('td', r).eq(0).html(d.nombreCompleto + ' <a href=' + (urlPerfilProfesorClase.replace("/0", "/" + d.id)) + ' title="Ver perfil del profesor" target="_blank"><i class="fa fa-eye"></i></a>');

                    //Opciones
                    $('td', r).eq(1).html('<input type="radio" name="idDocenteDisponibleClase" value="' + d.id + '" data-nombrecompleto="' + d.nombreCompleto + '"' + (i === 0 ? ' checked="checked"' : '') + '>');
                }
            });
        }
    }
}
function mostrarSeccionClase(numSecciones) {
    if (!numSecciones) {
        numSecciones = [1];
    }
    $('[id*="sec-clase-"]').hide();
    var auxSec = "";
    for (var i = 0; i < numSecciones.length; i++) {
        $("#sec-clase-" + auxSec + "" + numSecciones[i]).show();
        auxSec += "" + numSecciones[i];
    }
    verificarSeccionReprogramarClase();
}
function limpiarCamposClase(soloCamposDocente) {
    $("input[name='idDocente']").val("");
    $(".nombre-docente-pago").html("");

    if (!soloCamposDocente) {

    }
}