window.addEventListener("load", verificarJqueryClase, false);
function verificarJqueryClase() {
  ((window.jQuery && jQuery.ui) ? cargarSeccionClases() : window.setTimeout(verificarJqueryClase, 100));
}
function  cargarSeccionClases() {
  //Urls y datos  
  urlPerfilProfesor = (typeof (urlPerfilProfesor) === "undefined" ? "" : urlPerfilProfesor);
  
  urlListarPeriodos = (typeof (urlListarPeriodos) === "undefined" ? "" : urlListarPeriodos);
  urlListarClases = (typeof (urlListarClases) === "undefined" ? "" : urlListarClases);
  urlActualizarEstadoClase = (typeof (urlActualizarEstadoClase) === "undefined" ? "" : urlActualizarEstadoClase);
  urlDatosClase = (typeof (urlDatosClase) === "undefined" ? "" : urlDatosClase);
  urlEliminarClase = (typeof (urlEliminarClase) === "undefined" ? "" : urlEliminarClase);
  urlListarDocentesDisponiblesClase = (typeof (urlListarDocentesDisponiblesClase) === "undefined" ? "" : urlListarDocentesDisponiblesClase);
  
  estadosClase = (typeof (estadosClase) === "undefined" ? "" : estadosClase);
  estadoClaseRealizada = (typeof (estadoClaseRealizada) === "undefined" ? "" : estadoClaseRealizada);
  estadoClaseCancelada = (typeof (estadoClaseCancelada) === "undefined" ? "" : estadoClaseCancelada);
  
  tipoCancelacionClaseAlumno = (typeof (tipoCancelacionClaseAlumno) === "undefined" ? "" : tipoCancelacionClaseAlumno);
  
  minHorario = (typeof (minHorario) === "undefined" ? "" : minHorario);
  maxHorario = (typeof (maxHorario) === "undefined" ? "" : maxHorario);
  minHorasClase = (typeof (minHorasClase) === "undefined" ? "" : minHorasClase);
  maxHorasClase = (typeof (maxHorasClase) === "undefined" ? "" : maxHorasClase);
  
  estadosPago = (typeof (estadosPago) === "undefined" ? "" : estadosPago);
  
  cargarListaPeriodos();
  cargarFormularioClase();
  cargarFormularioCancelarClase();
  mostrarSeccionClase();

  //Común    
  $(".btn-docentes-disponibles-clase").click(cargarDocentesDisponiblesClase);
  $("#genero-docente-disponible-clase, #id-curso-docente-disponible-clase, #tipo-docente-disponible-clase").change(function () {
    cargarDocentesDisponiblesClase(true);
  });
  $("#btn-confirmar-docente-disponible-clase").click(function () {
    if (urlPerfilProfesor !== "") {
      limpiarCamposClase(true);
      var docenteDisponibleClase = $("input[name='idDocenteDisponibleClase']:checked");
      if (docenteDisponibleClase.length > 0) {
        $(".id-docente-clase").val(docenteDisponibleClase.val());
        $(".nombre-docente-clase").html((docenteDisponibleClase.val() !== '' ? '<i class="fa flaticon-teach"></i> <b>' + docenteDisponibleClase.data('nombrecompleto') + '</b> <a href=' + (urlPerfilProfesor.replace('/0', '/' + docenteDisponibleClase.val())) + ' title="Ver perfil del profesor" target="_blank"><i class="fa fa-eye"></i></a>' : ''));
        if ($("#formulario-registrar-actualizar-clase").is(":visible")) {
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
function cargarListaPeriodos() {
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
        $("td", r).eq(4).addClass("text-center");
        $('td', r).eq(4).html('<a href="javascript:void(0);" onclick="mostrarOcultarClases(this);" class="btn btn-primary btn-xs" data-periodo="' + d.numeroPeriodo + '"><i class="fa fa-eye"></i> Ver clases</button>');
      }
    });
  }

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
      llamadaAjax(urlActualizarEstadoClase, "POST", {"idClase": idClase, "idAlumno": idAlumno, "estado": $(this).val()}, true);
    }
    $(this).closest(".sec-btn-editar-estado-clase").append('<a href="javascript:void(0);" class="btn-editar-estado-clase" data-idclase="' + idClase + '" data-idalumno="' + idAlumno + '" data-estado="' + $(this).val() + '"><span class="label ' + estadosClase[$(this).val()][1] + ' btn_estado">' + estadosClase[$(this).val()][0] + '</span></a>');
    $(this).remove();
  });
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
  if (urlListarClases !== "" && urlEliminarClase !== "" && urlPerfilProfesor !== "" && estadosClase !== "" && estadoClaseRealizada !== "" && estadoClaseCancelada !== "" && estadosPago !== "") {
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
                  + '<b>Profesor:</b> ' + (d[i].idProfesor !== null ? '<a target="_blank" href="' + urlPerfilProfesor.replace("/0", "/" + d[i].idProfesor) + '">' + d[i].nombreProfesor + ' ' + d[i].apellidoProfesor + (d[i].estadoPagoProfesor !== null ? '<br/><span class="label ' + estadosPago[d[i].estadoPagoProfesor][1] + ' btn_estado">Pago al profesor - ' + estadosPago[d[i].estadoPagoProfesor][0] + '</span>' : '') + '</a>' : 'Sin profesor asignado') +
                  '</td>' +
                  '<td>' +
                  '<input type="checkbox" disabled="disabled"' + (d[i].idHistorial !== null ? ' checked="checked"' : '') + '/>' +
                  '</td>' +
                  '<td class="text-center">' +
                  '<div class="sec-btn-editar-estado-clase"><a href="javascript:void(0);" class="btn-editar-estado-clase" data-idclase="' + d[i].id + '" data-idalumno="' + d[i].idAlumno + '" data-estado="' + d[i].estado + '"><span class="label ' + estadosClase[d[i].estado][1] + ' btn_estado">' + estadosClase[d[i].estado][0] + '</span></a></div>' +
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
              agregarMensaje("errores", "Ocurrió un problema durante la carga de lista de clases del período seleccionado. Por favor inténtelo nuevamente.", true, "#sec-mensajes-clase");
            }
          });
        }
    );
  }
}

//Formulario
function cargarFormularioClase() {
  $("#formulario-registrar-actualizar-clase").validate({
    ignore: ":hidden",
    rules: {
      fecha: {
        required: true
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
      },
      numeroPeriodo: {
        required: true,
        validarEntero: true
      }
    },
    submitHandler: function (form) {
      if (confirm($("#btn-guardar").text() === "Guardar"
          ? "¿Está seguro que desea guardar los cambios de los datos de la clase?"
          : "¿Está seguro que desea registrar los datos de este clase?"))
        form.submit();
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
    $("#btn-guardar").text("Registrar");
    mostrarSeccionClase([2]);
  });
}
function editarClase(idClase) {
  obtenerDatosClase(idClase, function (d) {
    limpiarCamposClase();
    $("#numero-periodo-clase").val(d.numeroPeriodo);
    $("#estado-clase").val(d.estado);
    if (d.idHistorial !== null) {
      $("#notificar-clase").attr("checked", true);
      $("#notificar-clase").closest("label").addClass("checked");
    }
    $("#fecha-clase").val(formatoFecha(d.fechaInicio));
    $("#hora-inicio-clase").val(tiempoSegundos(d.fechaInicio));
    $("#duracion-clase").val(d.duracion);
    $("#costo-hora-clase").val(redondear(d.costoHora, 2));
    $("input[name='idClase']").val(d.id);
    $("#btn-guardar").text("Guardar");

    if (d.idProfesor !== null) {
      $(".id-docente-clase").val(d.idProfesor);
      $(".nombre-docente-clase").html('<i class="fa flaticon-teach"></i> <b>' + d.nombreProfesor + ' ' + d.apellidoProfesor + '</b> <a href=' + (urlPerfilProfesor.replace('/0', '/' + d.idProfesor)) + ' title="Ver perfil del profesor" target="_blank"><i class="fa fa-eye"></i></a>');
      $("#costo-hora-docente").val(redondear(d.costoHoraProfesor, 2));
      mostrarSeccionClase([2, 1]);
    } else {
      mostrarSeccionClase([2]);
    }
  });
}
function obtenerDatosClase(idClase, funcionRetorno) {
  if (urlDatosClase !== "") {
    $.blockUI({message: "<h4>Cargando...</h4>", baseZ: 2000});
    llamadaAjax(urlDatosClase.replace("/0", "/" + idClase), "POST", {}, true,
        function (d) {
          if (funcionRetorno !== undefined)
            funcionRetorno(d);
          $("body").unblock();
        },
        function (d) {},
        function (de) {
          $('body').unblock({
            onUnblock: function () {
              agregarMensaje("errores", "Ocurrió un problema durante la carga de datos de la clase seleccionada. Por favor inténtelo nuevamente.", true, "#sec-mensajes-clase");
            }
          });
        }
    );
  }
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
  establecerCampoHorario("hora-inicio-clase-reprogramada");
  establecerCampoDuracion("duracion-clase-reprogramada");
  $("#tipo-cancelacion-clase").change(function () {
    (($(this).val() === tipoCancelacionClaseAlumno) ? mostrarSeccionClase([3, 1]) : mostrarSeccionClase([3, 2]));
    verificarSeccionReprogramarClase();
  });
  $("#reprogramar-clase-can-alu, #reprogramar-clase-can-pro").change(verificarSeccionReprogramarClase);
}
function cancelarClase(idClase) {
  obtenerDatosClase(idClase, function (d) {
    limpiarCamposClase();
    $("input[name='idClase']").val(d.id);
    if (d.idProfesor !== null) {
      $("input[name='idProfesorClaseCancelada']").val(d.idProfesor);
    }
    $("#hora-inicio-clase-reprogramada").val(tiempoSegundos(d.fechaInicio));
    $("#duracion-clase-reprogramada").val(d.duracion);
    mostrarSeccionClase([3, 1, 1]);
  });
}
function verificarSeccionReprogramarClase() {
  var repClaseAlu = $("#reprogramar-clase-can-alu");
  var repClasePro = $("#reprogramar-clase-can-pro");
  (((repClaseAlu.is(":visible") && repClaseAlu.is(":checked")) || (repClasePro.is(":visible") && repClasePro.is(":checked"))) ? $("#sec-clase-33").show() : $("#sec-clase-33").hide());
  (($("#sec-clase-33").is(":visible") && $(".id-docente-clase").val() !== "") ? $("#sec-clase-331").show() : $("#sec-clase-331").hide());
}

//Común - Util
function cargarDocentesDisponiblesClase(recargarListaPeriodos) {
  var formulario = ($("#formulario-cancelar-clase").is(":visible") ? $("#formulario-cancelar-clase") : $("#formulario-registrar-actualizar-clase"));
  var camposFormularioCancelarClase = formulario.find(":input, select").not(":hidden, input[name='pagoProfesor'], input[name='costoHoraDocente'], input[name='costoHora'], input[name='numeroPeriodo']");
  if (!camposFormularioCancelarClase.valid()) {
    return false;
  }

  $('#mod-docentes-disponibles-clase').modal('show');
  if ($.fn.DataTable.isDataTable('#tab-lista-docentes-clase')) {
    if (recargarListaPeriodos) {
      $('#tab-lista-docentes-clase').DataTable().ajax.reload();
    }
  } else {
    if (urlListarDocentesDisponiblesClase !== "" && urlPerfilProfesor !== "") {
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
          $('td', r).eq(0).html(d.nombreCompleto + ' <a href=' + (urlPerfilProfesor.replace("/0", "/" + d.id)) + ' title="Ver perfil del profesor" target="_blank"><i class="fa fa-eye"></i></a>');

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
  $(".id-docente-clase").val("");
  $(".nombre-docente-pago").html("");
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