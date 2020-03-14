var listaAlumnos = {};
listaAlumnos = (function ()/* - */ {
  $(document).ready(function ()/* - */ {
    cargarLista();
  });

  function cargarLista()/* - */ {
    urlListar = (typeof (urlListar) === "undefined" ? "" : urlListar);
    urlPerfilAlumno = (typeof (urlPerfilAlumno) === "undefined" ? "" : urlPerfilAlumno);
    urlEditar = (typeof (urlEditar) === "undefined" ? "" : urlEditar);
    urlEliminar = (typeof (urlEliminar) === "undefined" ? "" : urlEliminar);
    urlPerfilProfesor = (typeof (urlPerfilProfesor) === "undefined" ? "" : urlPerfilProfesor);
    urlListarClases = (typeof (urlListarClases) === "undefined" ? "" : urlListarClases);

    estados = (typeof (estados) === "undefined" ? "" : estados);
    estadosDisponibleCambio = (typeof (estadosDisponibleCambio) === "undefined" ? "" : estadosDisponibleCambio);

    if (urlListar !== "" && urlPerfilAlumno !== "" && urlEditar !== "" && urlEliminar !== "" && urlPerfilProfesor !== "" && urlListarClases !== "" && estados !== "" && estadosDisponibleCambio !== "") {
      $("#tab-lista-alumnos").DataTable({
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
        dom: "<'row'<'col-sm-6'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'i>>" + "<'row'<'col-sm-12'tr>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        autoWidth: false,
        responsive: true,
        orderCellsTop: true,
        fixedHeader: true,
        order: [[6, "desc"]],
        rowId: 'idEntidad',
        columns: [
          {data: "", name: "", orderable: false, "searchable": false, render: function (e, t, d, m) {
              return m.row + m.settings._iDisplayStart + 1;
            }, "className": "text-center", responsivePriority: 0},
          {data: "nombre", name: "nombre", render: function (e, t, d, m) {
              return '<a href="' + (urlPerfilAlumno.replace("/0", "/" + d.id)) + '">' + (d.nombre !== null ? d.nombre : "") + " " + (d.apellido !== null ? d.apellido : "") + '</a>' +
                      (d.telefono ? '<br/><span class="text-info"><i class="fa  fa-mobile"></i> ' + util.incluirEnlaceWhatsApp(d.telefono) + '</span>' : '') +
                      (d.distritoAlumno ? '<br/><span class="text-info"><small><i class="fa fa-street-view"></i> ' + util.letraCapital(d.distritoAlumno) + '</small></span>' : '') +
                      (d.nombreProfesor ?
                              '<small><br/><br/>Profesor(a): <a href="' + (urlPerfilProfesor.replace("/0", "/" + d.idProfesor)) + '">' + (d.nombreProfesor !== null ? d.nombreProfesor : "") + " " + (d.apellidoProfesor !== null ? d.apellidoProfesor : "") + '</a></small>' +
                              (d.telefonoProfesor ? '<br/><span class="text-info"><small><i class="fa  fa-mobile"></i> ' + util.incluirEnlaceWhatsApp(d.telefonoProfesor) + '</small></span>' : '') +
                              (d.distritoProfesor ? '<br/><span class="text-info"><small><i class="fa fa-street-view"></i> ' + util.letraCapital(d.distritoProfesor) + '</small></span>' : '')
                              : '');
            }, responsivePriority: 0},
          {data: "porcentajeAvanceXClases", name: "porcentajeAvanceXClases", width: "30%", render: function (e, t, d, m) {
              var ultimaClaseFecha = utilFechasHorarios.formatoFecha(d.ultimaClaseFecha);

              var htmlAvanceXBolsaHoras = '<div class="clearfix"><span>Sin bolsa de horas</span></div><br/>';
              if (d.duracionTotalXClases && parseInt(d.numeroPagosXBolsaHoras)) {
                htmlAvanceXBolsaHoras = '<div class="clearfix">' +
                        '<span class="pull-left">Bolsa de ' + utilFechasHorarios.formatoHora(d.duracionTotalXClases) + ' hora(s) </span>' +
                        '<a href="javascript:void(0);" onclick="listaAlumnos.abrirModalListaClases(' + d.idEntidad + ');" title="Ver lista de clases" class="btn-ver-lista-clases">' +
                        '<i class="fa fa-eye"></i>' +
                        '</a>' +
                        '<small class="pull-right">' + util.redondear(d.porcentajeAvanceXClases, 2) + ' %</small>' +
                        '</div>' +
                        '<div class="progress xs">' +
                        '<div class="progress-bar progress-bar-green" style="width: ' + d.porcentajeAvanceXClases + '%;"></div>' +
                        '</div>' +
                        '<div class="clearfix">' +
                        '<span class="pull-left">' +
                        '<span class="text-green" data-toggle="tooltip" title="" data-original-title="Horas realizadas">' +
                        '<i class="fa fa-clock-o"></i> ' + utilFechasHorarios.formatoHora(d.duracionTotalXClasesRealizadas) +
                        '</span>' +
                        ((d.duracionTotalXClases - d.duracionTotalXClasesRealizadas) > 0
                                ? '  -  <span class="text-yellow" data-toggle="tooltip" title="" data-original-title="Horas pendientes">' +
                                '<i class="fa fa-clock-o"></i> ' + utilFechasHorarios.formatoHora(d.duracionTotalXClases - d.duracionTotalXClasesRealizadas) +
                                '</span>' : '') +
                        '</span>' +
                        '</div><br/>';
              }
              var htmlAvanceGlobal = '';
              if (d.duracionTotalXClasesGlobal && parseInt(d.numeroPagosXBolsaHorasGlobal) && d.duracionTotalXClasesGlobal !== d.duracionTotalXClases) {
                htmlAvanceGlobal = '<div class="clearfix">' +
                        '<span class="pull-left">Histórico de ' + utilFechasHorarios.formatoHora(d.duracionTotalXClasesGlobal) + ' hora(s) </span>' +
                        (!(d.duracionTotalXClases && parseInt(d.numeroPagosXBolsaHoras))
                                ? '<a href="javascript:void(0);" onclick="listaAlumnos.abrirModalListaClases(' + d.idEntidad + ');" title="Ver lista de clases" class="btn-ver-lista-clases">' +
                                '<i class="fa fa-eye"></i>' +
                                '</a>' : '') +
                        '<small class="pull-right">' + util.redondear(d.porcentajeAvanceXClasesGlobal, 2) + ' %</small>' +
                        '</div>' +
                        '<div class="progress xs">' +
                        '<div class="progress-bar progress-bar-green" style="width: ' + d.porcentajeAvanceXClasesGlobal + '%;"></div>' +
                        '</div>' +
                        ((d.duracionTotalXClasesGlobal - d.duracionTotalXClasesRealizadasGlobal) > 0
                                ? '<div class="clearfix">' +
                                '<span class="pull-left">' +
                                '<span class="text-green" data-toggle="tooltip" title="" data-original-title="Horas realizadas">' +
                                '<i class="fa fa-clock-o"></i> ' + utilFechasHorarios.formatoHora(d.duracionTotalXClasesRealizadasGlobal) +
                                '</span>' + '  -  <span class="text-yellow" data-toggle="tooltip" title="" data-original-title="Horas pendientes">' +
                                '<i class="fa fa-clock-o"></i> ' + utilFechasHorarios.formatoHora(d.duracionTotalXClasesGlobal - d.duracionTotalXClasesRealizadasGlobal) +
                                '</span>' +
                                '</span>' : '') +
                        '</div><br/>';
              }

              return htmlAvanceXBolsaHoras +
                      htmlAvanceGlobal +
                      (ultimaClaseFecha !== "" ? '<div class="clearfix"><small class="pull-left">Última clase: ' + ultimaClaseFecha + '</small></div>' : '');

            }, "className": "min-tablet-p"},
          {data: "curso", name: "curso", render: function (e, t, d, m) {
              return d.curso + '<div id="sec-info-horario-' + d.id + '"></div>';
            }, "className": "min-tablet-l"},
          {data: "estado", name: "estado", render: function (e, t, d, m) {
              var estado = '';
              if (estados[d.estado] !== undefined) {
                if (estadosDisponibleCambio[d.estado] !== undefined) {
                  estado = '<div class="sec-btn-editar-estado" data-idtabla="tab-lista-alumnos" data-idselestados="sel-estados" data-tipocambio="1">' +
                          '<a href="javascript:void(0);" class="btn-editar-estado" data-id="' + d.id + '" data-estado="' + d.estado + '">' +
                          '<span class="label ' + estados[d.estado][1] + ' btn-estado">' + estados[d.estado][0] + '</span>' +
                          '</a>' +
                          '</div>';
                } else {
                  estado = '<span class="label ' + estados[d.estado][1] + ' btn-estado">' + estados[d.estado][0] + '</span><br/>';
                }
              }
              return estado + (d.nivelIngles !== null && d.nivelIngles !== "" ? '<span class="text-info"><small>(Nivel ' + d.nivelIngles + ')</small></span>' : '');
            }, "className": "text-center min-tablet-l"},
          {data: "montoTotalPagosXBolsaHoras", name: "montoTotalPagosXBolsaHoras", width: "10%", render: function (e, t, d, m) {
              var htmlPagosXBolsaHoras = '';
              if (d.montoTotalPagosXBolsaHoras !== null && d.montoTotalPagosXBolsaHoras !== "" && d.montoTotalPagosXBolsaHoras > 0) {
                htmlPagosXBolsaHoras = 'S/. ' + util.redondear(d.montoTotalPagosXBolsaHoras, 2) + '<br/><span class="text-info"><small>(' + d.numeroPagosXBolsaHoras + ' pago' + (parseInt(d.numeroPagosXBolsaHoras) === 1 ? '' : 's') + ' por bolsa de horas)</small></span><br/><br/>';
              }
              var htmlPagosGlobal = '';
              if (d.montoTotalPagosXBolsaHorasGlobal !== null && d.montoTotalPagosXBolsaHorasGlobal !== "" && d.montoTotalPagosXBolsaHorasGlobal > 0 && d.montoTotalPagosXBolsaHorasGlobal !== d.montoTotalPagosXBolsaHoras) {
                htmlPagosGlobal = 'S/. ' + util.redondear(d.montoTotalPagosXBolsaHorasGlobal, 2) + '<br/><span class="text-info"><small>(' + d.numeroPagosXBolsaHorasGlobal + ' pago' + (parseInt(d.numeroPagosXBolsaHorasGlobal) === 1 ? '' : 's') + ')</small></span>';
              }
              return htmlPagosXBolsaHoras + htmlPagosGlobal;
            }, "className": "text-center min-tablet-l"},
          {data: "fechaRegistro", name: "fechaRegistro", width: "12%", render: function (e, t, d, m) {
              var fechaInicioClases = utilFechasHorarios.formatoFecha(d.fechaInicioClase);
              return utilFechasHorarios.formatoFecha(d.fechaRegistro, true) + '<br/>' +
                      (fechaInicioClases !== "" ? '<span class="text-info"><small>(Inicio de clases: ' + fechaInicioClases + ')</small></span>' : '');
            }, "className": "text-center desktop"},
          {data: "id", name: "id", orderable: false, "searchable": false, width: "5%", render: function (e, t, d, m) {
              return '<ul class="buttons">' +
                      '<li>' +
                      '<a href="' + (urlPerfilAlumno.replace("/0", "/" + d.id)) + '" title="Ver perfil"><i class="fa fa-eye"></i></a>' +
                      '</li>' +
                      '<li>' +
                      '<a href="' + (urlEditar.replace("/0", "/" + d.id)) + '" title="Editar datos"><i class="fa fa-pencil"></i></a>' +
                      '</li>' +
                      '<li>' +
                      '<a href="javascript:void(0);" title="Eliminar alumno" onclick="utilTablas.eliminarElemento(this, \'¿Está seguro que desea eliminar los datos de este alumno?\', \'tab-lista-alumnos\')" data-id="' + d.id + '" data-urleliminar="' + ((urlEliminar.replace("/0", "/" + d.id))) + '">' +
                      '<i class="fa fa-trash"></i>' +
                      '</a>' +
                      '</li>' +
                      '</ul>';
            }, className: "text-center min-mobile-l"}
        ],
        initComplete: function (s, j) {
          utilTablas.establecerBotonRecargaTabla($("#tab-lista-alumnos"));
          utilTablas.establecerCabecerasBusquedaTabla($("#tab-lista-alumnos"));
        },
        drawCallback: function (s) {
          CargarHorarios();
        }
      });
      listaClases.actualizar(urlListarClases, true, false);
    }
  }

  var datosHorariosCargados = [];
  function CargarHorarios()/* - */ {
    urlHorarioMultiple = (typeof (urlHorarioMultiple) === "undefined" ? "" : urlHorarioMultiple);
    if (urlHorarioMultiple !== "") {
      //Horarios
      var idsEntidades = jQuery.map($("#tab-lista-alumnos").DataTable().rows().ids(), function (ele) {
        if (!datosHorariosCargados.some(function (dhc) {
          return dhc.idEntidad === ele;
        })) {
          return ele;
        }
      });
      if (idsEntidades.length > 0) {
        util.llamadaAjax(urlHorarioMultiple, "POST", {"idsEntidades": idsEntidades}, true, function (datos) {
          datosHorariosCargados = datosHorariosCargados.concat(datos);
          MostrarHorarios();
        });
      } else {
        MostrarHorarios();
      }
    }
  }
  function MostrarHorarios()/* - */ {
    datosHorariosCargados.forEach(function (d) {
      $("#sec-info-horario-" + d.idEntidad).html(horario.obtenerTexto($.parseJSON(d.datosHorario)));
    });
  }

  function abrirModalListaClases(id)/* - */ {
    var tr = $("#tab-lista-alumnos").find("#" + id)[0];
    var fila = $("#tab-lista-alumnos").DataTable().row(tr);
    var datosAlumno = fila.data();

    $.blockUI({message: "<h4>Cargando...</h4>"});
    listaClases.actualizar(urlListarClases.replace("/0", "/" + datosAlumno.id), true, false, function () {
      $("#mod-lista-clases").modal("show");
      $("#mod-lista-clases").find(".modal-title").html("Lista de clases " + (datosAlumno.sexo === "F" ? "de la alumna " : "del alumno ") + datosAlumno.nombre + " " + datosAlumno.apellido);
      $("body").unblock();
    }, function () {
      $("body").unblock();
    });
  }

  return {
    abrirModalListaClases: abrirModalListaClases
  };
}());

