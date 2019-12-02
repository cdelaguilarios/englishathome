var listaPagosDocente = {};
listaPagosDocente = (function ()/* - */ {
  window.addEventListener("load", esperarCargaJquery, false);
  function esperarCargaJquery()/* - */ {
    ((window.jQuery && jQuery.ui) ? cargarLista() : window.setTimeout(esperarCargaJquery, 100));
  }

  //Privado  
  var tablaPagos = null;
  function cargarLista()/* - */ {
    urlListarPagosXClases = (typeof (urlListarPagosXClases) === "undefined" ? "" : urlListarPagosXClases);
    urlPerfilProfesor = (typeof (urlPerfilProfesor) === "undefined" ? "" : urlPerfilProfesor);
    urlPerfilAlumno = (typeof (urlPerfilAlumno) === "undefined" ? "" : urlPerfilAlumno);

    estadoPagoRealizado = (typeof (estadoPagoRealizado) === "undefined" ? "" : estadoPagoRealizado);

    if (urlListarPagosXClases !== "" && urlPerfilProfesor !== "" && urlPerfilAlumno !== "" && estadoPagoRealizado !== "") {
      tablaPagos = $("#tab-lista-pagos").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: urlListarPagosXClases,
          type: "POST",
          data: function (d) {
            d._token = $("meta[name=_token]").attr("content");
            d.estadoPago = $("#bus-estado-pago").val();
            $.extend(d, filtrosBusquedaFechas.obtenerDatos());
          }
        },
        autoWidth: false,
        responsive: true,
        order: [[5, "desc"]],
        columns: [
          {data: "", name: "", orderable: false, "searchable": false, render: function (e, t, d, m) {
              return m.row + m.settings._iDisplayStart + 1;
            }, "className": "text-center not-mobile"},
          {data: "profesor", name: "profesor", render: function (e, t, d, m) {
              return '<a href="' + (urlPerfilProfesor.replace("/0", "/" + d.idProfesor)) + '" target="_blank">' + d.profesor + '</a>';
            }},
          {data: "numeroTotalClases", name: "numeroTotalClases", "searchable": false, className: "text-center", render: function (e, t, d, m) {
              return '<div class="clearfix">' +
                      '<span>' + d.numeroTotalClases + '</span>' +
                      '<a href="javascript:void(0);" onclick="listaPagosDocente.cargarListaDetalle(this);" title="Ver lista de clases" class="btn-ver-lista-clases">' +
                      '<i class="fa fa-eye"></i>' +
                      '</a>' +
                      '</div>';
            }},
          {data: "duracionTotalClases", name: "duracionTotalClases", "searchable": false, render: function (e, t, d, m) {
              return utilFechasHorarios.formatoHora(d.duracionTotalClases);
            }, className: "text-center"},
          {data: "costoHoraPromedioProfesor", name: "costoHoraPromedioProfesor", "searchable": false, render: function (e, t, d, m) {
              return 'S/. ' + util.redondear(d.costoHoraPromedioProfesor, 2);
            }, className: "text-center", type: "monto"},
          {data: "montoTotalXClases", name: "montoTotalXClases", "searchable": false, render: function (e, t, d, m) {
              return 'S/. ' + util.redondear(d.montoTotalXClases, 2);
            }, className: "text-center", type: "monto"},
          {data: "idProfesor", name: "clase.idProfesor", orderable: false, "searchable": false, width: "5%", render: function (e, t, d, m) {
              return ($("#bus-estado-pago").val() === estadoPagoRealizado ?
                      '<span class="label label-success btn-estado">Pagado</span>' :
                      '<a href="javascript:void(0);" onclick="listaPagosDocente.cargarFormulario(this);" type="button" class="btn btn-success btn-sm">Pagar</a>');
            }, className: "text-center"}
        ],
        initComplete: function (s, j) {
          utilTablas.establecerBotonRecargaTabla($("#tab-lista-pagos"));
          utilTablas.establecerCabecerasBusquedaTabla($("#tab-lista-pagos"));
        },
        footerCallback: function (r, d, s, e, di) {
          var api = this.api();

          var totalClases = 0, totalClasesPagina = 0;
          var duracionTotal = 0, duracionTotalPagina = 0;
          var montoTotal = 0, montoTotalPagina = 0;
          $('#tab-lista-pagos').DataTable().rows({filter: 'applied'}).data().each(function (datosPago) {
            totalClases += parseFloat(datosPago.numeroTotalClases);
            duracionTotal += parseFloat(datosPago.duracionTotalClases);
            montoTotal += parseFloat(datosPago.montoTotalXClases);
          });
          $('#tab-lista-pagos').DataTable().rows({page: 'current'}).data().each(function (datosPago) {
            totalClasesPagina += parseFloat(datosPago.numeroTotalClases);
            duracionTotalPagina += parseFloat(datosPago.duracionTotalClases);
            montoTotalPagina += parseFloat(datosPago.montoTotalXClases);
          });

          $(api.column(2).footer()).html("Total de la página " + totalClasesPagina +
                  (montoTotal !== montoTotalPagina ? "<br/>Total " + totalClases : ""));
          $(api.column(3).footer()).html("Total de la página " + utilFechasHorarios.formatoHora(duracionTotalPagina) +
                  (montoTotal !== montoTotalPagina ? "<br/>Total " + utilFechasHorarios.formatoHora(duracionTotal) : ""));
          $(api.column(4).footer()).html("Total de la página S/. " + util.redondear((montoTotalPagina / (duracionTotalPagina / 3600)), 2) +
                  (montoTotal !== montoTotalPagina ? "<br/>Total S/." + util.redondear((montoTotal / (duracionTotal / 3600)), 2) : ""));
          $(api.column(5).footer()).html("Total de la página S/. " + util.redondear(montoTotalPagina, 2) +
                  (montoTotal !== montoTotalPagina ? "<br/>Total S/." + util.redondear(montoTotal, 2) : ""));
        }
      });
      tablaPagos.on('draw.dtr', function (event) {
        event.stopImmediatePropagation();
        return false;
      });

      var funcionCambio = function () {
        reCargar();
      };
      $("#bus-estado-pago").change(funcionCambio);
      filtrosBusquedaFechas.cargar(funcionCambio);

    }
  }

  //Público
  function mostrar()/* - */ {
    $("div[id^=sec-pago-]").hide();
    utilTablas.recargarDatosTabla($("#tab-lista-pagos"));
    $("#sec-pago-lista").show();
  }
  function reCargar()/* - */ {
    $("#tab-lista-pagos").DataTable().ajax.reload();
  }

  var tablasDetalles = [];
  function cargarListaDetalle(elemento) {
    var tr = $(elemento).closest("tr");
    var fila = tablaPagos.row(tr);

    if (fila.child.isShown()) {
      $(elemento).html('<i class="fa fa-eye"></i>');
      var tablaClasesEli = $("table", fila.child());
      tablaClasesEli.detach();
      tablaClasesEli.DataTable().destroy();
      fila.child.hide();

      tr.removeClass('shown');
    } else {
      var datosPago = fila.data();
      $(elemento).html('<i class="fa fa-eye-slash"></i>');

      var idTabla = "tab-lista-clases-profesor-" + datosPago.idProfesor;
      fila.child('<table id="' + idTabla + '" class="table table-bordered table-hover">' +
              '<thead>' +
              '<tr>' +
              '<th>N°</th>' +
              '<th>Alumno(a)</th>' +
              '<th>Fecha</th>' +
              '<th>Duración (horas)</th>' +
              '<th>Pago por hora al profesor</th>' +
              '<th>Pago total al profesor</th>' +
              '</tr>' +
              '</thead>' +
              '<tfoot>' +
              '<tr>' +
              '<th colspan="3"></th>' +
              '<th></th>' +
              '<th></th>' +
              '<th></th>' +
              '</tr>' +
              '</tfoot>' +
              '</table>').show();

      $(tr).addClass('shown');
      var procesarTodoEnServidor = (datosPago.numeroTotalClases > 200);
      tablasDetalles[idTabla] = $("#" + idTabla).DataTable({
        processing: true,
        serverSide: procesarTodoEnServidor,
        ajax: {
          url: urlListarPagosXClasesDetalle.replace("/0", "/" + datosPago.idProfesor),
          type: "POST",
          data: function (d) {
            d._token = $("meta[name=_token]").attr("content");
            d.estadoPago = $("#bus-estado-pago").val();
            $.extend(d, filtrosBusquedaFechas.obtenerDatos());
          }
        },
        pageLength: 10,
        autoWidth: false,
        responsive: true,
        orderCellsTop: true,
        fixedHeader: true,
        order: [[2, "desc"]],
        rowId: 'id',
        columns: [
          {data: "", name: "", orderable: false, "searchable": false, "className": "text-center not-mobile"},
          {data: "alumno", name: "alumno", render: function (e, t, d, m) {
              return '<a href="' + (urlPerfilAlumno.replace("/0", "/" + d.idAlumno)) + '" target="_blank">' + d.alumno + '</a>';
            }},
          {data: "fechaConfirmacion", name: "fechaConfirmacion", render: function (e, t, d, m) {
              return utilFechasHorarios.formatoFecha(d.fechaConfirmacion);
            }, className: "text-center", type: (procesarTodoEnServidor ? "" : "fecha")},
          {data: "duracion", name: "duracion", render: function (e, t, d, m) {
              return utilFechasHorarios.formatoHora(d.duracion);
            }, className: "text-center"},
          {data: "costoHoraProfesor", name: "costoHoraProfesor", render: function (e, t, d, m) {
              return 'S/. ' + util.redondear(d.costoHoraProfesor, 2);
            }, className: "text-center", type: (procesarTodoEnServidor ? "" : "monto")},
          {data: "pagoTotalFinalProfesor", name: "pagoTotalFinalProfesor", render: function (e, t, d, m) {
              return 'S/. ' + util.redondear(d.pagoTotalFinalProfesor, 2);
            }, className: "text-center", type: (procesarTodoEnServidor ? "" : "monto")}
        ],
        initComplete: function (s, j) {
          utilTablas.establecerBotonRecargaTabla($("#" + idTabla));
          utilTablas.establecerCabecerasBusquedaTabla($("#" + idTabla));
          $("#" + idTabla).closest("td").css("border", "1px solid #3c8dbc");
        },
        footerCallback: function (r, d, s, e, di) {
          var api = this.api();

          var duracionTotal = 0, duracionTotalPagina = 0;
          var montoTotal = 0, montoTotalPagina = 0;
          $("#" + idTabla).DataTable().rows({filter: 'applied'}).data().each(function (datosPago) {
            duracionTotal += parseFloat(datosPago.duracion);
            montoTotal += parseFloat(datosPago.pagoTotalFinalProfesor);
          });
          $("#" + idTabla).DataTable().rows({page: 'current'}).data().each(function (datosPago) {
            duracionTotalPagina += parseFloat(datosPago.duracion);
            montoTotalPagina += parseFloat(datosPago.pagoTotalFinalProfesor);
          });

          $(api.column(3).footer()).html("Total de la página " + utilFechasHorarios.formatoHora(duracionTotalPagina) +
                  (montoTotal !== montoTotalPagina ? "<br/>Total " + utilFechasHorarios.formatoHora(duracionTotal) : ""));
          $(api.column(4).footer()).html("Promedio de la página S/. " + util.redondear((montoTotalPagina / (duracionTotalPagina / 3600)), 2) +
                  (montoTotal !== montoTotalPagina ? "<br/>Promedio S/." + util.redondear((montoTotal / (duracionTotal / 3600)), 2) : ""));
          $(api.column(5).footer()).html("Total de la página S/. " + util.redondear(montoTotalPagina, 2) +
                  (montoTotal !== montoTotalPagina ? "<br/>Total S/." + util.redondear(montoTotal, 2) : ""));
        }
      });

      tablasDetalles[idTabla].on('order.dt search.dt', function () {
        tablasDetalles[idTabla].column(0, {search: 'applied', order: 'applied'}).nodes().each(function (c, i) {
          c.innerHTML = i + 1;
        });
      }).draw();
    }
  }
  function cargarFormulario(elemento) {
    var tr = $(elemento).closest("tr");
    var fila = tablaPagos.row(tr);
    var datosPago = fila.data();
    formularioPagoDocente.mostrar(datosPago);
  }
  function obtenerDatosFiltrosBusqueda() {
    var d = {};
    d.estadoPago = $("#bus-estado-pago").val();
    $.extend(d, filtrosBusquedaFechas.obtenerDatos());
    return d;
  }

  return {
    mostrar: mostrar,
    reCargar: reCargar,
    cargarListaDetalle: cargarListaDetalle,
    cargarFormulario: cargarFormulario,
    obtenerDatosFiltrosBusqueda: obtenerDatosFiltrosBusqueda
  };
}());