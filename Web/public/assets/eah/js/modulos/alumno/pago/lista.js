var listaPagosAlumno = {};
listaPagosAlumno = (function ()/* - */ {
  window.addEventListener("load", esperarCargaJquery, false);
  function esperarCargaJquery()/* - */ {
    ((window.jQuery && jQuery.ui) ? cargar() : window.setTimeout(esperarCargaJquery, 100));
  }

  //Privado
  function cargar()/* - */ {
    cargarLista();

    $("a[href='#pago']").click(function () {
      $(this).tab("show");
      $("#tab-lista-pagos").DataTable().responsive.recalc();
    });
    $("#btn-nuevo-pago").click(function () {
      crearEditarPagoAlumno.crear();
    });
  }
  function cargarLista()/* - */ {
    urlListarPagos = (typeof (urlListarPagos) === "undefined" ? "" : urlListarPagos);
    urlEliminarPago = (typeof (urlEliminarPago) === "undefined" ? "" : urlEliminarPago);

    motivosPago = (typeof (motivosPago) === "undefined" ? "" : motivosPago);
    motivoPagoXClases = (typeof (motivoPagoXClases) === "undefined" ? "" : motivoPagoXClases);
    cuentasBanco = (typeof (cuentasBanco) === "undefined" ? "" : cuentasBanco);
    estadosPago = (typeof (estadosPago) === "undefined" ? "" : estadosPago);
    estadosPagoDisponibleCambio = (typeof (estadosPagoDisponibleCambio) === "undefined" ? "" : estadosPagoDisponibleCambio);
    estadoPagoCosumido = (typeof (estadoPagoCosumido) === "undefined" ? "" : estadoPagoCosumido);

    if (urlListarPagos !== "" && urlEliminarPago !== "" && motivosPago !== "" && cuentasBanco !== "" && estadosPago !== "" && estadosPagoDisponibleCambio !== "") {
      $("#tab-lista-pagos").DataTable({
        processing: true,
        serverSide: true,
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
        rowId: 'id',
        columns: [
          {data: "id", name: "id", render: function (e, t, d, m) {
              var costoXHoraClase = (d.costoXHoraClase !== null ? parseFloat(d.costoXHoraClase + "") : 0);

              return '<b>Código: </b>' + d.id +
                      '<br/><b>Motivo: </b>' + motivosPago[d.motivo] +
                      '<br/><b>Cuenta: </b>' + cuentasBanco[d.cuenta] +
                      (d.descripcion !== null && d.descripcion !== "" ? '<br/><b>Descripción: </b>' + d.descripcion : '') +
                      (d.motivo === motivoPagoXClases && costoXHoraClase > 0 ? '<br/><small><b>S/. ' + util.redondear(costoXHoraClase, 2) + ' por hora de clase</b></small>' : '');
            }},
          {data: "fecha", name: "fecha", render: function (e, t, d, m) {
              return utilFechasHorarios.formatoFecha(d.fecha, false);
            }, className: "text-center", type: "fecha"},
          {data: "estado", name: "estado", render: function (e, t, d, m) {
              var estado = '';
              if (estadosPago[d.estado] !== undefined) {
                if (estadosPagoDisponibleCambio[d.estado] !== undefined) {
                  estado = '<div class="sec-btn-editar-estado" data-idtabla="tab-lista-pagos" data-idselestados="sel-estados-pago" data-tipocambio="1">' +
                          '<a href="javascript:void(0);" class="btn-editar-estado" data-id="' + d.id + '" data-estado="' + d.estado + '">' +
                          '<span class="label ' + estadosPago[d.estado][1] + ' btn-estado">' + estadosPago[d.estado][0] + '</span>' +
                          '</a>' +
                          '</div>';
                } else {
                  estado = '<span class="label ' + estadosPago[d.estado][1] + ' btn-estado">' + estadosPago[d.estado][0] + '</span><br/>';
                }
              }
              return estado /*+ (d.estado === estadoPagoCosumido ? '<small class="text-info"' + (d.motivo === motivoPagoXClases ? ' data-toggle="tooltip" title="No será considerado dentro de la bolsa de horas"' : '') + '>(Pago consumido)</small>' : '')*/;
            }, className: "text-center"},
          {data: "monto", name: "monto", render: function (e, t, d, m) {
              var datos = '<b>S/. ' + util.redondear(d.monto, 2) + '</b>';
              if (d.motivo === motivoPagoXClases) {
                var saldoFavor = (d.saldoFavor !== null ? parseFloat(d.saldoFavor + "") : 0);
                var saldoFavorUtilizado = (d.saldoFavorUtilizado !== null && d.saldoFavorUtilizado === 1);

                datos += ('<div class="info-adicional">' +
                        (d.duracionTotalXClasesRealizadas > 0 ?
                                '<br/><span class="text-green" data-toggle="tooltip" title="Horas realizadas">' +
                                '<i class="fa fa-clock-o"></i> ' + utilFechasHorarios.formatoHora(d.duracionTotalXClasesRealizadas) + ' (S/. ' + util.redondear(d.montoTotalXClasesRealizadas, 2) + ')' +
                                '</span>' : '') +
                        (d.numeroClasesCanceladas > 0 ?
                                '<br/><span class="text-green-disabled" data-toggle="tooltip" title="Horas consumidas por ' + d.numeroClasesCanceladas + ' clase(s) cancelada(s)">' +
                                '<i class="fa fa-clock-o"></i> ' + utilFechasHorarios.formatoHora(d.duracionTotalXClasesCanceladas) + ' (S/. ' + util.redondear(d.montoTotalXClasesCanceladas, 2) + ')' +
                                '</span>' : '') +
                        (d.duracionTotalXClasesPendientes > 0 /*&& d.estado !== estadoPagoCosumido*/ ?
                                '<br/><span class="text-yellow" data-toggle="tooltip" title="Horas pendientes">' +
                                '<i class="fa fa-clock-o"></i> ' + utilFechasHorarios.formatoHora(d.duracionTotalXClasesPendientes) + ' (S/. ' + util.redondear(d.montoTotalXClasesPendientes, 2) + ')' +
                                '</span>' : '') +
                        (d.duracionTotalXClasesNoPagadas > 0 /*&& d.estado !== estadoPagoCosumido*/ ?
                                '<br/><span class="text-red" data-toggle="tooltip" title="Horas no pagadas">' +
                                '<i class="fa fa-clock-o"></i> ' + utilFechasHorarios.formatoHora(d.duracionTotalXClasesNoPagadas) + ' (S/. ' + util.redondear(d.montoTotalXClasesNoPagadas, 2) + ')' +
                                '</span>' : '') +
                        (d.duracionTotalXClases !== d.duracionTotalXClasesRealizadas ?
                                '<br/><span class="text-info" data-toggle="tooltip" title="Horas en total">' +
                                '<i class="fa fa-clock-o"></i> ' + utilFechasHorarios.formatoHora(d.duracionTotalXClases) + ' (S/. ' + util.redondear(d.montoTotalXClases, 2) + ')' +
                                '</span>' : '') +
                        (saldoFavor > 0 ?
                                '<br/><small><b>Saldo a favor de S/. ' + util.redondear(saldoFavor, (saldoFavor < 0.01 ? 4 : 2)) + (saldoFavorUtilizado ? ' (<span class="text-green">utilizado</span>)' :
                                '') + '</b></small>' : '') + '</div>'
                        );
              }
              return datos;
            }, className: "text-center", type: "monto"},
          {data: "id", name: "id", orderable: false, searchable: false, width: "5%", render: function (e, t, d, m) {
              return '<ul class="buttons">' +
                      '<li>' +
                      '<a href="javascript:void(0);" onclick="crearEditarPagoAlumno.editar(' + d.id + ');" title="Editar datos del pago"><i class="fa fa-pencil"></i></a>' +
                      '</li>' +
                      '<li>' +
                      '<a href="javascript:void(0);" title="Eliminar pago" onclick="utilTablas.eliminarElemento(this, \'¿Está seguro que desea eliminar los datos de este pago?, considere que si el pago está relacionado a una o más clases estas también serán eliminadas.\', \'tab-lista-pagos\', false, function(){utilTablas.recargarDatosTabla($(\'#tab-lista-clases\'));}, true)" data-id="' + d.id + '" data-urleliminar="' + ((urlEliminarPago.replace("/0", "/" + d.id))) + '">' + //TODO: revisar función reiniciarHistorial()
                      '<i class="fa fa-trash"></i>' +
                      '</a>' +
                      '</li>' +
                      '</ul>';
            }, className: "text-center"}
        ],
        initComplete: function (s, j) {
          utilTablas.establecerBotonRecargaTabla($("#tab-lista-pagos"));
          utilTablas.establecerCabecerasBusquedaTabla($("#tab-lista-pagos"));
        },
        footerCallback: function (r, d, s, e, di) {
          var api = this.api();

          var saldoFavorTotal = 0;
          $('#tab-lista-pagos').DataTable().rows().data().each(function (datosPago) {
            var saldoFavorPago = (datosPago.saldoFavor !== null ? parseFloat(datosPago.saldoFavor + "") : 0);
            if (!(datosPago.saldoFavorUtilizado !== null && datosPago.saldoFavorUtilizado === 1)) {
              saldoFavorTotal += saldoFavorPago;
            }
          });
          formularioPagoAlumno.establecerSaldoFavor(saldoFavorTotal);

          var montoTotal = 0, montoTotalPagina = 0;
          var saldoFavorTotalUtilizadoTotal = 0, saldoFavorTotalUtilizadoPagina = 0;
          $('#tab-lista-pagos').DataTable().rows({filter: 'applied'}).data().each(function (datosPago) {
            var saldoFavorPago = (datosPago.saldoFavor !== null ? parseFloat(datosPago.saldoFavor + "") : 0);
            if (datosPago.saldoFavorUtilizado !== null && datosPago.saldoFavorUtilizado === 1) {
              saldoFavorTotalUtilizadoTotal += saldoFavorPago;
            }
            montoTotal += parseFloat(datosPago.monto);
          });
          $('#tab-lista-pagos').DataTable().rows({page: 'current'}).data().each(function (datosPago) {
            var saldoFavorPago = (datosPago.saldoFavor !== null ? parseFloat(datosPago.saldoFavor + "") : 0);
            if (datosPago.saldoFavorUtilizado !== null && datosPago.saldoFavorUtilizado === 1) {
              saldoFavorTotalUtilizadoPagina += saldoFavorPago;
            }
            montoTotalPagina += parseFloat(datosPago.monto);
          });
          $(api.column(3).footer()).html(
                  "Total S/. " + util.redondear(montoTotal - saldoFavorTotalUtilizadoTotal, 2) +
                  (montoTotal !== montoTotalPagina
                          ? "<br/>Total de la página S/." + util.redondear(montoTotalPagina - saldoFavorTotalUtilizadoPagina, 2)
                          : "")
                  );
        }
      });
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

  return {
    mostrar: mostrar,
    reCargar: reCargar
  };
}());