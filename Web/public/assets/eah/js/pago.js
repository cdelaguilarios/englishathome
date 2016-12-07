window.addEventListener("load", verificarJqueryPago, false);
function verificarJqueryPago() {
    ((window.jQuery && jQuery.ui) ? cargarPagos() : window.setTimeout(verificarJqueryPago, 100));
}

function cargarPagos() {
    urlListarPagos = (typeof (urlListarPagos) === "undefined" ? "" : urlListarPagos);
    urlEliminarPago = (typeof (urlEliminarPago) === "undefined" ? "" : urlEliminarPago);
    motivosPago = (typeof (motivosPago) === "undefined" ? "" : motivosPago);
    estadosPago = (typeof (estadosPago) === "undefined" ? "" : estadosPago);

    if (urlListarPagos !== "" && urlEliminarPago !== "" && motivosPago !== "" && estadosPago !== "") {
        $('#tab-lista-pagos').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": urlListarPagos,
                "type": "POST",
                "data": function (d) {
                    d._token = $("meta[name=_token]").attr("content");
                }
            },
            autoWidth: false,
            columns: [
                {data: "motivo", name: "motivo"},
                {data: "monto", name: "monto"},
                {data: "fechaRegistro", name: "fechaRegistro"},
                {data: "estado", name: "estado"},
                {data: "id", name: "id", orderable: false, searchable: false, width: "10%"}
            ],
            createdRow: function (r, d, i) {
                //Motivo              
                $("td", r).eq(0).html(motivosPago[d.motivo]);

                //Monto              
                $("td", r).eq(1).html('S/. ' + redondear(d.monto, 2) + (d.saldoFavor !== null && parseFloat(d.saldoFavor + "") > 0 ? '<br/><small><b>Saldo a favor de S/. ' + redondear(d.saldoFavor, 2) + (d.saldoFavorUtilizado !== null && d.saldoFavorUtilizado === 1 ? ' (<span class="saldo-favor-utilizado">utilizado</span>)' : '') + '</b></small>' : ''));

                //Fecha registro                
                $("td", r).eq(2).html(formatoFecha(d.fechaRegistro, true));

                //Estado
                $("td", r).eq(3).html('<span class="label ' + estadosPago[d.estado][1] + ' btn_estado">' + estadosPago[d.estado][0] + '</span>');

                //Botones
                var tBotones = '<ul class="buttons">' +
                        '<li>' +
                        '<a href="javascript:void(0)" onclick="verDatosPago(' + d.id + ');" title="Ver datos del pago"><i class="fa fa-eye"></i></a>' +
                        '</li>' +
                        '<li>' +
                        '<a href="javascript:void(0);" title="Eliminar pago" onclick="eliminarElemento(this, \'¿Está seguro que desea eliminar los datos de este pago?\', \'tab-lista-pagos\')" data-id="' + d.id + '" data-urleliminar="' + ((urlEliminarPago.replace("/0", "/" + d.id))) + '">' +
                        '<i class="fa fa-trash"></i>' +
                        '</a>' +
                        '</li>' +
                        '</ul>';
                $("td", r).eq(4).html(tBotones);
            }
        });
    }
    $("#formulario-pago").validate({
        ignore: ":hidden",
        rules: {
            motivo: {
                required: true
            },
            imagenComprobante: {
                validarImagen: true
            },
            monto: {
                required: true,
                validarDecimal: true
            },
            costoHoraClase: {
                required: true,
                validarDecimal: true
            },
            fechaInicioClases: {
                required: true
            },
            periodoClases: {
                required: true,
                validarEntero: true
            },
            costoHoraDocente: {
                required: true,
                validarDecimal: true
            }
        },
        submitHandler: function (form) {
            var datosNotificacionClases = [];
            $.each($("#sec-lista-clases-pago tbody tr"), function (e, v) {
                datosNotificacionClases.push({"notificar": $(v).find("input[type='checkbox']").is(":checked")});
            });
            $("input[name='datosNotificacionClases']").val(JSON.stringify(datosNotificacionClases));
            if (confirm($("#btn-guardar").text() === "Guardar"
                    ? "¿Está seguro que desea guardar los cambios de los datos del pago?"
                    : "¿Está seguro que desea registrar los datos de este pago?")) {
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

    mostrarSeccionPago([1]);
    establecerCalendario("fecha-inicio-clases-pago", false, true);
    $("#btn-nuevo-pago").click(function () {
        limpiarCamposPago();
        $("#btn-anterior-pago, #btn-registrar-pago").hide();
        $("#btn-generar-clases-pago").show();
        mostrarSeccionPago([2, 1, 1]);
    });
    $("#motivo-pago").change(function () {
        $("#btn-registrar-pago, #btn-generar-clases-pago").hide();
        if ($(this)[0].selectedIndex !== 0) {
            $("#btn-registrar-pago").show();
            mostrarSeccionPago([2, 1]);
        } else {
            $("#btn-generar-clases-pago").show();
            mostrarSeccionPago([2, 1, 1]);
        }
    });
    $("#periodo-clases-pago").change(function () {
        $("#txt-periodo").text($(this).val());
    });
    $("#monto-pago").change(function () {
        if (!$(this).valid()) {
            $("#usar-saldo-favor").attr("checked", false);
            $("#usar-saldo-favor").closest("label").removeClass("checked");
        }
    });
    $("#usar-saldo-favor").click(function (e) {
        saldoFavorTotal = (typeof (saldoFavorTotal) === "undefined" ? "" : saldoFavorTotal);
        if ($("#monto-pago").valid() && saldoFavorTotal !== "") {
            $("#monto-pago").val(parseFloat($("#monto-pago").val()) + (($(this).is(":checked")) ? saldoFavorTotal : -1 * saldoFavorTotal));
            $(this).attr("checked", $(this).is(":checked"));
        } else {
            e.stopPropagation();
            return false;
        }
    });
    $("#periodo-clases-pago").change(function () {
        $("#txt-periodo").text($(this).val());
    });
    $("#btn-anterior-pago").click(function () {
        $("#btn-anterior-pago, #btn-registrar-pago").hide();
        $("#btn-generar-clases-pago").show();
        mostrarSeccionPago([2, 1, 1]);
    });
    $("#btn-generar-clases-pago").click(generarClases);
    $("#btn-cancelar-pago").click(function () {
        mostrarSeccionPago([1]);
    });

    $("#btn-docentes-disponibles-pago").click(function () {
        cargarDocentesDisponiblesPago(false);
    });
    $("#tipo-docente-disponible-pago, #genero-docente-disponible-pago, #id-curso-docente-disponible-pago").change(function () {
        cargarDocentesDisponiblesPago(true);
    });
    $("#btn-confirmar-docente-disponible-pago").click(function () {
        urlPerfilProfesorPago = (typeof (urlPerfilProfesorPago) === "undefined" ? "" : urlPerfilProfesorPago);
        if (urlListarDocentesDisponiblesPago !== "" && urlPerfilProfesorPago !== "") {
            var docenteDisponiblePago = $("input[name='idDocenteDisponiblePago']:checked");
            limpiarCamposPago(true);
            mostrarSeccionPago([2, 2]);
            if (docenteDisponiblePago.length > 0) {
                $("input[name='idDocente']").val(docenteDisponiblePago.val());
                $("#nombre-docente-pago").html((docenteDisponiblePago.val() !== '' ? '<i class="fa flaticon-teach"></i> <b>' + docenteDisponiblePago.data('nombrecompleto') + '</b> <a href=' + (urlPerfilProfesorPago.replace('/0', '/' + docenteDisponiblePago.val())) + ' title="Ver perfil del profesor" target="_blank"><i class="fa fa-eye"></i></a>' : ''));
                mostrarSeccionPago([2, 2, 1]);
            }
        }
        $('#mod-docentes-disponibles-pago').modal("hide");
    });

    if ($("#formulario-pago .contenedor-alerta").length > 0) {
        $("a[href='#pago']").tab("show");
        $("#btn-anterior-pago, #btn-registrar-pago").hide();
        $("#btn-generar-clases-pago").show();
        mostrarSeccionPago([2, 1, 1]);
    }
}

function generarClases(e) {
    var camposFormularioPago = $("#formulario-pago").find(":input, select").not(":hidden, input[name='costoHoraDocente']");
    if (!camposFormularioPago.valid()) {
        e.preventDefault();
        return false;
    }

    urlGenerarClasesPago = (typeof (urlGenerarClasesPago) === "undefined" ? "" : urlGenerarClasesPago);
    var fDatos = $("#formulario-pago").serializeArray();
    var datos = {};
    datos["generarClases"] = "1";
    $(fDatos).each(function (i, o) {
        datos[o.name] = o.value;
    });

    if (urlGenerarClasesPago !== "") {
        $.blockUI({message: "<h4>Cargando...</h4>"});
        llamadaAjax(urlGenerarClasesPago, "POST", datos, true,
                function (d) {
                    $("#sec-lista-clases-pago tbody, #sec-saldo-favor-pago").html("");
                    $("input[name='saldoFavor']").val("");
                    $.each(d, function (i, v) {
                        if (i !== "montoRestante") {
                            var tiempoAdicionalMinutos = (v.tiempoAdicional > 0 ? v.tiempoAdicional / 60 : 0);
                            var tiempoAdicionalHoras = ((tiempoAdicionalMinutos > 0 && tiempoAdicionalMinutos >= 60) ? tiempoAdicionalMinutos / 60 : 0);
                            var tiempoAdicional = (v.tiempoAdicional > 0 ? ' <small><b>(Se le descontó ' +
                                    (tiempoAdicionalHoras > 0 ? (tiempoAdicionalHoras + (tiempoAdicionalHoras > 1 ? ' horas' : ' hora'))
                                            : (tiempoAdicionalMinutos + (tiempoAdicionalMinutos > 1 ? ' minutos' : 'minuto'))) + ')</b></small>'
                                    : '');

                            $("#sec-lista-clases-pago tbody").append('<tr>' +
                                    '<td class="text-center">' + (parseInt(i) + 1) + '</td>' +
                                    '<td><b>' + formatoFecha(v.fechaInicio.date) + '</b> - De ' + formatoFecha(v.fechaInicio.date, false, true) + ' a ' + formatoFecha(v.fechaFin.date, false, true) + '</td>' +
                                    '<td class="text-center">' + formatoHora(v.duracion) + tiempoAdicional + '</td>' +
                                    '<td class="text-center"><input type="checkbox" name="notificarClasePago_' + (parseInt(i) + 1) + '"' + (v.idProfesor !== '' ? '' : ' checked="checked"') + '/></td>' +
                                    '</tr>');
                        } else if (v > 0) {
                            $("#sec-saldo-favor-pago").html('<span>El alumno tiene un saldo a favor de <b>S/. ' + redondear(v, 2) + '</b></span>');
                            $("input[name='saldoFavor']").val(v);
                        }
                    });
                    if ($("#sec-lista-clases-pago tbody").html() !== "") {
                        limpiarCamposPago(true);
                        $("#btn-generar-clases-pago").hide();
                        $("#btn-anterior-pago, #btn-registrar-pago").show();
                        mostrarSeccionPago([2, 2]);
                    }
                    $("body").unblock();
                },
                function (d) {
                },
                function (de) {
                    $('body').unblock({
                        onUnblock: function () {
                            agregarMensaje("errores",
                                    ((de.responseJSON !== undefined && de.responseJSON["mensaje"] !== undefined) ?
                                            de["responseJSON"]["mensaje"] :
                                            "Ocurrió un problema durante la generación de clases. Por favor inténtelo nuevamente."), true, "#sec-mensajes-pago");
                        }
                    });
                }
        );
    }
}
function cargarDocentesDisponiblesPago(recargarLista) {
    var camposFormularioPago = $("#formulario-pago").find(":input, select").not(":hidden, input[name='costoHoraDocente']");
    if (!camposFormularioPago.valid()) {
        return false;
    }

    $("#mod-docentes-disponibles-pago").modal("show");
    if ($.fn.DataTable.isDataTable("#tab-lista-docentes-pago")) {
        if (recargarLista) {
            $("#tab-lista-docentes-pago").DataTable().ajax.reload();
        }
    } else {
        urlListarDocentesDisponiblesPago = (typeof (urlListarDocentesDisponiblesPago) === "undefined" ? "" : urlListarDocentesDisponiblesPago);
        urlPerfilProfesorPago = (typeof (urlPerfilProfesorPago) === "undefined" ? "" : urlPerfilProfesorPago);
        if (urlListarDocentesDisponiblesPago !== "" && urlPerfilProfesorPago !== "") {
            $("#tab-lista-docentes-pago").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: urlListarDocentesDisponiblesPago,
                    type: "POST",
                    data: function (d) {
                        d.docentesDisponibles = "1";
                        d.tipoDocente = $("#tipo-docente-disponible-pago").val();
                        d.generoDocente = $("#genero-docente-disponible-pago").val();
                        d.idCursoDocente = $("#id-curso-docente-disponible-pago").val();

                        var fDatos = $("#formulario-pago").serializeArray();
                        $(fDatos).each(function (i, o) {
                            d[o.name] = o.value;
                        });
                    }
                },
                autoWidth: false,
                columns: [
                    {data: "nombreCompleto", name: "nombreCompleto"},
                    {data: "id", name: "id", orderable: false, "searchable": false, width: "10%"}
                ],
                createdRow: function (r, d, i) {
                    //Motivo              
                    $("td", r).eq(0).html(d.nombreCompleto + ' <a href=' + (urlPerfilProfesorPago.replace('/0', '/' + d.id)) + ' title="Ver perfil del profesor" target="_blank"><i class="fa fa-eye"></i></a>');

                    //Elegir
                    $("td", r).eq(1).addClass('text-center');
                    $("td", r).eq(1).html('<input type="radio" name="idDocenteDisponiblePago" value="' + d.id + '" data-nombrecompleto="' + d.nombreCompleto + '"' + (i === 0 ? ' checked="checked"' : '') + '>');
                }
            });
        }
    }
}
function verDatosPago(idPago) {
    urlDatosPago = (typeof (urlDatosPago) === "undefined" ? "" : urlDatosPago);
    motivosPago = (typeof (motivosPago) === "undefined" ? "" : motivosPago);
    urlImagenesPago = (typeof (urlImagenesPago) === "undefined" ? "" : urlImagenesPago);
    estadosPago = (typeof (estadosPago) === "undefined" ? "" : estadosPago);

    if (urlDatosPago !== "" && motivosPago !== "" && urlImagenesPago !== "" && estadosPago !== "") {
        $.blockUI({message: "<h4>Cargando...</h4>", baseZ: 2000});
        llamadaAjax(urlDatosPago.replace("/0", "/" + idPago), "POST", {}, true,
                function (d) {
                    $("#sec-descripcion-pago").hide();
                    if (d.descripcion !== null && d.descripcion.trim() !== "") {
                        $("#sec-descripcion-pago").show();
                    }
                    $("#dat-motivo-pago").text(motivosPago[d.motivo]);
                    $("#dat-descripcion-pago").text(d.descripcion);
                    $("#dat-monto-pago").html('S/. ' + redondear(d.monto, 2) + (d.saldoFavor !== null && parseFloat(d.saldoFavor + "") > 0 ? '<br/><small><b>Saldo a favor de S/. ' + redondear(d.saldoFavor, 2) + (d.saldoFavorUtilizado !== null && d.saldoFavorUtilizado === 1 ? ' (<span class="saldo-favor-utilizado">utilizado</span>)' : '') + '</b></small>' : ''));
                    $("#dat-estado-pago").html('<span class="label ' + estadosPago[d.estado][1] + ' btn_estado">' + estadosPago[d.estado][0] + '</span>');
                    $("#dat-fecha-registro-pago").text(formatoFecha(d.fechaRegistro, true));
                    if (d.rutaImagenComprobante !== "") {
                        var rutaImagen = urlImagenesPago.replace("/0", "/" + d.rutaImagenComprobante);
                        $("#dat-imagen-comprobante-pago").attr("href", rutaImagen);
                        $("#dat-imagen-comprobante-pago").find("img").attr("src", rutaImagen);
                    }
                    $("#mod-datos-pago").modal("show");
                    $("body").unblock();
                },
                function (d) {
                },
                function (de) {
                    $('body').unblock({
                        onUnblock: function () {
                            agregarMensaje("errores",
                                    ((de.responseJSON !== undefined && de.responseJSON["mensaje"] !== undefined) ?
                                            de["responseJSON"]["mensaje"] :
                                            "Ocurrió un problema durante la carga de datos del pago seleccionado. Por favor inténtelo nuevamente."), true, "#sec-mensajes-mod-datos-pago");
                        }
                    });
                }
        );
    }
}

//Util
function mostrarSeccionPago(numSecciones) {
    $("[id*='sec-pago']").hide();
    var auxSec = "";
    for (var i = 0; i < numSecciones.length; i++) {
        $("#sec-pago-" + auxSec + "" + numSecciones[i]).show();
        auxSec += "" + numSecciones[i];
    }
}
function limpiarCamposPago(soloCamposDocente) {
    $("input[name='idDocente']").val("");
    $("#nombre-docente-pago").html("");

    if (!soloCamposDocente) {
        $("#formulario-pago input, #formulario-pago select").each(function (i, e) {
            if (e.name !== "costoHoraClase" && e.name !== "fechaInicioClases" && e.name !== "periodoClases" && e.name !== "_token" && e.type !== "hidden") {
                if ($(e).is("select")) {
                    $(e).prop("selectedIndex", 0);
                } else {
                    e.value = "";
                }
            }
        });
    }
} 