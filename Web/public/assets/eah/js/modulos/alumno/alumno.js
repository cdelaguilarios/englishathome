var mapa;

$(document).ready(function () {
  cargarLista();
  cargarCajaBusqueda();
  cargarListaClase();
  cargarFormularioComentarios();
  cargarFormulario();
});

/* - */function cargarLista() {
  urlListar = (typeof (urlListar) === "undefined" ? "" : urlListar);
  urlPerfil = (typeof (urlPerfil) === "undefined" ? "" : urlPerfil);
  urlEditar = (typeof (urlEditar) === "undefined" ? "" : urlEditar);
  urlEliminar = (typeof (urlEliminar) === "undefined" ? "" : urlEliminar);
  urlPerfilProfesor = (typeof (urlPerfilProfesor) === "undefined" ? "" : urlPerfilProfesor);

  estados = (typeof (estados) === "undefined" ? "" : estados);
  estadosDisponibleCambio = (typeof (estadosDisponibleCambio) === "undefined" ? "" : estadosDisponibleCambio);
  estadoCuotaProgramada = (typeof (estadoCuotaProgramada) === "undefined" ? "" : estadoCuotaProgramada);

  if (urlListar !== "" && urlPerfil !== "" && urlEditar !== "" && urlEliminar !== "" && urlPerfilProfesor !== "" && estados !== "" && estadosDisponibleCambio !== "" && estadoCuotaProgramada !== "") {
    $("#tab-lista").DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: urlListar,
        type: "POST",
        data: function (d) {
          d._token = $("meta[name=_token]").attr("content");
          d.estado = $("#bus-estado").val();
        }
      },
      autoWidth: false,
      responsive: true,
      orderCellsTop: true,
      fixedHeader: true,
      order: [[6, "desc"]],
      rowId: 'idEntidad',
      columns: [
        {data: "", name: "", orderable: false, "searchable": false, render: function (data, type, row, meta) {
            return meta.row + meta.settings._iDisplayStart + 1;
          }, "className": "text-center not-mobile"},
        {data: "nombre", name: "entidad.nombre", render: function (e, t, d, m) {
            return '<a href="' + (urlPerfil.replace("/0", "/" + d.id)) + '">' + (d.nombre !== null ? d.nombre : "") + " " + (d.apellido !== null ? d.apellido : "") + '</a>' +
                    (d.distritoAlumno ? '<br/><span class="text-info"><i class="fa fa-street-view"></i> ' + util.letraCapital(d.distritoAlumno) + '</span>' : '') +
                    (d.nombreProfesor ?
                            '<br/><br/>Profesor(a): <a href="' + (urlPerfilProfesor.replace("/0", "/" + d.idProfesor)) + '">' + (d.nombreProfesor !== null ? d.nombreProfesor : "") + " " + (d.apellidoProfesor !== null ? d.apellidoProfesor : "") + '</a>' +
                            (d.distritoProfesor ? '<br/><span class="text-info"><i class="fa fa-street-view"></i> ' + util.letraCapital(d.distritoProfesor) + '</span>' : '')
                            : '');
          }},
        {data: "porcentajeAvanceClases", name: "porcentajeAvanceClases", width: "25%", render: function (e, t, d, m) {
            if (d.duracionTotalClases) {
              var porcentajeAvance = (d.duracionTotalClasesRealizadas ? d.porcentajeAvanceClases : 0);
              return '<div class="clearfix">' +
                      '<span class="pull-left">Total de clases: ' + d.totalClases + '</span>' +
                      '<a href="javascript:void(0);" onclick="abrirModalListaClases(' + d.idEntidad + ');" title="Ver lista de clases" class="btn-ver-lista-clases">' +
                      '<i class="fa fa-eye"></i>' +
                      '</a>' +
                      '<small class="pull-right">' + util.redondear(porcentajeAvance, 2) + ' %</small>' +
                      '</div>' +
                      '<div class="progress xs">' +
                      '<div class="progress-bar progress-bar-green" style="width: ' + porcentajeAvance + '%;"></div>' +
                      '</div>' +
                      '<div class="clearfix">' +
                      '<span class="pull-left">' +
                      '<span class="text-green" data-toggle="tooltip" title="" data-original-title="Horas realizadas">' +
                      '<i class="fa fa-clock-o"></i> ' + utilFechasHorarios.formatoHora(d.duracionTotalClasesRealizadas) +
                      '</span>  de  <span class="text-info" data-toggle="tooltip" title="" data-original-title="Horas programadas"><i class="fa fa-clock-o"></i> ' + utilFechasHorarios.formatoHora(d.duracionTotalClases) + '</span>' +
                      '</span>' +
                      '</div>';
            } else {
              return 'Sin clases registradas';
            }
          }, "className": "not-mobile"},
        {data: "curso", name: "curso", render: function (e, t, d, m) {
            return d.curso + '<div id="sec-info-horario-' + d.id + '"></div>';
          }, "className": "not-mobile"},
        {data: "estado", name: "entidad.estado", render: function (e, t, d, m) {
            var estado = '';
            if (estados[d.estado] !== undefined && estadosDisponibleCambio[d.estado] !== undefined) {
              estado = '<div class="sec-btn-editar-estado" data-idtabla="tab-lista" data-idselestados="sel-estados" data-tipocambio="1">' +
                      '<a href="javascript:void(0);" class="btn-editar-estado" data-id="' + d.id + '" data-estado="' + d.estado + '">' +
                      '<span class="label ' + estados[d.estado][1] + ' btn-estado">' + estados[d.estado][0] + '</span>' +
                      '</a>' +
                      '</div>' +
                      (d.estado === estadoCuotaProgramada && d.fechaUltimaClase ? '<small class="text-red">(Última clase: ' + utilFechasHorarios.formatoFecha(d.fechaUltimaClase) + ')</small><br/>' : '');
            } else if (estados[d.estado] !== undefined) {
              estado = '<span class="label ' + estados[d.estado][1] + ' btn-estado">' + estados[d.estado][0] + '</span><br/>' +
                      (d.estado === estadoCuotaProgramada && d.fechaUltimaClase ? '<small class="text-red">(Última clase: ' + utilFechasHorarios.formatoFecha(d.fechaUltimaClase) + ')</small><br/>' : '');
            }
            return estado + '<span class="text-info">(Nivel ' + d.nivelIngles + ')</span>';
          }, "className": "text-center not-mobile"},
        {data: "totalPagos", name: "totalPagos", render: function (e, t, d, m) {
            return 'S/. ' + util.redondear(d.pagoAcumulado, 2) + '<br/>' +
                    '<span class="text-info">(' + d.totalPagos + ' pago' + (d.totalPagos > 1 ? 's' : '') + ')</span>';
          }, "className": "text-center not-mobile"},
        {data: "fechaRegistro", name: "entidad.fechaRegistro", width: "12%", render: function (e, t, d, m) {
            return utilFechasHorarios.formatoFecha(d.fechaRegistro, true) + '<br/>' +
                    '<span class="text-info">(Inicio de clases:<br/>' + utilFechasHorarios.formatoFecha(d.fechaInicioClase) + ')</span>';
          }, "className": "text-center not-mobile"},
        {data: "id", name: "entidad.id", orderable: false, "searchable": false, width: "5%", render: function (e, t, d, m) {
            return '<ul class="buttons">' +
                    '<li>' +
                    '<a href="' + (urlPerfil.replace("/0", "/" + d.id)) + '" title="Ver perfil"><i class="fa fa-eye"></i></a>' +
                    '</li>' +
                    '<li>' +
                    '<a href="' + (urlEditar.replace("/0", "/" + d.id)) + '" title="Editar datos"><i class="fa fa-pencil"></i></a>' +
                    '</li>' +
                    '<li>' +
                    '<a href="javascript:void(0);" title="Eliminar alumno" onclick="utilTablas.eliminarElemento(this, \'¿Está seguro que desea eliminar los datos de este alumno?\', \'tab-lista\')" data-id="' + d.id + '" data-urleliminar="' + ((urlEliminar.replace("/0", "/" + d.id))) + '">' +
                    '<i class="fa fa-trash"></i>' +
                    '</a>' +
                    '</li>' +
                    '</ul>';
          }, className: "text-center"}
      ],
      initComplete: function (s, j) {
        utilTablas.establecerBotonRecargaTabla("tab-lista");
        utilTablas.establecerCabecerasBusquedaTabla("tab-lista");
      },
      drawCallback: function (s) {
        CargarHorarios();
      }
    });
  }
}
function cargarCajaBusqueda() {
  urlBuscar = (typeof (urlBuscar) === "undefined" ? "" : urlBuscar);
  urlEditar = (typeof (urlEditar) === "undefined" ? "" : urlEditar);
  urlPerfil = (typeof (urlPerfil) === "undefined" ? "" : urlPerfil);
  idAlumno = (typeof (idAlumno) === "undefined" ? "" : idAlumno);
  nombreCompletoAlumno = (typeof (nombreCompletoAlumno) === "undefined" ? "" : nombreCompletoAlumno);

  if (urlBuscar !== "" && idAlumno !== "" && nombreCompletoAlumno !== "") {
    establecerListaBusqueda("#sel-alumno", urlBuscar);
    $("#sel-alumno").empty().append('<option value="' + idAlumno + '">' + nombreCompletoAlumno + '</option>').val(idAlumno);
    $("#sel-alumno").change(function () {
      if ($(this).data("seccion") === "perfil" && urlPerfil !== "" && $(this).val() !== this.options[this.selectedIndex].innerHTML)
        window.location.href = urlPerfil.replace("/0", "/" + $(this).val());
      else if (urlEditar !== "" && $(this).val() !== this.options[this.selectedIndex].innerHTML)
        window.location.href = urlEditar.replace("/0", "/" + $(this).val());
    });
  }
}

var datosHorariosCargados = [];
function CargarHorarios() {
  urlHorarioMultiple = (typeof (urlHorarioMultiple) === "undefined" ? "" : urlHorarioMultiple);
  if (urlHorarioMultiple !== "") {
    //Horarios
    var ids = jQuery.map($("#tab-lista").DataTable().rows().ids(), function (ele) {
      if (!datosHorariosCargados.some(function (dhc) {
        return dhc.idEntidad === ele;
      })) {
        return ele;
      }
    });
    if (ids.length > 0) {
      util.llamadaAjax(urlHorarioMultiple, "POST", {"idsEntidades": ids}, true, function (datos) {
        datosHorariosCargados = datosHorariosCargados.concat(datos);
        MostrarHorarios();
      });
    } else {
      MostrarHorarios();
    }
  }
}
function MostrarHorarios() {
  datosHorariosCargados.forEach(function (d) {
    $("#sec-info-horario-" + d.idEntidad).html(horario.obtenerTexto($.parseJSON(d.datosHorario)));
  });
}

function cargarListaClase() {
  urlListarClases = (typeof (urlListarClases) === "undefined" ? "" : urlListarClases);
  urlPerfilProfesor = (typeof (urlPerfilProfesor) === "undefined" ? "" : urlPerfilProfesor);
  estadosClase = (typeof (estadosClase) === "undefined" ? "" : estadosClase);

  if (urlListarClases !== "" && urlPerfilProfesor !== "" && estadosClase !== "") {
    $("#tab-lista-clases").DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: urlListarClases,
        type: "POST",
        data: function (d) {
          d._token = $("meta[name=_token]").attr("content");
          d.idAlumno = $("#mod-lista-clases").find("input[name='idAlumno']").val();
        }
      },
      autoWidth: false,
      responsive: true,
      order: [[1, "desc"]],
      rowId: 'id',
      columns: [
        {data: "", name: "", orderable: false, "searchable": false, "className": "text-center not-mobile",
          render: function (data, type, row, meta) {
            return meta.row + meta.settings._iDisplayStart + 1;
          }},
        {data: "fechaInicio", name: "fechaInicio", render: function (e, t, d, m) {
            var fechaConfirmacionIni = "";
            if (d.fechaConfirmacion !== null && !isNaN(Date.parse(d.fechaConfirmacion))) {
              fechaConfirmacionIni = new Date(d.fechaConfirmacion);
              fechaConfirmacionIni.setSeconds(fechaConfirmacionIni.getSeconds() - d.duracion);
            }
            return '<b>Fecha:</b> ' + utilFechasHorarios.formatoFecha(d.fechaInicio) + ' - De ' + utilFechasHorarios.formatoFecha(d.fechaInicio, false, true) + ' a ' + utilFechasHorarios.formatoFecha(d.fechaFin, false, true) + '<br/>' +
                    (d.fechaConfirmacion !== null ? '<b>Fecha de confirmación:</b> ' + utilFechasHorarios.formatoFecha(d.fechaConfirmacion) + ' - De ' + utilFechasHorarios.formatoFecha(fechaConfirmacionIni, false, true) + ' a ' + utilFechasHorarios.formatoFecha(d.fechaConfirmacion, false, true) + '<br/>' : '') +
                    '<b>Duración:</b> ' + utilFechasHorarios.formatoHora(d.duracion) + '<br/>' +
                    (d.idHistorial !== null ? '<b>Notificar:</b> ' + ' <i class="fa fa-check icon-notificar-clase"></i>' + '<br/>' : '') +
                    '<b>Profesor(a):</b> ' + (d.idProfesor !== null ? '<a href="' + (urlPerfilProfesor.replace("/0", "/" + d.idProfesor)) + '">' + (d.nombreProfesor !== null ? d.nombreProfesor : "") + " " + (d.apellidoProfesor !== null ? d.apellidoProfesor : "") + '</a>' : 'Sin profesor asignad');
          }},
        {data: "estado", name: "estado", width: "13%", render: function (e, t, d, m) {
            var estado = '';
            if (estadosClase[d.estado] !== undefined && estadosClaseDisponibleCambio[d.estado] !== undefined) {
              estado = '<div class="sec-btn-editar-estado" data-idtabla="tab-lista-clases" data-idselestados="sel-estados-clase" data-tipocambio="1">' +
                      '<a href="javascript:void(0);" class="btn-editar-estado" data-id="' + d.id + '" data-estado="' + d.estado + '">' +
                      '<span class="label ' + estadosClase[d.estado][1] + ' btn-estado">' + estadosClase[d.estado][0] + '</span>' +
                      '</a>' +
                      '</div>';
            } else if (estadosClase[d.estado] !== undefined) {
              estado = '<span class="label ' + estadosClase[d.estado][1] + ' btn-estado">' + estadosClase[d.estado][0] + '</span>';
            }
            return estado;
          }, className: "text-center"},
        {data: "comentarioAlumno", name: "comentarioAlumno", width: "50%", render: function (e, t, d, m) {
            var incluirComentario = function (idClase, titulo, tipo, comentario) {
              var maxTexto = 200;
              return '<b>' + titulo + ':</b> ' + (comentario ? comentario.substring(0, maxTexto) + (comentario.length > maxTexto ? '...' : '') : '<i>Sin comentarios</i>') + ' <a href="javascript:void(0);" onclick="abrirModalFormularioComentarios(' + idClase + ', ' + tipo + ');" title="Ver/editar comentarios"><i class="fa fa-eye"></i></a>' + '<br/><br/>';
            };
            return incluirComentario(d.id, 'Del alumno', 1, d.comentarioAlumno) +
                    incluirComentario(d.id, 'Del profesor', 2, d.comentarioProfesor) +
                    incluirComentario(d.id, 'De EAH para el alumno', 3, d.comentarioParaAlumno) +
                    incluirComentario(d.id, 'De EAH para el profesor', 4, d.comentarioParaProfesor);
          }, "className": "not-mobile"}
      ],
      initComplete: function (s, j) {
        utilTablas.establecerBotonRecargaTabla("tab-lista-clases");
        utilTablas.establecerCabecerasBusquedaTabla("tab-lista-clases");
      }
    });
  }
}
function abrirModalListaClases(id) {
  var tr = $("#" + id);
  var fila = $("#tab-lista").DataTable().row(tr);
  var datosAlumno = fila.data();

  $.blockUI({message: "<h4>Cargando...</h4>"});
  actualizarListaClases(datosAlumno.id, function () {
    $("#mod-lista-clases").modal("show");
    $("#mod-lista-clases").find(".modal-title").html("Lista de clases " + (datosAlumno.sexo === "F" ? "de la alumna " : "del alumno ") + datosAlumno.nombre + " " + datosAlumno.apellido);
    $("body").unblock();
  }, function () {
    $("body").unblock();
  });
}

function cargarFormularioComentarios() {
  $("#formulario-comentarios").validate({
    rules: {
      comentario: {
        required: true
      }
    },
    submitHandler: function (f) {
      if (confirm("¿Está seguro que desea guardar los cambios de estos comentarios?")) {
        $.blockUI({message: "<h4>Guardando...</h4>"});
        var datos = procesarDatosFormulario(f);
        util.llamadaAjax($(f).attr("action"), "POST", datos, true,
                function (d) {
                  $("body").unblock({
                    onUnblock: function () {
                      agregarMensaje("exitosos", d["mensaje"], true, "#sec-men-lista-clases");
                    }
                  });
                },
                function (d) {
                  $("#mod-comentarios").modal("hide");
                  $("#formulario-comentarios").find("textarea[name='comentario']").val("");
                  $("#tab-lista-clases").DataTable().ajax.reload();
                },
                function (de) {
                  $("body").unblock({
                    onUnblock: function () {
                      var res = de["responseJSON"];
                      if (res["mensaje"]) {
                        agregarMensaje("errores", res["mensaje"], true, "#sec-men-lista-clases");
                      } else {
                        agregarMensaje("errores", res[Object.keys(res)[0]][0], true, "#sec-men-lista-clases");
                      }
                    }
                  });
                }
        );
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
}
function abrirModalFormularioComentarios(idClase, tipo) {
  var tr = $("#" + idClase);
  var fila = $("#tab-lista-clases").DataTable().row(tr);
  var datosFila = fila.data();
  $("#mod-comentarios").find(".modal-title").html("Comentarios " + (tipo === 1 ? "del alumno" : (tipo === 2 ? "del profesor" : (tipo === 3 ? "De EAH para el alumno" : "De EAH para el profesor"))));
  $("#formulario-comentarios").find("textarea[name='comentario']").val(tipo === 1 ? datosFila.comentarioAlumno : (tipo === 2 ? datosFila.comentarioProfesor : (tipo === 3 ? datosFila.comentarioParaAlumno : datosFila.comentarioParaProfesor)));
  $("#formulario-comentarios").find("input[name='idClase']").val(datosFila.id);
  $("#formulario-comentarios").find("input[name='idAlumno']").val(datosFila.idAlumno);
  $("#formulario-comentarios").find("input[name='tipo']").val(tipo);
  $("#mod-comentarios").modal("show").on('hidden.bs.modal', function () {
    $("body").addClass("modal-open");
  });
}

function cargarFormulario() {
  minHorasClase = (typeof (minHorasClase) === "undefined" ? "" : minHorasClase);
  maxHorasClase = (typeof (maxHorasClase) === "undefined" ? "" : maxHorasClase);
  urlActualizarHorario = (typeof (urlActualizarHorario) === "undefined" ? "" : urlActualizarHorario);
  $("#formulario-alumno").validate({
    ignore: "",
    rules: {
      nombre: {
        required: true,
        validarAlfabetico: true
      },
      apellido: {
        required: true,
        validarAlfabetico: true
      },
      telefono: {
        required: ($("input[name='usuarioNoLogueado']").val() === "1")
      },
      fechaNacimiento: {
        required: ($("input[name='usuarioNoLogueado']").val() === "1"),
        validarFecha: true
      },
      idTipoDocumento: {
        required: true
      },
      numeroDocumento: {
        required: ($("input[name='usuarioNoLogueado']").val() === "1"),
        number: true
      },
      correoElectronico: {
        required: true,
        email: true
      },
      imagenPerfil: {
        validarImagen: true
      },
      codigoDepartamento: {
        required: true
      },
      codigoProvincia: {
        required: true
      },
      codigoDistrito: {
        required: true
      },
      direccion: {
        required: true
      },
      numeroHorasClase: {
        required: true,
        validarDecimal: true,
        range: [(minHorasClase * 3600), (maxHorasClase * 3600)]
      },
      fechaInicioClase: {
        required: true,
        validarFecha: true
      },
      costoHoraClase: {
        required: true,
        validarDecimal: true
      }
    },
    submitHandler: function (f) {
      if ($.parseJSON($("input[name='horario']").val()) !== null && $.parseJSON($("input[name='horario']").val()).length > 0) {
        if (confirm($("input[name='modoEditar']").val() === "1"
                ? "¿Está seguro que desea guardar los cambios de los datos del alumno?"
                : "¿Está seguro que desea registrar estos datos?")) {
          $.blockUI({message: "<h4>" + ($("input[name='modoEditar']").val() === "1" ? "Guardando" : "Registrando") + " datos...</h4>"});
          f.submit();
        }
      } else {
        agregarMensaje("advertencias", "Debe ingresar un horario disponible para sus clases.", true, "#sec-men-alerta-horario");
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
    invalidHandler: function (e, v) {
      if (v.errorList.length > 0 && $(v.errorList[0].element).closest(".step-pane").data("step") !== undefined) {
        $('#wiz-registro-alumno').wizard('selectedItem', {step: $(v.errorList[0].element).closest(".step-pane").data("step")});
      }
    },
    onfocusout: false,
    onkeyup: false,
    onclick: false
  });
  if ($("input[name='modoEditarRegistrar']").val() === "1") {
    establecerWizard("alumno", ($("input[name='modoEditar']").length > 0 && $("input[name='modoEditar']").val() === "1"));

    if (!($("input[name='idInteresado']").length > 0 && $("input[name='idInteresado']").val() !== "")) {
      var fechaNacimiento = $("#fecha-nacimiento").val();
      utilFechasHorarios.establecerCalendario("fecha-nacimiento", false, true, false);
      if (fechaNacimiento !== "") {
        if (Date.parse(fechaNacimiento)) {
          var datFechaNacimiento = fechaNacimiento.split("/");
          $("#fecha-nacimiento").datepicker("setDate", (new Date(datFechaNacimiento[1] + "/" + datFechaNacimiento[0] + "/" + datFechaNacimiento[2])));
        } else {
          $("#fecha-nacimiento").datepicker("setDate", (new Date()));
        }
      }
    }

    var fechaInicioClase = $("#fecha-inicio-clase").val();
    utilFechasHorarios.establecerCalendario("fecha-inicio-clase", false, false, false);
    if (fechaInicioClase !== "") {
      if (Date.parse(fechaInicioClase)) {
        var datFechaInicioClase = fechaInicioClase.split("/");
        $("#fecha-inicio-clase").datepicker("setDate", (new Date(datFechaInicioClase[1] + "/" + datFechaInicioClase[0] + "/" + datFechaInicioClase[2])));
      } else {
        $("#fecha-inicio-clase").datepicker("setDate", (new Date()));
      }
    }

    var numeroHorasClase = $("input[name='auxNumeroHorasClase']").val();
    utilFechasHorarios.establecerCampoDuracion("numero-horas-clase", (numeroHorasClase !== "" ? numeroHorasClase : 7200));

    $("#direccion").focusout(verificarDatosBusquedaMapa);
    $("input[name='codigoUbigeo']").change(verificarDatosBusquedaMapa);
  } else {
    $("input[name='horario']").change(function () {
      if (urlActualizarHorario !== "" && $(this).val() !== "") {
        $.blockUI({message: "<h4>Actualizando horario...</h4>"});
        util.llamadaAjax(urlActualizarHorario, "POST", {"horario": $(this).val()}, true,
                function (d) {
                  $("body").unblock({
                    onUnblock: function () {
                      agregarMensaje("exitosos", "Actualización de horario exitosa.", true);
                    }
                  });
                },
                function (d) {
                },
                function (de) {
                  $("body").unblock({
                    onUnblock: function () {
                      agregarMensaje("errores", "Ocurrió un problema durante la actualización del horario del alumno. Por favor inténtelo nuevamente.", true);
                    }
                  });
                }
        );
      }
    });
  }
}


