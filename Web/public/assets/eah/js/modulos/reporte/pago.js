google.charts.load("current", {"packages": ["bar"]});
google.charts.setOnLoadCallback(cargarReportePagos);

function cargarReportePagos() {
  cargarListaPagos();
  cargarGrafico(true, "pagos", "sol", "soles");
}

var primeraRecargaListaPagos = true;
function cargarListaPagos() {
  urlListar = (typeof (urlListar) === "undefined" ? "" : urlListar);
  urlPerfilAlumno = (typeof (urlPerfilAlumno) === "undefined" ? "" : urlPerfilAlumno);
  urlPerfilProfesor = (typeof (urlPerfilProfesor) === "undefined" ? "" : urlPerfilProfesor);
  estados = (typeof (estados) === "undefined" ? "" : estados);
  motivosPago = (typeof (motivosPago) === "undefined" ? "" : motivosPago);
  cuentasBanco = (typeof (cuentasBanco) === "undefined" ? "" : cuentasBanco);
  if (urlListar !== "" && urlPerfilAlumno !== "" && urlPerfilProfesor !== "" && estados !== "" && motivosPago !== "" && cuentasBanco !== "") {
    $("#tab-lista").DataTable({
      processing: true,
      serverSide: false,
      ajax: {
        url: urlListar,
        type: "POST",
        data: function (d) {
          d._token = $("meta[name=_token]").attr("content");
          $.extend(d, obtenerDatosFiltrosBusqueda());
        }
      },
      autoWidth: false,
      responsive: true,
      order: [[4, "desc"]],
      columns: [
        {data: "id", name: "id", className: "text-center"},
        {data: "nombreEntidad", name: "nombreEntidad", render: function (e, t, d, m) {
            return (d.idEntidad !== null ? '<a target="_blank" href="' + (d.esEntidadProfesor === 1 ? urlPerfilProfesor : urlPerfilAlumno).replace("/0", "/" + d.idEntidad) + '">' + d.nombreEntidad + ' ' + d.apellidoEntidad + '</a>' : "");
          }},
        {data: "motivo", name: "motivo", render: function (e, t, d, m) {
            return motivosPago[d.motivo];
          }},
        {data: "cuenta", name: "cuenta", render: function (e, t, d, m) {
            return cuentasBanco[d.cuenta];
          }, className: "text-center"},
        {data: "fechaRegistro", name: "fechaRegistro", render: function (e, t, d, m) {
            return formatoFecha(d.fechaRegistro, true);
          }, className: "text-center"},
        {data: "estado", name: "estado", render: function (e, t, d, m) {
            return '<span class="label ' + estados[d.estado][1] + ' btn-estado">' + estados[d.estado][0] + '</span>';
          }, className: "text-center"},
        {data: "monto", name: "monto", render: function (e, t, d, m) {
            return 'S/. ' + redondear(d.monto, 2) + (d.saldoFavor !== null && parseFloat(d.saldoFavor + "") > 0 ? '<br/><small><b>Saldo a favor de S/. ' + redondear(d.saldoFavor, 2) + (d.saldoFavorUtilizado !== null && d.saldoFavorUtilizado === 1 ? ' (<span class="saldo-favor-utilizado">utilizado</span>)' : '') + '</b></small>' : '');
          }, className: "text-center"}
      ],
      initComplete: function (s, j) {
        establecerBotonRecargaTabla("tab-lista");
      },
      footerCallback: function (r, d, s, e, di) {
        var api = this.api();

        var montoTotal = 0, montoTotalPagina = 0;
        $('#tab-lista').DataTable().rows({filter: 'applied'}).data().each(function (i) {
          montoTotal += parseFloat(i.monto) + (i.saldoFavor !== null ? parseFloat(i.saldoFavor + "") : 0);
        });
        $('#tab-lista').DataTable().rows({page: 'current'}).data().each(function (i) {
          montoTotalPagina += parseFloat(i.monto) + (i.saldoFavor !== null ? parseFloat(i.saldoFavor + "") : 0);
        });
        $(api.column(6).footer()).html("Total S/. " + redondear(montoTotal, 2) + (montoTotal !== montoTotalPagina ? "<br/>Total de la página S/." + redondear(montoTotalPagina, 2) : ""));
      },
      drawCallback: function (os) {
        var idsSel = [];
        $('#tab-lista').DataTable().rows({filter: 'applied'}).data().each(function (i) {
          idsSel.push(i.id);
        });
        cargarDatosGrafico(idsSel, true);
      }
    });
    cargarFiltrosBusqueda(function () {
      if (!primeraRecargaListaPagos) {
        $("#tab-lista").DataTable().ajax.reload();
      } else {
        primeraRecargaListaPagos = false;
      }
    });
  }
}
