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

    if (urlListar !== "" && urlPerfilAlumno !== "" && urlEditar !== "" && urlEliminar !== "" && urlPerfilProfesor !== "" && urlListarClases !== "" && estados !== "" && estadosDisponibleCambio !== "" ) {
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
            }, "className": "text-center not-mobile"},
          {data: "nombre", name: "nombre", render: function (e, t, d, m) {
              return '<a href="' + (urlPerfilAlumno.replace("/0", "/" + d.id)) + '">' + (d.nombre !== null ? d.nombre : "") + " " + (d.apellido !== null ? d.apellido : "") + '</a>' + (d.telefono ? '<br/><span class="text-info"><i class="fa  fa-mobile"></i> ' + incluirEnlaceWhatsApp(d.telefono) + '</span>' : '') +
                      (d.distritoAlumno ? '<br/><span class="text-info"><i class="fa fa-street-view"></i> ' + util.letraCapital(d.distritoAlumno) + '</span>' : '') +
                      (d.nombreProfesor ?
                              '<br/><br/>Profesor(a): <a href="' + (urlPerfilProfesor.replace("/0", "/" + d.idProfesor)) + '">' + (d.nombreProfesor !== null ? d.nombreProfesor : "") + " " + (d.apellidoProfesor !== null ? d.apellidoProfesor : "") + '</a>' +
                              (d.distritoProfesor ? '<br/><span class="text-info"><i class="fa fa-street-view"></i> ' + util.letraCapital(d.distritoProfesor) + '</span>' : '')
                              : '');
            }},
          {data: "ultimoPagoPorcentajeAvanceXClases", name: "ultimoPagoPorcentajeAvanceXClases", width: "20%", render: function (e, t, d, m) {
              if (d.ultimoPagoDuracionTotalXClases) {
                var ultimaClaseFecha = utilFechasHorarios.formatoFecha(d.ultimaClaseFecha);

                return '<div class="clearfix">' +
                        '<span class="pull-left">Bolsa de ' + utilFechasHorarios.formatoHora(d.ultimoPagoDuracionTotalXClases) + ' hora(s) </span>' +
                        '<a href="javascript:void(0);" onclick="listaAlumnos.abrirModalListaClases(' + d.idEntidad + ');" title="Ver lista de clases" class="btn-ver-lista-clases">' +
                        '<i class="fa fa-eye"></i>' +
                        '</a>' +
                        '<small class="pull-right">' + util.redondear(d.ultimoPagoPorcentajeAvanceXClases, 2) + ' %</small>' +
                        '</div>' +
                        '<div class="progress xs">' +
                        '<div class="progress-bar progress-bar-green" style="width: ' + d.ultimoPagoPorcentajeAvanceXClases + '%;"></div>' +
                        '</div>' +
                        '<div class="clearfix">' +
                        '<span class="pull-left">' +
                        '<span class="text-green" data-toggle="tooltip" title="" data-original-title="Horas realizadas">' +
                        '<i class="fa fa-clock-o"></i> ' + utilFechasHorarios.formatoHora(d.ultimoPagoDuracionTotalXClasesRealizadas) +
                        '</span>  de  <span class="text-info" data-toggle="tooltip" title="" data-original-title="Horas programadas"><i class="fa fa-clock-o"></i> ' + utilFechasHorarios.formatoHora(d.ultimoPagoDuracionTotalXClases) + '</span>' +
                        '</span>' +
                        '</div>' +
                        (ultimaClaseFecha !== "" ? '<div class="clearfix"><br/><small class="pull-left">Última clase: ' + ultimaClaseFecha + '</small></div>' : '');
              } else {
                return 'Sin clases registradas';
              }
            }, "className": "not-mobile"},
          {data: "curso", name: "curso", render: function (e, t, d, m) {
              return d.curso + '<div id="sec-info-horario-' + d.id + '"></div>';
            }, "className": "not-mobile"},
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
              return estado + (d.nivelIngles !== null && d.nivelIngles !== "" ? '<span class="text-info">(Nivel ' + d.nivelIngles + ')</span>' : '');
            }, "className": "text-center not-mobile"},
          {data: "ultimoPagoMonto", name: "ultimoPagoMonto", width: "10%", render: function (e, t, d, m) {
              return (d.ultimoPagoMonto !== null && d.ultimoPagoMonto !== "" && d.ultimoPagoMonto > 0 ? 'S/. ' + util.redondear(d.ultimoPagoMonto, 2) : '');
            }, "className": "text-center not-mobile"},
          {data: "fechaRegistro", name: "fechaRegistro", width: "12%", render: function (e, t, d, m) {
              var fechaInicioClases = utilFechasHorarios.formatoFecha(d.fechaInicioClase);
              return utilFechasHorarios.formatoFecha(d.fechaRegistro, true) + '<br/>' +
                      (fechaInicioClases !== "" ? '<span class="text-info">(Inicio de clases:<br/>' + fechaInicioClases + ')</span>' : '');
            }, "className": "text-center not-mobile"},
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
            }, className: "text-center"}
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

