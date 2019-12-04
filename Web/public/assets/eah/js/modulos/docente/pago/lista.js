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
    urlEliminarPagoXClases = (typeof (urlEliminarPagoXClases) === "undefined" ? "" : urlEliminarPagoXClases);

    estados = (typeof (estados) === "undefined" ? "" : estados);
    estadoPagoRealizado = (typeof (estadoPagoRealizado) === "undefined" ? "" : estadoPagoRealizado);

    if (urlListarPagosXClases !== "" && urlPerfilProfesor !== "" && urlPerfilAlumno !== "" && urlEliminarPagoXClases !== "" && estados !== "" && estadoPagoRealizado !== "") {
      tablaPagos = $("#tab-lista-pagos").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: urlListarPagosXClases,
          type: "POST",
          data: function (d) {
            d._token = $("meta[name=_token]").attr("content");
            d.estadoPago = $("#bus-estado").val();
            $.extend(d, filtrosBusquedaFechas.obtenerDatos());
          }
        },
        dom: "<'row'<'col-sm-6'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'i>>" + "<'row'<'col-sm-12'tr>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        autoWidth: false,
        responsive: true,
        order: [[5, "desc"]],
        rowId: 'idPago',
        columns: [
          {data: "", name: "", orderable: false, "searchable": false, render: function (e, t, d, m) {
              return m.row + m.settings._iDisplayStart + 1;
            }, "className": "text-center not-mobile"},
          {data: "profesor", name: "profesor", render: function (e, t, d, m) {
              var cuentasBancariasProfesor = '';
              if (d.cuentasBancariasProfesor !== null && d.cuentasBancariasProfesor !== "") {
                var cuentasBancarias = d.cuentasBancariasProfesor.split(";");
                for (var i = 0; i < cuentasBancarias.length; i++) {
                  var datosCuentaBancaria = cuentasBancarias[i].split("|");
                  if (datosCuentaBancaria.length === 2) {
                    cuentasBancariasProfesor += '</br><span class="text-info"><i class="fa fa-money"></i> ' + datosCuentaBancaria[0] + ' ' + datosCuentaBancaria[1] + '</span>';
                  }
                }
              }

              return '<a href="' + (urlPerfilProfesor.replace("/0", "/" + d.idProfesor)) + '" target="_blank">' + d.profesor + '</a>'
                      + (cuentasBancariasProfesor !== "" ? cuentasBancariasProfesor : '');
            }},
          {data: "numeroTotalClases", name: "numeroTotalClases", "searchable": false, className: "text-center", render: function (e, t, d, m) {
              return '<div class="clearfix">' +
                      '<span>' + d.numeroTotalClases + '</span>' +
                      '<a href="javascript:void(0);" onclick="listaPagosDocente.cargarListaClases(this);" title="Ver lista de clases" class="btn-ver-lista-clases">' +
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
          {data: "estadoPago", name: "estadoPago", render: function (e, t, d, m) {
              return '<span class="label ' + estados[d.estadoPago][1] + ' btn-estado">' + estados[d.estadoPago][0] + '</span>'
                      + (d.fechaPago !== null && d.fechaPago !== "" ? '<div class="clearfix"><span class="text-info">(Fecha pago: ' + utilFechasHorarios.formatoFecha(d.fechaPago) + ')</span></div>' : '');
            }, "className": "text-center not-mobile"},
          {data: "idProfesor", name: "clase.idProfesor", orderable: false, "searchable": false, width: "10%", render: function (e, t, d, m) {
              return (d.estadoPago === estadoPagoRealizado ?
                      '<ul class="buttons">' +
                      '<li>' +
                      '<a href="javascript:void(0);" onclick="listaPagosDocente.editarPagoXClases(this);" title="Editar datos del pago"><i class="fa fa-pencil"></i></a>' +
                      '</li>' +
                      '<li>' +
                      '<a href="javascript:void(0);" title="Eliminar pago" onclick="utilTablas.eliminarElemento(this, \'¿Está seguro que desea eliminar los datos de este pago?\', \'tab-lista-pagos\', false, null, true)" data-id="' + d.id + '" data-urleliminar="' + ((urlEliminarPagoXClases.replace("/0", "/" + d.idProfesor).replace("/-1", "/" + d.idPago))) + '">' +
                      '<i class="fa fa-trash"></i>' +
                      '</a>' +
                      '</li>' +
                      '</ul>' :
                      '<a href="javascript:void(0);" onclick="listaPagosDocente.registrarPagoXClases(this);" type="button" class="btn btn-success btn-sm">Pagar</a>');
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
      filtrosBusquedaFechas.cargar(funcionCambio);
      filtrosBusquedaFechas.actualizarTitulo("Fecha de clases (*): ");
    }
  }
  function obtenerDatos(elemento) {
    var tr = $(elemento).closest("tr");
    var fila = tablaPagos.row(tr);
    return fila.data();
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

  var tablasClases = [];
  function cargarListaClases(elemento) {
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

      var idTabla = "tab-lista-clases-profesor-" + datosPago.idProfesor + (datosPago.idPago !== null ? "-" + datosPago.idPago : "");
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
      tablasClases[idTabla] = $("#" + idTabla).DataTable({
        processing: true,
        serverSide: procesarTodoEnServidor,
        ajax: {
          url: urlListarPagosXClasesDetalle.replace("/0", "/" + datosPago.idProfesor),
          type: "POST",
          data: function (d) {
            d._token = $("meta[name=_token]").attr("content");
            d.estadoPago = $("#bus-estado").val();
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

      tablasClases[idTabla].on('order.dt search.dt', function () {
        tablasClases[idTabla].column(0, {search: 'applied', order: 'applied'}).nodes().each(function (c, i) {
          c.innerHTML = i + 1;
        });
      }).draw();
    }
  }

  //Pagos
  function registrarPagoXClases(elemento) {
    var datosPago = obtenerDatos(elemento);
    formularioPagoDocente.registrar(datosPago);
  }
  function editarPagoXClases(elemento) {
    var datosPago = obtenerDatos(elemento);
    formularioPagoDocente.editar(datosPago);
  }
  function obtenerDatosFiltrosBusqueda() {
    var d = {};
    d.estadoPago = $("#bus-estado").val();
    $.extend(d, filtrosBusquedaFechas.obtenerDatos());
    return d;
  }

  return {
    mostrar: mostrar,
    reCargar: reCargar,
    cargarListaClases: cargarListaClases,
    registrarPagoXClases: registrarPagoXClases,
    editarPagoXClases: editarPagoXClases,
    obtenerDatosFiltrosBusqueda: obtenerDatosFiltrosBusqueda
  };
}());