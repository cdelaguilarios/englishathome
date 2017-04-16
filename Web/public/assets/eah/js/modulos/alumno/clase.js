window.addEventListener("load", verificarJqueryClase, false);
function verificarJqueryClase() {
  ((window.jQuery && jQuery.ui) ? cargarSeccionClases() : window.setTimeout(verificarJqueryClase, 100));
}
function  cargarSeccionClases() {
  urlPerfilProfesor = (typeof (urlPerfilProfesor) === "undefined" ? "" : urlPerfilProfesor);
  estadosClase = (typeof (estadosClase) === "undefined" ? "" : estadosClase);
  estadosClaseCambio = (typeof (estadosClaseCambio) === "undefined" ? "" : estadosClaseCambio);
  minHorario = (typeof (minHorario) === "undefined" ? "" : minHorario);
  maxHorario = (typeof (maxHorario) === "undefined" ? "" : maxHorario);
  minHorasClase = (typeof (minHorasClase) === "undefined" ? "" : minHorasClase);
  maxHorasClase = (typeof (maxHorasClase) === "undefined" ? "" : maxHorasClase);

  cargarListaPeriodos();
  cargarFormularioClase();
  cargarFormularioCancelarClase();
  cargarFormularioClasesGrupo();
  mostrarSeccionClase();

  //Común   
  if (obtenerParametroUrlXNombre("sec") === "clase") {
    $("a[href='#clase']").tab("show");
  }
  $("#fecha-clase, #fecha-clase-reprogramada, #hora-inicio-clase, #hora-inicio-clase-reprogramada, #hora-inicio-clases, #duracion-clase, #duracion-clase-reprogramada, #duracion-clases").live("change", function () {
    //numFormulario => 1:Formulario cancelar, 2: Formulario grupo, 3: Formulario clásico
    var numFormulario = ($("#formulario-cancelar-clase").is(":visible") ? 1 : ($("#formulario-actualizar-clases").is(":visible") ? 2 : 3));
    var idsClases = (numFormulario === 2 ? $("input[name='idsClases']").val() : [$("input[name='idClase']").val()]);
    var fecha = (numFormulario === 1 ? $("#fecha-clase-reprogramada").val() : (numFormulario === 2 ? null : $("#fecha-clase").val()));
    var horaInicio = (numFormulario === 1 ? $("#hora-inicio-clase-reprogramada").val() : (numFormulario === 2 ? $("#hora-inicio-clases").val() : $("#hora-inicio-clase").val()));
    var duracion = (numFormulario === 1 ? $("#duracion-clase-reprogramada").val() : (numFormulario === 2 ? $("#duracion-clases").val() : $("#duracion-clase").val()));
    cambioFechaHorario(idsClases, fecha, horaInicio, duracion, numFormulario);
  });
  $(".btn-docentes-disponibles-clase").click(function () {
    cargarDocentesDisponiblesClase(false, true);
  });
  $("#sexo-docente-disponible-clase, #id-curso-docente-disponible-clase, #tipo-docente-disponible-clase").change(function () {
    cargarDocentesDisponiblesClase(true);
  });
  $("#btn-confirmar-docente-disponible-clase").click(function () {
    if (urlPerfilProfesor !== "") {
      limpiarCamposClase(true);
      var docenteDisponibleClase = $("input[name='idDocenteDisponibleClase']:checked");
      //numFormulario => 1:Formulario cancelar, 2: Formulario grupo, 3: Formulario clásico
      var numFormulario = ($("#formulario-cancelar-clase").is(":visible") ? 1 : ($("#formulario-actualizar-clases").is(":visible") ? 2 : 3));

      if (docenteDisponibleClase.length > 0) {
        $(".id-docente-clase").val(docenteDisponibleClase.val());
        $(".nombre-docente-clase").html((docenteDisponibleClase.val() !== '' ? '<i class="fa flaticon-teach"></i> <b>' + docenteDisponibleClase.data('nombrecompleto') + '</b> <a href=' + (urlPerfilProfesor.replace('/0', '/' + docenteDisponibleClase.val())) + ' title="Ver perfil del profesor" target="_blank"><i class="fa fa-eye"></i></a>' : ''));
        (numFormulario === 1 ? "" : (numFormulario === 2 ? $("#sec-clase-441").show() : mostrarSeccionClase([2, 1])));
      } else {
        (numFormulario === 1 ? $("#sec-clase-321").hide() : (numFormulario === 2 ? $("#sec-clase-441").hide() : mostrarSeccionClase([2])));
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
function cargarListaPeriodos() {
  urlListarPeriodos = (typeof (urlListarPeriodos) === "undefined" ? "" : urlListarPeriodos);
  urlActualizarEstadoClase = (typeof (urlActualizarEstadoClase) === "undefined" ? "" : urlActualizarEstadoClase);
  if (urlListarPeriodos !== "" && urlActualizarEstadoClase !== "") {
    $("#tab-lista-periodos-clases").DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: urlListarPeriodos,
        type: "POST",
        data: function (d) {
          d._token = $('meta[name=_token]').attr("content");
        }
      },
      autoWidth: false,
      seaching: false,
      filter: false,
      order: [[0, "desc"]],
      columns: [
        {data: "numeroPeriodo", name: "numeroPeriodo", searchable: false},
        {data: "numeroPeriodo", name: "numeroPeriodo", orderable: false, searchable: false, render: function (e, t, d, m) {
            return '<a href="javascript:void(0);" onclick="mostrarOcultarClases(this);" class="btn btn-primary btn-xs" data-periodo="' + d.numeroPeriodo + '"><i class="fa fa-eye"></i> Ver clases</button>';
          }}
      ],
      "createdRow": function (r, d, i) {
        var fechaActual = new Date();
        var fechaInicioSel = new Date(d.fechaInicio);
        var fechaFinSel = new Date(d.fechaFin);

        //Número de período                       
        $("td", r).eq(0).html("<b>Período: </b>" + d.numeroPeriodo + ((fechaActual >= fechaInicioSel && fechaActual <= fechaFinSel) ? " (Actual)" : "") + "<br/>Del <b>" + formatoFecha(d.fechaInicio) + "</b> al <b>" + formatoFecha(d.fechaFin) + "</b><br/><b>Total de horas:</b> " + formatoHora(d.horasTotal));

        //Opciones
        $("td", r).eq(1).addClass("text-center");
      },
      initComplete: function (s, j) {
        establecerBotonRecargaTabla("tab-lista-periodos-clases");
      }
    });

    $(window).click(function (e) {
      if (!$(e.target).closest('.sec-btn-editar-estado-clase').length) {
        $(".sec-btn-editar-estado-clase select").trigger("change");
      }
    });
    $(".btn-editar-estado-clase").live("click", function () {
      $("#sel-estados-clase").clone().val($(this).data("estado")).data("idclase", $(this).data("idclase")).data("idalumno", $(this).data("idalumno")).data("estado", $(this).data("estado")).appendTo($(this).closest(".sec-btn-editar-estado-clase"));
      $(this).remove();
      event.stopPropagation();
    });
    $(".sec-btn-editar-estado-clase select").live("change", function () {
      var idClase = $(this).data("idclase");
      var idAlumno = $(this).data("idalumno");
      if (urlActualizarEstadoClase !== "" && $(this).data("estado") !== $(this).val()) {
        llamadaAjax(urlActualizarEstadoClase, "POST", {"idClase": idClase, "idAlumno": idAlumno, "estado": $(this).val()}, true, undefined, undefined, function (de) {
          var rj = de.responseJSON;
          if (rj !== undefined && rj.mensaje !== undefined) {
            agregarMensaje("errores", rj.mensaje, true);
          } else if (rj !== undefined && rj[Object.keys(rj)[0]] !== undefined) {
            agregarMensaje("errores", rj[Object.keys(rj)[0]][0], true);
          }
          $("#tab-lista-pagos").DataTable().ajax.reload();
        });
      }
      $(this).closest(".sec-btn-editar-estado-clase").append('<a href="javascript:void(0);" class="btn-editar-estado-clase" data-idclase="' + idClase + '" data-idalumno="' + idAlumno + '" data-estado="' + $(this).val() + '"><span class="label ' + estadosClase[$(this).val()][1] + ' btn-estado">' + estadosClase[$(this).val()][0] + '</span></a>');
      $(this).remove();
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
  estadoClaseRealizada = (typeof (estadoClaseRealizada) === "undefined" ? "" : estadoClaseRealizada);
  estadoClaseCancelada = (typeof (estadoClaseCancelada) === "undefined" ? "" : estadoClaseCancelada);
  estadosPago = (typeof (estadosPago) === "undefined" ? "" : estadosPago);

  if (urlListarClases !== "" && urlEliminarClase !== "" && urlPerfilProfesor !== "" && estadosClase !== "" && estadoClaseRealizada !== "" && estadoClaseCancelada !== "" && estadosPago !== "") {
    $.blockUI({message: "<h4>Cargando...</h4>"});
    llamadaAjax(((urlListarClases.replace("/0", "/" + datosFila.numeroPeriodo))), "POST", {}, true,
        function (d) {
          if (d.length > 0) {
            $("body").unblock({
              onUnblock: function () {
                fila.child($('#sec-not-mobile').css('display') === 'none' ? htmlListaClasesMovil(d) : htmlListaClases(d) + '<div class="box-body"><div id="sec-btn-editar-clases-' + d[0].numeroPeriodo + '" style="display: none;"><a type="button" class="btn btn-primary btn-sm" onclick="editarClasesGrupo(' + d[0].numeroPeriodo + ')">Editar clases seleccionadas</a></div></div>').show();
                tr.find('a:eq(0)').html(tr.find('a:eq(0)').html().replace('<i class="fa fa-eye"></i> Ver', '<i class="fa fa-eye-slash"></i> Ocultar'));

                if ($('#sec-not-mobile').css('display') === 'none') {
                  $("#tab-lista-clases-" + d[0].numeroPeriodo).DataTable({
                    paginate: false,
                    columnDefs: [
                      {targets: [1], orderable: false, searchable: false}
                    ]
                  });
                } else {
                  $("#tab-lista-clases-" + d[0].numeroPeriodo).DataTable({
                    paginate: false,
                    order: [[1, "asc"]],
                    columnDefs: [
                      {targets: [1], type: "fecha"},
                      {targets: [3], orderable: false, searchable: false}
                    ]
                  });
                }
                $("#tab-lista-clases-" + d[0].numeroPeriodo).find("input[type='checkbox']").live("change", function () {
                  (($("#tab-lista-clases-" + d[0].numeroPeriodo).find("input[type='checkbox']:checked").length > 0) ? $("#sec-btn-editar-clases-" + d[0].numeroPeriodo).show() : $("#sec-btn-editar-clases-" + d[0].numeroPeriodo).hide());
                });
              }
            });
          } else {
            recargarDatosTabla("tab-lista-periodos-clases");
            $("body").unblock();
          }
        },
        function (d) {
        },
        function (de) {
          $("body").unblock({
            onUnblock: function () {
              agregarMensaje("errores", "Ocurrió un problema durante la carga de lista de clases del período seleccionado. Por favor inténtelo nuevamente.", true, "#sec-mensajes-clase");
            }
          });
        }
    );
  }
}
function htmlListaClases(d) {
  var htmlListaClases = "";
  for (var i = 0; i < d.length; i++) {
    htmlListaClases +=
        '<tr>' +
        '<td class="text-center">' +
        (d[i].estado !== estadoClaseCancelada ? '<input type="checkbox" data-id="' + d[i].id + '" />' : '') +
        '</td>' +
        '<td>' +
        '<b>Fecha:</b> ' + formatoFecha(d[i].fechaInicio) + ' - De ' + formatoFecha(d[i].fechaInicio, false, true) + ' a ' + formatoFecha(d[i].fechaFin, false, true) + '<br/>'
        + '<b>Duración:</b> ' + formatoHora(d[i].duracion) + '<br/>'
        + (d[i].idHistorial !== null ?
            '<b>Notificar:</b> ' + ' <i class="fa fa-check icon-notificar-clase"></i>' + '<br/>' : '')
        + '<b>Profesor:</b> ' + (d[i].idProfesor !== null && d[i].nombreProfesor !== null && d[i].nombreProfesor !== '' ? '<a target="_blank" href="' + urlPerfilProfesor.replace("/0", "/" + d[i].idProfesor) + '">' + d[i].nombreProfesor + ' ' + d[i].apellidoProfesor + '</a>' + (d[i].estadoPagoProfesor !== null ? '<br/><span class="label ' + estadosPago[d[i].estadoPagoProfesor][1] + ' btn-estado">Pago al profesor - ' + estadosPago[d[i].estadoPagoProfesor][0] + '</span>' : '') : 'Sin profesor asignado') +
        '</td>' +
        '<td class="text-center">' +
        ((estadosClase[d[i].estado] !== undefined && estadosClaseCambio[d[i].estado] !== undefined) ?
            '<div class="sec-btn-editar-estado-clase"><a href="javascript:void(0);" class="btn-editar-estado-clase" data-idclase="' + d[i].id + '" data-idalumno="' + d[i].idAlumno + '" data-estado="' + d[i].estado + '"><span class="label ' + estadosClase[d[i].estado][1] + ' btn-estado">' + estadosClase[d[i].estado][0] + '</span></a></div>' : ((estadosClase[d[i].estado] !== undefined) ? '<span class="label ' + estadosClase[d[i].estado][1] + ' btn-estado">' + estadosClase[d[i].estado][0] + '</span>' : '')) +
        '</td>' +
        '<td class="text-center">' +
        '<ul class="buttons">' +
        '<li>' +
        '<a href="javascript:void(0);" onclick="editarClase(' + d[i].id + ');" title="Editar clase"><i class="fa fa-pencil"></i></a>' +
        '</li>' +
        (d[i].estado !== estadoClaseRealizada && d[i].estado !== estadoClaseCancelada ? '<li>' +
            '<a href="javascript:void(0);" onclick="cancelarClase(' + d[i].id + ');" title="Cancelar clase"><i class="fa fa-remove"></i></a>' +
            '</li>' :
            '') +
        "<li>" +
        '<a href="javascript:void(0);" title="Eliminar clase" onclick="eliminarElemento(this, \'¿Está seguro que desea eliminar los datos de esta clase?\', null, true, function(){mostrarOcultarClases($(\'a[data-periodo=' + d[i].numeroPeriodo + ']\'), true);})" data-id="' + d[i].id + '" data-urleliminar="' + ((urlEliminarClase.replace('/0', '/' + d[i].id))) + '">' +
        "<i class='fa fa-trash'></i>" +
        "</a>" +
        "</li>" +
        '</ul>' +
        '</td>' +
        '</tr>';
  }
  return '<div class="box-body">' +
      '<div id="sec-mensajes-periodo-' + d[0].numeroPeriodo + '"></div>' +
      '<table id="tab-lista-clases-' + d[0].numeroPeriodo + '" class="table table-bordered table-hover sub-table">' +
      '<thead>' +
      '<tr>' +
      '<th class="text-center">Seleccionar</th>' +
      '<th>Datos</th>' +
      '<th>Estado</th>' +
      '<th>Opciones</th>' +
      '</tr>' +
      '</thead>' +
      '<tbody>' + htmlListaClases + '</tbody>' +
      '</table>' +
      '</div>';
}
function htmlListaClasesMovil(d) {
  var htmlListaClases = "";
  for (var i = 0; i < d.length; i++) {
    htmlListaClases +=
        '<tr>' +
        '<td>' +
        '<b>Número:</b> ' + (i + 1) + '<br/>' +
        '<b>Fecha:</b> ' + formatoFecha(d[i].fechaInicio) + ' - De ' + formatoFecha(d[i].fechaInicio, false, true) + ' a ' + formatoFecha(d[i].fechaFin, false, true) + '<br/>' +
        '<b>Duración:</b> ' + formatoHora(d[i].duracion) + '<br/>' +
        '<b>Profesor:</b> ' + (d[i].idProfesor !== null && d[i].nombreProfesor !== null && d[i].nombreProfesor !== '' ? '<a target="_blank" href="' + urlPerfilProfesor.replace("/0", "/" + d[i].idProfesor) + '">' + d[i].nombreProfesor + ' ' + d[i].apellidoProfesor + (d[i].estadoPagoProfesor !== null ? '<br/><span class="label ' + estadosPago[d[i].estadoPagoProfesor][1] + ' btn-estado">Pago al profesor - ' + estadosPago[d[i].estadoPagoProfesor][0] + '</span>' : '') + '</a>' : 'Sin profesor asignado') + '<br/>' +
        (d[i].idHistorial !== null ? '<b>Notificar:</b> <i class="fa fa-check icon-notificar-clase"></i><br/>' : '') +
        ((estadosClase[d[i].estado] !== undefined && estadosClaseCambio[d[i].estado] !== undefined) ?
            '<div class="sec-btn-editar-estado-clase"><a href="javascript:void(0);" class="btn-editar-estado-clase" data-idclase="' + d[i].id + '" data-idalumno="' + d[i].idAlumno + '" data-estado="' + d[i].estado + '"><span class="label ' + estadosClase[d[i].estado][1] + ' btn-estado">' + estadosClase[d[i].estado][0] + '</span></a></div>' : ((estadosClase[d[i].estado] !== undefined) ? '<span class="label ' + estadosClase[d[i].estado][1] + ' btn-estado">' + estadosClase[d[i].estado][0] + '</span>' : '')) +
        '</td>' +
        '<td class="text-center">' +
        '<ul class="buttons">' +
        '<li>' +
        '<a href="javascript:void(0);" onclick="editarClase(' + d[i].id + ');" title="Editar clase"><i class="fa fa-pencil"></i></a>' +
        '</li>' +
        (d[i].estado !== estadoClaseRealizada && d[i].estado !== estadoClaseCancelada ? '<li>' +
            '<a href="javascript:void(0);" onclick="cancelarClase(' + d[i].id + ');" title="Cancelar clase"><i class="fa fa-remove"></i></a>' +
            '</li>' :
            '') +
        "<li>" +
        '<a href="javascript:void(0);" title="Eliminar clase" onclick="eliminarElemento(this, \'¿Está seguro que desea eliminar los datos de esta clase?\', null, true, function(){mostrarOcultarClases($(\'a[data-periodo=' + d[i].numeroPeriodo + ']\'), true);})" data-id="' + d[i].id + '" data-urleliminar="' + ((urlEliminarClase.replace('/0', '/' + d[i].id))) + '">' +
        "<i class='fa fa-trash'></i>" +
        "</a>" +
        "</li>" +
        '</ul>' +
        '</td>' +
        '</tr>';
  }
  return '<div class="box-body">' +
      '<div id="sec-mensajes-periodo-' + d[0].numeroPeriodo + '"></div>' +
      '<table id="tab-lista-clases-' + d[0].numeroPeriodo + '" class="table table-bordered table-hover sub-table">' +
      '<thead>' +
      '<tr>' +
      '<th>Datos</th>' +
      '<th>Opciones</th>' +
      '</tr>' +
      '</thead>' +
      '<tbody>' + htmlListaClases + '</tbody>' +
      '</table>' +
      '</div>';
}

//Formulario
function cargarFormularioClase() {
  $("#formulario-registrar-actualizar-clase").validate({
    ignore: ":hidden",
    rules: {
      numeroPeriodo: {
        required: true,
        validarEntero: true
      },
      estado: {
        required: true
      },
      fecha: {
        required: true,
        validarFecha: true
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
      }
    },
    submitHandler: function (f) {
      if (confirm($("#btn-guardar-clase").text().trim() === "Guardar"
          ? "¿Está seguro que desea guardar los cambios de los datos de la clase?"
          : "¿Está seguro que desea registrar los datos de esta clase?")) {
        $.blockUI({message: "<h4>" + ($("#btn-guardar-clase").text().trim() === "Guardar" ? "Guardando" : "Registrando") + " datos...</h4>"});
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
  //Registrar
  establecerCalendario("fecha-clase", false, false, false);
  establecerCampoHorario("hora-inicio-clase");
  establecerCampoDuracion("duracion-clase");
  $("#btn-nuevo-clase").click(function () {
    limpiarCamposClase();
    $("#titulo-formulario").text("Nueva clase");
    $("#btn-guardar-clase").text("Registrar");
    mostrarSeccionClase([2]);
  });
}
function editarClase(idClase) {
  obtenerDatosClase(idAlumno, idClase, function (d) {
    limpiarCamposClase();
    $("input[name='idClase']").val(d.id);
    $("#titulo-formulario").text("Editar clase");
    $("#numero-periodo-clase").val(d.numeroPeriodo);
    $("#estado-clase").val(d.estado);
    (d.estado === estadoClaseCancelada ? $("#sec-estado-clase").hide() : $("#sec-estado-clase").show());
    (d.estado === estadoClaseCancelada ? $("#sec-notificar-clase").hide() : $("#sec-notificar-clase").show());
    if (d.idHistorial !== null) {
      $("#notificar-clase").attr("checked", true);
      $("#notificar-clase").closest("label").addClass("checked");
    }
    var datFechaInicio = formatoFecha(d.fechaInicio).split("/");
    $("#fecha-clase").datepicker("setDate", (new Date(datFechaInicio[1] + "/" + datFechaInicio[0] + "/" + datFechaInicio[2])));
    $("#hora-inicio-clase").val(tiempoSegundos(d.fechaInicio));
    $("#duracion-clase").val(d.duracion);
    $("#costo-hora-clase").val(redondear(d.costoHora, 2));
    $("#id-pago-clase").val(d.idPago);
    $("#btn-guardar-clase").text("Guardar");

    if (d.idProfesor !== null && d.nombreProfesor !== null && d.nombreProfesor !== '') {
      $(".id-docente-clase").val(d.idProfesor);
      $(".nombre-docente-clase").html('<i class="fa flaticon-teach"></i> <b>' + d.nombreProfesor + ' ' + d.apellidoProfesor + '</b> <a href=' + (urlPerfilProfesor.replace('/0', '/' + d.idProfesor)) + ' title="Ver perfil del profesor" target="_blank"><i class="fa fa-eye"></i></a>');
      $("#costo-hora-docente").val(redondear(d.costoHoraProfesor, 2));
      mostrarSeccionClase([2, 1]);
    } else {
      mostrarSeccionClase([2]);
    }
  });
}

//Formulario Cancelar
function cargarFormularioCancelarClase() {
  tipoCancelacionClaseAlumno = (typeof (tipoCancelacionClaseAlumno) === "undefined" ? "" : tipoCancelacionClaseAlumno);
  if (tipoCancelacionClaseAlumno !== "") {
    $("#formulario-cancelar-clase").validate({
      ignore: ":hidden,:not(:visible)",
      rules: {
        pagoProfesor: {
          validarDecimalNegativo: true
        },
        fecha: {
          required: true,
          validarFecha: true
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
        }
      },
      submitHandler: function (f) {
        if (confirm("¿Está seguro que desea cancelar esta clase?")) {
          $.blockUI({message: "<h4>Cancelando clase...</h4>"});
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
    establecerCalendario("fecha-clase-reprogramada", false, false, false);
    establecerCampoHorario("hora-inicio-clase-reprogramada");
    establecerCampoDuracion("duracion-clase-reprogramada");
    $("#reprogramar-clase-cancelacion").change(verificarSeccionReprogramarClase);
  }
}
function cancelarClase(idClase) {
  obtenerDatosClase(idAlumno, idClase, function (d) {
    limpiarCamposClase();
    $("input[name='idClase']").val(d.id);
    if (d.idProfesor !== null && d.nombreProfesor !== null && d.nombreProfesor !== '') {
      $("#sec-campo-pago-profesor").show();
      $("input[name='idProfesor']").val(d.idProfesor);
    } else {
      $("#sec-campo-pago-profesor").hide();
    }
    var datFechaProximaClase = formatoFecha(d.fechaProximaClase).split("/");
    $("#fecha-clase-reprogramada").datepicker("setDate", (new Date(datFechaProximaClase[1] + "/" + datFechaProximaClase[0] + "/" + datFechaProximaClase[2])));
    $("#hora-inicio-clase-reprogramada").val(tiempoSegundos(d.fechaInicio));
    $("#duracion-clase-reprogramada").val(d.duracion);
    $("#costo-hora-clase-reprogramada").val(redondear(d.costoHora, 2));
    $("#id-pago-clase-reprogramada").val(d.idPago);
    mostrarSeccionClase([3, 1, 1]);
  });
}
function verificarSeccionReprogramarClase() {
  var repClase = $("#reprogramar-clase-cancelacion");
  ((repClase.is(":visible") && repClase.is(":checked")) ? $("#sec-clase-32").show() : $("#sec-clase-32").hide());
  (($("#sec-clase-32").is(":visible") && $(".id-docente-clase").val() !== "") ? $("#sec-clase-321").show() : $("#sec-clase-321").hide());
}

//Formulario Grupo
function cargarFormularioClasesGrupo() {
  $("#formulario-actualizar-clases").validate({
    ignore: ":hidden",
    rules: {
      numeroPeriodo: {
        required: true,
        validarEntero: true
      },
      estado: {
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
      }
    },
    submitHandler: function (f) {
      if ($("#editar-datos-generales-clases:checked, #editar-datos-tiempo-clases:checked, #editar-datos-pago-clases:checked, #editar-datos-profesor-clases:checked").length > 0) {
        if (confirm("¿Está seguro que desea guardar estos cambios?")) {
          $.blockUI({message: "<h4>Guardando datos...</h4>"});
          f.submit();
        }
      } else {
        agregarMensaje("advertencias", "Debe seleccionar un grupo de datos a editar.", true, "#sec-mensajes-clase");
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
  //Registrar
  establecerCampoHorario("hora-inicio-clases");
  establecerCampoDuracion("duracion-clases");
  $("#editar-datos-generales-clases, #editar-datos-tiempo-clases, #editar-datos-pago-clases, #editar-datos-profesor-clases").live("click", function () {
    (($(this).is(':checked')) ? $("#" + $(this).data("seccion")).show() : $("#" + $(this).data("seccion")).hide());
  });
}
function editarClasesGrupo(numeroPeriodo) {
  var idsClases = [];
  $.each($("#tab-lista-clases-" + numeroPeriodo).find("input[type='checkbox']:checked"), function (e, v) {
    idsClases.push($(v).data("id"));
  });
  obtenerDatosClasesGrupo(idsClases, function (d) {
    limpiarCamposClasesGrupo();
    $("#numero-periodo-clases").val(d.numeroPeriodo);
    $("#estado-clases").val(d.estado);
    $("#hora-inicio-clases").val(tiempoSegundos(d.fechaInicio));
    $("#duracion-clases").val(d.duracion);
    $("#costo-hora-clases").val(redondear(d.costoHora, 2));
    $("#id-pago-clases").val(d.idPago);
    $("input[name='idsClases']").val(idsClases);

    $("#editar-datos-generales-clases").attr("checked", true);
    $("#editar-datos-generales-clases").closest("label").addClass("checked");
    mostrarSeccionClase([4, 1]);
  });
}
function obtenerDatosClasesGrupo(idsClases, funcionRetorno) {
  urlDatosClasesGrupo = (typeof (urlDatosClasesGrupo) === "undefined" ? "" : urlDatosClasesGrupo);
  if (urlDatosClasesGrupo !== "") {
    $.blockUI({message: "<h4>Cargando...</h4>", baseZ: 2000});
    llamadaAjax(urlDatosClasesGrupo, "POST", {"ids": idsClases}, true,
        function (d) {
          if (funcionRetorno !== undefined)
            funcionRetorno(d);
          $("body").unblock();
        },
        function (d) {},
        function (de) {
          $('body').unblock({
            onUnblock: function () {
              agregarMensaje("errores", "Ocurrió un problema durante la carga de datos de las clases seleccionadas. Por favor inténtelo nuevamente.", true, "#sec-mensajes-clase");
            }
          });
        }
    );
  }
}
function limpiarCamposClasesGrupo(soloCamposDocente) {
  $(".id-docente-clase").val("");
  $(".nombre-docente-clase").html("");
  if (!soloCamposDocente) {
    $("#formulario-actualizar-clases").find(":input, select").each(function (i, e) {
      if (e.name !== "idAlumno" && e.name !== "_token") {
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
  }
}

//Común - Util
function cargarDocentesDisponiblesClase(recargarListaPeriodos, recrearTabla) {
  var formulario = ($("#formulario-cancelar-clase").is(":visible") ? $("#formulario-cancelar-clase") : ($("#formulario-actualizar-clases").is(":visible") ? $("#formulario-actualizar-clases") : $("#formulario-registrar-actualizar-clase")));
  var camposFormularioClase = formulario.find(":input, select").not(":hidden, input[name='pagoProfesor'], input[name='costoHoraDocente'], input[name='costoHora'], input[name='numeroPeriodo']");
  if (!camposFormularioClase.valid()) {
    return false;
  }

  $('#mod-docentes-disponibles-clase').modal('show');
  if ($.fn.DataTable.isDataTable('#tab-lista-docentes-clase') && recrearTabla) {
    $('#tab-lista-docentes-clase').DataTable().destroy();
  }
  if ($.fn.DataTable.isDataTable('#tab-lista-docentes-clase')) {
    if (recargarListaPeriodos) {
      $('#tab-lista-docentes-clase').DataTable().ajax.reload();
    }
  } else {
    urlListarDocentesDisponiblesClase = (typeof (urlListarDocentesDisponiblesClase) === "undefined" ? "" : urlListarDocentesDisponiblesClase);
    if (urlListarDocentesDisponiblesClase !== "" && urlPerfilProfesor !== "") {
      $('#tab-lista-docentes-clase').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
          "url": urlListarDocentesDisponiblesClase,
          "type": "POST",
          "data": function (d) {
            d.tipoDocente = $("#tipo-docente-disponible-clase").val();
            d.sexoDocente = $("#sexo-docente-disponible-clase").val();
            d.idCursoDocente = $("#id-curso-docente-disponible-clase").val();
            var fDatos = formulario.serializeArray();
            $(fDatos).each(function (i, o) {
              d[o.name] = o.value;
            });
          }
        },
        autoWidth: false,
        columns: [
          {data: "nombreCompleto", name: "nombreCompleto", render: function (e, t, d, m) {
              return d.nombreCompleto + ' <a href=' + (urlPerfilProfesor.replace('/0', '/' + d.id)) + ' title="Ver perfil del profesor" target="_blank"><i class="fa fa-eye"></i></a>';
            }},
          {data: "id", name: "id", orderable: false, "searchable": false, width: "5%"}
        ],
        createdRow: function (r, d, i) {
          $("td", r).eq(1).html('<input type="radio" name="idDocenteDisponibleClase" value="' + d.id + '" data-nombrecompleto="' + d.nombreCompleto + '"/>');
          $("td", r).eq(1).addClass("text-center");
        },
        initComplete: function (s, j) {
          establecerBotonRecargaTabla("tab-lista-docentes-clase");
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
  $(".id-docente-clase").val("");
  $(".nombre-docente-clase").html("");
  if (!soloCamposDocente) {
    $("#formulario-registrar-actualizar-clase, #formulario-cancelar-clase").find(":input, select").each(function (i, e) {
      if (e.name !== "idAlumno" && e.name !== "_token") {
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
  }
}
function verDatosPagosClase(idElemento) {
  if ($("#" + idElemento).val() !== "") {
    verDatosPago($("#" + idElemento).val());
  }
}
var cargandoDatosClaseXHorario = false;
function cambioFechaHorario(idsClases, fecha, horaInicio, duracion, numFormulario) {
  //numFormulario => 1:Formulario cancelar, 2: Formulario grupo, 3: Formulario clásico
  (numFormulario === 1 ? $("#sec-clase-321").hide() : (numFormulario === 2 ? $("#sec-clase-441").hide() : mostrarSeccionClase([2])));
  limpiarCamposClase(true);
  urlTotalClasesXHorario = (typeof (urlTotalClasesXHorario) === "undefined" ? "" : urlTotalClasesXHorario);
  if (urlTotalClasesXHorario !== "" && !cargandoDatosClaseXHorario && ((idsClases !== "" && idsClases !== null) || ((idsClases === "" || idsClases === null) && (fecha !== "" && fecha !== null))) && (horaInicio !== "" && horaInicio !== null) && (duracion !== "" && duracion !== null)) {
    cargandoDatosClaseXHorario = true;
    llamadaAjax(urlTotalClasesXHorario, "POST", {"ids": idsClases, "fecha": fecha, "horaInicio": horaInicio, "duracion": duracion}, true,
        function (d) {
          if (parseInt(d) > 0) {
            agregarMensaje("advertencias", "El alumno ya tiene programada un clase para este fecha en el horario seleccionado.", true, "#sec-mensajes-clase");
          }
        }, function (d) {
      setTimeout(function () {
        cargandoDatosClaseXHorario = false;
      }, 1000);
    });
  }
}