window.addEventListener("load", verificarJqueryPago, false);
function verificarJqueryPago() {
  ((window.jQuery && jQuery.ui) ? cargarSeccionPagos() : window.setTimeout(verificarJqueryPago, 100));
}
var saldoFavorTotal = 0;
function cargarSeccionPagos() {
  urlPerfilProfesor = (typeof (urlPerfilProfesor) === "undefined" ? "" : urlPerfilProfesor);
  motivosPago = (typeof (motivosPago) === "undefined" ? "" : motivosPago);
  cuentasBanco = (typeof (cuentasBanco) === "undefined" ? "" : cuentasBanco);
  estadosPago = (typeof (estadosPago) === "undefined" ? "" : estadosPago);
  urlArchivos = (typeof (urlArchivos) === "undefined" ? "" : urlArchivos);

  cargarListaPago();
  cargarFormularioPago();
  cargarFormularioActualizarPago();
  mostrarSeccionPago();

  //Común formularios
  $(".monto-pago").change(function () {
    if (!$(this).valid()) {
      var modo = $(this).data("modo");
      $(".usar-saldo-favor[data-modo='" + modo + "']").attr("checked", false);
      $(".usar-saldo-favor[data-modo='" + modo + "']").closest("label").removeClass("checked");
    }
  });
  $(".usar-saldo-favor").click(function (e) {
    var modo = $(this).data("modo");
    if ($(".monto-pago[data-modo='" + modo + "']").valid() && saldoFavorTotal > 0) {
      $(".monto-pago[data-modo='" + modo + "']").val(redondear(parseFloat($(".monto-pago[data-modo='" + modo + "']").val()) + ($(this).is(":checked") ? saldoFavorTotal : (-1 * saldoFavorTotal)), 2));
      $(this).attr("checked", $(this).is(":checked"));
    } else {
      e.stopPropagation();
      return false;
    }
  });
  $(".btn-cancelar-pago").click(function () {
    mostrarSeccionPago([1]);
  });

  //Común   
  registroHistorial = (typeof (registroHistorial) === "undefined" ? false : registroHistorial);
  if (obtenerParametroUrlXNombre("sec") === "pago" && !registroHistorial) {
    $("a[href='#pago']").trigger("click");
  }
  $("a[href='#pago']").click(function () {
    $(this).tab("show");
    $("#tab-lista-pagos").DataTable().responsive.recalc();
  });
}

//Lista
function cargarListaPago() {
  urlListarPagos = (typeof (urlListarPagos) === "undefined" ? "" : urlListarPagos);
  urlActualizarEstadoPago = (typeof (urlActualizarEstadoPago) === "undefined" ? "" : urlActualizarEstadoPago);
  urlEliminarPago = (typeof (urlEliminarPago) === "undefined" ? "" : urlEliminarPago);
  if (urlListarPagos !== "" && urlActualizarEstadoPago !== "" && urlEliminarPago !== "" && motivosPago !== "" && cuentasBanco !== "" && estadosPago !== "") {
    $("#tab-lista-pagos").DataTable({
      processing: true,
      serverSide: false,
      ajax: {
        url: urlListarPagos,
        type: "POST",
        data: function (d) {
          d._token = $("meta[name=_token]").attr("content");
        }
      },
      autoWidth: false,
      responsive: true,
      order: [[1, "desc"]],
      columns: [
        {data: "id", name: "pago.id", render: function (e, t, d, m) {
            var costoHoraPromedio = (d.costoHoraPromedio !== null ? parseFloat(d.costoHoraPromedio + "") : 0);
            return '<b>Código: </b>' + d.id + '<br/><b>Motivo: </b>' + motivosPago[d.motivo] + '<br/><b>Cuenta: </b>' + cuentasBanco[d.cuenta] + '<br/><span data-toggle="tooltip" title="Fecha de registro"><i class="fa fa-fw fa-calendar"></i> ' + formatoFecha(d.fechaRegistro, true) + '</span>' + (costoHoraPromedio > 0 ? '<br/><small><b>S/. ' + redondear(costoHoraPromedio, 2) + ' por hora de clase</b></small>' : '');
          }},
        {data: "fecha", name: "pago.fecha", render: function (e, t, d, m) {
            return formatoFecha(d.fecha);
          }, className: "text-center", type: "fecha"},
        {data: "estado", name: "pago.estado", render: function (e, t, d, m) {
            return '<div class="sec-btn-editar-estado-pago"><a href="javascript:void(0);" class="btn-editar-estado-pago" data-idpago="' + d.id + '" data-idalumno="' + d.idAlumno + '" data-estado="' + d.estado + '"><span class="label ' + estadosPago[d.estado][1] + ' btn-estado">' + estadosPago[d.estado][0] + '</span></a></div>';
          }, className: "text-center"},
        {data: "monto", name: "pago.monto", render: function (e, t, d, m) {
            var montoTotal = (d.monto !== null ? parseFloat(d.monto + "") : 0);
            var saldoFavor = (d.saldoFavor !== null ? parseFloat(d.saldoFavor + "") : 0);
            var montoTotalClases = (montoTotal - saldoFavor);
            var costoHoraPromedio = (d.costoHoraPromedio !== null ? parseFloat(d.costoHoraPromedio + "") : 0);

            var duracionTotal = (costoHoraPromedio > 0 ? ((montoTotalClases / costoHoraPromedio) * 3600) : 0);
            var duracionRealizada = (d.duracionMontoRealizado !== null ? parseFloat(d.duracionMontoRealizado.split("-")[0] + "") : 0);
            var duracionPendiente = (duracionTotal - duracionRealizada);
            var duracionPendienteReal = (d.duracionMontoPendiente !== null ? parseFloat(d.duracionMontoPendiente.split("-")[0] + "") : 0);
            var duracionNoPagada = (costoHoraPromedio > 0 ? ((duracionRealizada + duracionPendienteReal) - duracionTotal) : 0);

            var montoRealizado = (d.duracionMontoRealizado !== null ? parseFloat(d.duracionMontoRealizado.split("-")[1] + "") : 0);
            var montoPendiente = (montoTotalClases - montoRealizado);
            var montoPendienteReal = (d.duracionMontoPendiente !== null ? parseFloat(d.duracionMontoPendiente.split("-")[1] + "") : 0);
            var montoAFavor = (montoTotalClases - (montoRealizado + montoPendienteReal));
            var montoNoPagado = (montoAFavor < 0 ? (montoAFavor * (-1)) : 0);

            var saldoFavorTotalPago = saldoFavor + (montoAFavor > 0 && costoHoraPromedio > 0 ? montoAFavor : 0);
            var saldoFavorUtilizado = (d.saldoFavorUtilizado !== null && d.saldoFavorUtilizado === 1);
            
            if(duracionRealizada > duracionTotal){
              duracionRealizada = duracionTotal;
              montoRealizado = montoTotalClases;
            }

            return '<b>S/. ' + redondear(montoTotal, 2) + '</b>' +
                ('<div class="info-adicional">' + (duracionRealizada > 0 ? '<br/><span class="text-green" data-toggle="tooltip" title="Horas realizadas"><i class="fa fa-clock-o"></i> ' + formatoHora(duracionRealizada) + ' (S/. ' + redondear(montoRealizado, 2) + ')</span>' : '') +
                    (duracionPendienteReal > 0 ? '<br/><span class="text-yellow" data-toggle="tooltip" title="Horas pendientes"><i class="fa fa-clock-o"></i> ' + formatoHora(montoNoPagado > 0 ? duracionPendiente : duracionPendienteReal) + ' (S/. ' + redondear(montoNoPagado > 0 ? montoPendiente : montoPendienteReal, 2) + ')</span>' : '') +
                    (duracionNoPagada > 0 ? '<br/><span class="text-red" data-toggle="tooltip" title="Horas no pagadas"><i class="fa fa-clock-o"></i> ' + formatoHora(duracionNoPagada) + ' (S/. ' + redondear(montoNoPagado, 2) + ')</span>' : '') +
                    (saldoFavorTotalPago > 0 ? '<br/><small><b>Saldo a favor de S/. ' + redondear(saldoFavorTotalPago, 2) + (saldoFavorUtilizado ? ' <br/>(<span class="text-green">utilizado</span>)' : '') + '</b></small>' : '') + '</div>');
          }, className: "text-center", type: "monto"},
        {data: "id", name: "pago.id", orderable: false, searchable: false, width: "5%", render: function (e, t, d, m) {
            return '<ul class="buttons">' +
                '<li>' +
                '<a href="javascript:void(0);" onclick="editarPago(' + d.id + ');" title="Editar datos del pago"><i class="fa fa-pencil"></i></a>' +
                '</li>' +
                '<li>' +
                '<a href="javascript:void(0);" title="Eliminar pago" onclick="eliminarElemento(this, \'¿Está seguro que desea eliminar los datos de este pago?, considere que si el pago está relacionado a una o más clases estas también serán eliminadas.\', \'tab-lista-pagos\', false, function(){recargarDatosTabla(\'tab-lista-periodos-clases\');reiniciarHistorial();})" data-id="' + d.id + '" data-urleliminar="' + ((urlEliminarPago.replace("/0", "/" + d.id))) + '">' +
                '<i class="fa fa-trash"></i>' +
                '</a>' +
                '</li>' +
                '</ul>';
          }, className: "text-center"}
      ],
      initComplete: function (s, j) {
        establecerBotonRecargaTabla("tab-lista-pagos");
      },
      footerCallback: function (r, d, s, e, di) {
        var api = this.api();

        var montoTotal = 0, montoTotalPagina = 0;
        saldoFavorTotal = 0;
        $('#tab-lista-pagos').DataTable().rows().data().each(function (i) {
          var montoTotal = (i.monto !== null ? parseFloat(i.monto + "") : 0);
          var saldoFavor = (i.saldoFavor !== null ? parseFloat(i.saldoFavor + "") : 0);
          var montoTotalClases = (montoTotal - saldoFavor);
          var costoHoraPromedio = (i.costoHoraPromedio !== null ? parseFloat(i.costoHoraPromedio + "") : 0);

          var montoRealizado = (i.duracionMontoRealizado !== null ? parseFloat(i.duracionMontoRealizado.split("-")[1] + "") : 0);
          var montoPendienteReal = (i.duracionMontoPendiente !== null ? parseFloat(i.duracionMontoPendiente.split("-")[1] + "") : 0);
          var montoAFavor = (montoTotalClases - (montoRealizado + montoPendienteReal));

          var saldoFavorTotalPago = saldoFavor + (montoAFavor > 0 && costoHoraPromedio > 0 ? montoAFavor : 0);
          if (!(i.saldoFavorUtilizado !== null && i.saldoFavorUtilizado === 1)) {
            saldoFavorTotal += saldoFavorTotalPago;
            $("#sec-saldo-favor").show();
            $("#lbl-usar-saldo-favor").text("Utilizar saldo a favor total (S/. " + redondear(saldoFavorTotal, 2) + ")");
          }
        });
        $('#tab-lista-pagos').DataTable().rows({filter: 'applied'}).data().each(function (i) {
          montoTotal += parseFloat(i.monto);
        });
        $('#tab-lista-pagos').DataTable().rows({page: 'current'}).data().each(function (i) {
          montoTotalPagina += parseFloat(i.monto);
        });
        $(api.column(3).footer()).html("Total S/. " + redondear(montoTotal, 2) + (montoTotal !== montoTotalPagina ? "<br/>Total de la página S/." + redondear(montoTotalPagina, 2) : ""));
      }
    });

    $(window).click(function (e) {
      if (!$(e.target).closest('.sec-btn-editar-estado-pago').length) {
        $(".sec-btn-editar-estado-pago select").trigger("change");
      }
    });
    $(".btn-editar-estado-pago").live("click", function () {
      $("#sel-estados-pago").clone().val($(this).data("estado")).data("idpago", $(this).data("idpago")).data("idalumno", $(this).data("idalumno")).data("estado", $(this).data("estado")).appendTo($(this).closest(".sec-btn-editar-estado-pago"));
      $(this).remove();
      event.stopPropagation();
    });
    $(".sec-btn-editar-estado-pago select").live("change", function () {
      var idpago = $(this).data("idpago");
      var idAlumno = $(this).data("idalumno");
      if (urlActualizarEstadoPago !== "" && $(this).data("estado") !== $(this).val()) {
        llamadaAjax(urlActualizarEstadoPago, "POST", {"idPago": idpago, "idAlumno": idAlumno, "estado": $(this).val()}, true, undefined, undefined, function (de) {
          var rj = de.responseJSON;
          if (rj !== undefined && rj.mensaje !== undefined) {
            agregarMensaje("errores", rj.mensaje, true);
          } else if (rj !== undefined && rj[Object.keys(rj)[0]] !== undefined) {
            agregarMensaje("errores", rj[Object.keys(rj)[0]][0], true);
          }
          $("#tab-lista-pagos").DataTable().ajax.reload();
        });
      }
      $(this).closest(".sec-btn-editar-estado-pago").append('<a href="javascript:void(0);" class="btn-editar-estado-pago" data-idpago="' + idpago + '" data-idalumno="' + idAlumno + '" data-estado="' + $(this).val() + '"><span class="label ' + estadosPago[$(this).val()][1] + ' btn-estado">' + estadosPago[$(this).val()][0] + '</span></a>');
      $(this).remove();
    });
  }
}

//Formulario
function cargarFormularioPago() {
  $("#formulario-pago").validate({
    ignore: ":hidden",
    rules: {
      fecha: {
        required: true,
        validarFecha: true
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
        required: true,
        validarFecha: true
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
    submitHandler: function (f) {
      var datosNotificacionClases = [];
      $.each($("#sec-lista-clases-pago tbody tr"), function (e, v) {
        datosNotificacionClases.push({"notificar": $(v).find("input[type='checkbox']").is(":checked")});
      });
      $("input[name='datosNotificacionClases']").val(JSON.stringify(datosNotificacionClases));
      if (confirm("¿Está seguro que desea registrar los datos de este pago?")) {
        $.blockUI({message: "<h4>Registrando datos...</h4>"});
        f.submit();
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
    },
    onfocusout: false,
    onkeyup: false,
    onclick: false
  });

  establecerCalendario("fecha-pago", false, false, false);

  var fechaInicioClasesPago = $("#fecha-inicio-clases-pago").val();
  establecerCalendario("fecha-inicio-clases-pago", false, false, false);
  if (fechaInicioClasesPago !== "") {
    var datFechaInicioClasesPago = fechaInicioClasesPago.split("/");
    $("#fecha-inicio-clases-pago").datepicker("setDate", (new Date(datFechaInicioClasesPago[1] + "/" + datFechaInicioClasesPago[0] + "/" + datFechaInicioClasesPago[2])));
  }

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
  $("#btn-anterior-pago").click(function () {
    $(this).hide();
    $("#motivo-pago").trigger("change");
  });
  $("#btn-generar-clases-pago").click(generarClases);
  $("#btn-docentes-disponibles-pago").click(function () {
    cargarDocentesDisponiblesPago(true);
  });
  $("#tipo-docente-disponible-pago, #sexo-docente-disponible-pago, #id-curso-docente-disponible-pago").change(function () {
    cargarDocentesDisponiblesPago(true);
  });
  $("#btn-confirmar-docente-disponible-pago").click(function () {
    if (urlPerfilProfesor !== "") {
      var docenteDisponiblePago = $("input[name='idDocenteDisponiblePago']:checked");
      limpiarCamposPago(true);
      if (docenteDisponiblePago.length > 0) {
        $("input[name='idDocente']").val(docenteDisponiblePago.val());
        $("#nombre-docente-pago").html((docenteDisponiblePago.val() !== '' ? '<i class="fa flaticon-teach"></i> <b>' + docenteDisponiblePago.data('nombrecompleto') + '</b> <a href=' + (urlPerfilProfesor.replace('/0', '/' + docenteDisponiblePago.val())) + ' title="Ver perfil del profesor" target="_blank"><i class="fa fa-eye"></i></a>' : ''));
        mostrarSeccionPago([2, 2, 1]);
      } else {
        mostrarSeccionPago([2, 2]);
      }
    }
    $("#mod-docentes-disponibles-pago").modal("hide");
  });
}
function generarClases(e) {
  var camposFormularioPago = $("#formulario-pago").find(":input, select").not(":hidden, input[name='costoHoraDocente']");
  if (!camposFormularioPago.valid()) {
    e.preventDefault();
    return false;
  }

  var fDatos = $("#formulario-pago").serializeArray();
  var datos = {};
  $(fDatos).each(function (i, o) {
    datos[o.name] = o.value;
  });

  urlGenerarClasesPago = (typeof (urlGenerarClasesPago) === "undefined" ? "" : urlGenerarClasesPago);
  if (urlGenerarClasesPago !== "") {
    $.blockUI({message: "<h4>Cargando...</h4>"});
    llamadaAjax(urlGenerarClasesPago, "POST", datos, true,
        function (d) {
          $("#sec-lista-clases-pago tbody, #sec-saldo-favor-pago").html("");
          $("input[name='saldoFavor']").val("");

          var idProfesor = "";
          var nombreCompletoProfesor = "";

          $.each(d, function (i, v) {
            if (i !== "montoRestante" && i !== "idProfesor" && i !== "nombreCompletoProfesor") {
              var tiempoAdicionalMinutos = (v.tiempoAdicional > 0 ? v.tiempoAdicional / 60 : 0);
              var tiempoAdicionalHoras = ((tiempoAdicionalMinutos > 0 && tiempoAdicionalMinutos >= 60) ? tiempoAdicionalMinutos / 60 : 0);
              var tiempoAdicional = (v.tiempoAdicional > 0 ? ' <small><b>(Se le descontó ' +
                  (tiempoAdicionalHoras > 0 ? (tiempoAdicionalHoras + (tiempoAdicionalHoras > 1 ? ' horas' : ' hora'))
                      : (tiempoAdicionalMinutos + (tiempoAdicionalMinutos > 1 ? ' minutos' : 'minuto'))) + ')</b></small>'
                  : '');
              $("#sec-lista-clases-pago tbody").append('<tr>' +
                  '<td>' + (parseInt(i) + 1) + '</td>' +
                  '<td><b>' + formatoFecha(v.fechaInicio.date) + '</b> - De ' + formatoFecha(v.fechaInicio.date, false, true) + ' a ' + formatoFecha(v.fechaFin.date, false, true) + '</td>' +
                  '<td>' + formatoHora(v.duracion) + tiempoAdicional + '</td>' +
                  '<td class="text-center"><input type="checkbox" name="notificarClasePago_' + (parseInt(i) + 1) + '"' + (v.idProfesor !== '' ? '' : ' checked="checked"') + '/></td>' +
                  '</tr>');
            } else if (i === "montoRestante" && v > 0) {
              $("#sec-saldo-favor-pago").html('<span>El alumno tiene un saldo a favor de <b>S/. ' + redondear(v, 2) + '</b></span>');
              $("input[name='saldoFavor']").val(redondear(v, 4));
            } else if (i === "idProfesor") {
              idProfesor = v;
            } else if (i === "nombreCompletoProfesor") {
              nombreCompletoProfesor = v;
            }
          });
          if ($("#sec-lista-clases-pago tbody").html() !== "") {
            limpiarCamposPago(true);
            $("#btn-generar-clases-pago").hide();
            $("#btn-anterior-pago, #btn-registrar-pago").show();
            mostrarSeccionPago([2, 2]);

            if (idProfesor !== "" && nombreCompletoProfesor !== "") {
              if (urlPerfilProfesor !== "") {
                $("input[name='idDocente']").val(idProfesor);
                $("#nombre-docente-pago").html('<i class="fa flaticon-teach"></i> <b>' + nombreCompletoProfesor + '</b> <a href=' + (urlPerfilProfesor.replace('/0', '/' + idProfesor)) + ' title="Ver perfil del profesor" target="_blank"><i class="fa fa-eye"></i></a>');
                mostrarSeccionPago([2, 2, 1]);
              }
            }
          }
          $("body").unblock();
        },
        function (d) {
        },
        function (de) {
          $("body").unblock({
            onUnblock: function () {
              agregarMensaje("errores", "Ocurrió un problema durante la generación de clases. Por favor inténtelo nuevamente.", true, "#sec-mensajes-pago");
            }
          });
        }
    );
  }
}
function cargarDocentesDisponiblesPago(recargarListaPago) {
  var camposFormularioPago = $("#formulario-pago").find(":input, select").not(":hidden, input[name='costoHoraDocente']");
  if (!camposFormularioPago.valid()) {
    return false;
  }

  $("#mod-docentes-disponibles-pago").modal("show");
  if ($.fn.DataTable.isDataTable("#tab-lista-docentes-pago")) {
    if (recargarListaPago) {
      $("#tab-lista-docentes-pago").DataTable().ajax.reload();
    }
  } else {
    urlListarDocentesDisponiblesPago = (typeof (urlListarDocentesDisponiblesPago) === "undefined" ? "" : urlListarDocentesDisponiblesPago);
    estadosProfesor = (typeof (estadosProfesor) === "undefined" ? "" : estadosProfesor);
    if (urlListarDocentesDisponiblesPago !== "" && urlPerfilProfesor !== "" && estadosProfesor !== "") {
      $("#tab-lista-docentes-pago").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: urlListarDocentesDisponiblesPago,
          type: "POST",
          data: function (d) {
            d.tipoDocente = $("#tipo-docente-disponible-pago").val();
            d.sexoDocente = $("#sexo-docente-disponible-pago").val();
            d.idCursoDocente = $("#id-curso-docente-disponible-pago").val();
            var fDatos = $("#formulario-pago").serializeArray();
            $(fDatos).each(function (i, o) {
              d[o.name] = o.value;
            });
          }
        },
        autoWidth: false,
        columns: [
          {data: "nombreCompleto", name: "nombreCompleto", render: function (e, t, d, m) {
              return '<a href=' + (urlPerfilProfesor.replace('/0', '/' + d.id)) + ' title="Ver perfil del profesor" target="_blank">' + d.nombreCompleto + '</a>';
            }},
          {data: "estado", name: "estado", render: function (e, t, d, m) {
              return ((estadosProfesor[d.estado] !== undefined) ? '<span class="label ' + estadosProfesor[d.estado][1] + ' btn-estado">' + estadosProfesor[d.estado][0] + '</span>' : '');
            }},
          {data: "id", name: "id", orderable: false, "searchable": false, width: "5%"}
        ],
        createdRow: function (r, d, i) {
          $("td", r).eq(2).html('<input type="radio" name="idDocenteDisponiblePago" value="' + d.id + '" data-nombrecompleto="' + d.nombreCompleto + '"/>');
          $("td", r).eq(2).addClass("text-center");
        },
        initComplete: function (s, j) {
          establecerBotonRecargaTabla("tab-lista-docentes-pago");
        }
      });
    }
  }
}

//Formulario editar
function cargarFormularioActualizarPago() {
  $("#formulario-actualizar-pago").validate({
    ignore: ":hidden",
    rules: {
      fecha: {
        required: true,
        validarFecha: true
      },
      imagenComprobante: {
        validarImagen: true
      },
      monto: {
        required: true,
        validarDecimal: true
      }
    },
    submitHandler: function (f) {
      if (confirm("¿Está seguro que desea guardar los datos de este pago?")) {
        $.blockUI({message: "<h4>Guardando datos...</h4>"});
        f.submit();
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
    },
    onfocusout: false,
    onkeyup: false,
    onclick: false
  });
  establecerCalendario("fecha-actualizar-pago", false, false, false);
}
function editarPago(idPago) {
  obtenerDatosPago(idPago, function (d) {
    if (urlArchivos !== "") {
      limpiarCamposPago();
      $("#motivo-actualizar-pago").val(d.motivo);
      $("#cuenta-actualizar-pago").val(d.cuenta);
      var datFecha = formatoFecha(d.fecha).split("/");
      $("#fecha-actualizar-pago").datepicker("setDate", (new Date(datFecha[1] + "/" + datFecha[0] + "/" + datFecha[2])));
      $("#estado-actualizar-pago").val(d.estado);
      $("#descripcion-actualizar-pago").val(d.descripcion);
      if (d.imagenesComprobante !== null && d.imagenesComprobante !== "") {
        var rutaImagen = urlArchivos.replace("/0", "/" + d.imagenesComprobante);
        $("#imagen-comprobante-actualizar-pago").attr("href", rutaImagen);
        $("#imagen-comprobante-actualizar-pago").find("img").attr("src", rutaImagen);
      }
      $("#monto-actualizar-pago").val(redondear(d.monto, 2));
      $("input[name='idPago']").val(d.id);
      mostrarSeccionPago([3]);
    }
  });
}

//Datos
function verDatosPago(idPago) {
  if (motivosPago !== "" && cuentasBanco !== "" && urlArchivos !== "" && estadosPago !== "") {
    obtenerDatosPago(idPago, function (d) {
      $("#sec-descripcion-pago").hide();
      if (d.descripcion !== null && d.descripcion.trim() !== "") {
        $("#sec-descripcion-pago").show();
      }
      $("#dat-motivo-pago").text(motivosPago[d.motivo]);
      $("#dat-cuenta-pago").text(cuentasBanco[d.cuenta]);
      $("#dat-descripcion-pago").text(d.descripcion);
      $("#dat-monto-pago").html('S/. ' + redondear(d.monto, 2) + (d.saldoFavor !== null && parseFloat(d.saldoFavor + "") > 0 ? '<br/><small><b>Saldo a favor de S/. ' + redondear(d.saldoFavor, 2) + (d.saldoFavorUtilizado !== null && d.saldoFavorUtilizado === 1 ? ' (<span class="text-green">utilizado</span>)' : '') + '</b></small>' : ''));
      $("#dat-estado-pago").html('<span class="label ' + estadosPago[d.estado][1] + ' btn-estado">' + estadosPago[d.estado][0] + '</span>');
      $("#dat-fecha-pago").text(formatoFecha(d.fecha));
      $("#dat-fecha-registro-pago").text(formatoFecha(d.fechaRegistro, true));
      if (d.imagenesComprobante !== null && d.imagenesComprobante !== "") {
        var rutaImagen = urlArchivos.replace("/0", "/" + d.imagenesComprobante);
        $("#dat-imagen-comprobante-pago").attr("href", rutaImagen);
        $("#dat-imagen-comprobante-pago").find("img").attr("src", rutaImagen);
      }
      $("#mod-datos-pago").modal("show");
      $("body").unblock();
    });
  }
}

//Util
function limpiarCamposPago(soloCamposDocente) {
  $("input[name='idDocente']").val("");
  $("#nombre-docente-pago").html("");
  if (!soloCamposDocente) {
    $("#formulario-pago, #formulario-actualizar-pago").find(":input, select").each(function (i, e) {
      if (e.name !== "fecha" && e.name !== "costoHoraClase" && e.name !== "fechaInicioClases" && e.name !== "periodoClases" && e.name !== "_token" && e.type !== "hidden") {
        if ($(e).is("select")) {
          $(e).prop("selectedIndex", 0);
        } else if ($(e).is(":checkbox")) {
          $(e).attr("checked", false);
          $(e).closest("label").removeClass("checked");
        } else {
          e.value = "";
        }
      }
    });
    $("#fecha-pago").datepicker("setDate", "today");
    $("form .help-block-error").remove();
  }
}
function mostrarSeccionPago(numSecciones) {
  if (!numSecciones) {
    numSecciones = [1];
  }

  $("[id*='sec-pago']").hide();
  var auxSec = "";
  for (var i = 0; i < numSecciones.length; i++) {
    $("#sec-pago-" + auxSec + "" + numSecciones[i]).show();
    auxSec += "" + numSecciones[i];
  }
}
function obtenerDatosPago(idPago, funcionRetorno) {
  urlDatosPago = (typeof (urlDatosPago) === "undefined" ? "" : urlDatosPago);
  if (urlDatosPago !== "") {
    $.blockUI({message: "<h4>Cargando...</h4>", baseZ: 2000});
    llamadaAjax(urlDatosPago.replace("/0", "/" + idPago), "POST", {}, true,
        function (d) {
          if (funcionRetorno !== undefined)
            funcionRetorno(d);
          $("body").unblock();
        },
        function (d) {},
        function (de) {
          $('body').unblock({
            onUnblock: function () {
              agregarMensaje("errores", "Ocurrió un problema durante la carga de datos del pago seleccionado. Por favor inténtelo nuevamente.", true, "#sec-mensajes-pago");
            }
          });
        }
    );
  }
}