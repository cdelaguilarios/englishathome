var listaPagosProfesor = {};
listaPagosProfesor = (function () {
  window.addEventListener("load", esperarCargaJquery, false);
  function esperarCargaJquery() {
    ((window.jQuery && jQuery.ui) ? cargarSeccion() : window.setTimeout(esperarCargaJquery, 100));
  }

  //Privado
  function cargarSeccion() {
    cargarLista();

    $("a[href='#pago']").click(function () {
      $(this).tab("show");
      $("#tab-lista-pagos").DataTable().responsive.recalc();
    });
    $("#btn-nuevo-pago").click(function () {
      crearEditarPagoGeneralProfesor.crear();
    });
  }
  function cargarLista() {
    urlListarPagos = (typeof (urlListarPagos) === "undefined" ? "" : urlListarPagos);
    urlEliminarPago = (typeof (urlEliminarPago) === "undefined" ? "" : urlEliminarPago);

    motivosPago = (typeof (motivosPago) === "undefined" ? "" : motivosPago);
    motivoPagoClases = (typeof (motivoPagoClases) === "undefined" ? "" : motivoPagoClases);
    estadosPago = (typeof (estadosPago) === "undefined" ? "" : estadosPago);

    if (urlListarPagos !== "" && urlEliminarPago !== "" && motivosPago !== "" && estadosPago !== "") {
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
              return '<b>Código: </b>' + d.id +
                      '<br/><b>Motivo: </b>' + motivosPago[d.motivo] +
                      (d.motivo === motivoPagoClases && d.duracionTotalXClases > 0 ? ' <small><b>(' + utilFechasHorarios.formatoHora(d.duracionTotalXClases) + ' hora(s) de clases)</b></small>' : '');
            }, responsivePriority: 0},
          {data: "fecha", name: "fecha", render: function (e, t, d, m) {
              return utilFechasHorarios.formatoFecha(d.fecha, false);
            }, className: "text-center min-tablet-l", type: "fecha"},
          {data: "estado", name: "estado", render: function (e, t, d, m) {
              var estado = '';
              if (d.motivo !== motivoPagoClases) {
                estado = '<div class="sec-btn-editar-estado" data-idtabla="tab-lista-pagos" data-idselestados="sel-estados-pago" data-tipocambio="1">' +
                        '<a href="javascript:void(0);" class="btn-editar-estado" data-id="' + d.id + '" data-estado="' + d.estado + '">' +
                        '<span class="label ' + estadosPago[d.estado][1] + ' btn-estado">' + estadosPago[d.estado][0] + '</span>' +
                        '</a>' +
                        '</div>';
              } else {
                estado = '<span class="label ' + estadosPago[d.estado][1] + ' btn-estado">' + estadosPago[d.estado][0] + '</span>';
              }
              return estado;
            }, className: "text-center", responsivePriority: 0},
          {data: "monto", name: "monto", render: function (e, t, d, m) {
              return 'S/. ' + util.redondear(d.monto, 2);
            }, className: "text-center min-tablet-p", type: "monto"},
          {data: "id", name: "id", orderable: false, searchable: false, width: "5%", render: function (e, t, d, m) {
              return '<ul class="buttons">' +
                      (d.motivo !== motivoPagoClases ?
                              '<li>' +
                              '<a href="javascript:void(0);" onclick="crearEditarPagoGeneralProfesor.editar(' + d.id + ');" title="Editar datos del pago"><i class="fa fa-pencil"></i></a>' +
                              '</li>' : '') +
                      '<li>' +
                      '<a href="javascript:void(0);" title="Eliminar pago" onclick="utilTablas.eliminarElemento(this, \'¿Está seguro que desea eliminar los datos de este pago?\', \'tab-lista-pagos\', false, function(){utilTablas.recargarDatosTabla($(\'#tab-lista-clases\'));listaNotificacionesHistorial.reCargar();})" data-id="' + d.id + '" data-urleliminar="' + ((urlEliminarPago.replace("/0", "/" + d.id))) + '">' + 
                      '<i class="fa fa-trash"></i>' +
                      '</a>' +
                      '</li>' +
                      '</ul>';
            }, className: "text-center min-mobile-l"}
        ],
        initComplete: function (s, j) {
          utilTablas.establecerBotonRecargaTabla($("#tab-lista-pagos"));
          utilTablas.establecerCabecerasBusquedaTabla($("#tab-lista-pagos"));
        },
        footerCallback: function (r, d, s, e, di) {
          var api = this.api();

          var montoTotal = 0, montoTotalPagina = 0;
          $('#tab-lista-pagos').DataTable().rows({filter: 'applied'}).data().each(function (i) {
            montoTotal += parseFloat(i.monto);
          });
          $('#tab-lista-pagos').DataTable().rows({page: 'current'}).data().each(function (i) {
            montoTotalPagina += parseFloat(i.monto);
          });
          $(api.column(3).footer()).html("Total S/. " + util.redondear(montoTotal, 2) + (montoTotal !== montoTotalPagina ? "<br/>Total de la página S/." + util.redondear(montoTotalPagina, 2) : ""));
        }
      });
    }
  }

  //Público
  function mostrar() {
    $("div[id^=sec-pago-]").hide();
    utilTablas.recargarDatosTabla($("#tab-lista-pagos"));
    $("#sec-pago-lista").show();
  }
  function reCargar() {
    $("#tab-lista-pagos").DataTable().ajax.reload();
  }

  return {
    mostrar: mostrar,
    reCargar: reCargar
  };
}());