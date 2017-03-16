google.charts.load("current", {"packages": ["bar"]});
google.charts.setOnLoadCallback(cargarReportePagos);

function cargarReportePagos() {
  cargarListaPagos();
  cargarGrafico(true, "pagos", "sol", "soles");
}

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
      serverSide: true,
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
      order: [[5, "desc"]],
      columns: [
        {data: "idEntidad", name: "idEntidad", render: function (e, t, d, m) {
            return (d.idEntidad !== null ? '<a target="_blank" href="' + (d.esEntidadProfesor === 1 ? urlPerfilProfesor : urlPerfilAlumno).replace("/0", "/" + d.idEntidad) + '">' + d.nombreEntidad + ' ' + d.apellidoEntidad + '</a>' : "");
          }},
        {data: "id", name: "pago.id"},
        {data: "motivo", name: "pago.motivo", render: function (e, t, d, m) {
            return motivosPago[d.motivo];
          }},
        {data: "cuenta", name: "pago.cuenta", render: function (e, t, d, m) {
            return cuentasBanco[d.cuenta];
          }},
        {data: "monto", name: "pago.monto", render: function (e, t, d, m) {
            return 'S/. ' + redondear(d.monto, 2) + (d.saldoFavor !== null && parseFloat(d.saldoFavor + "") > 0 ? '<br/><small><b>Saldo a favor de S/. ' + redondear(d.saldoFavor, 2) + (d.saldoFavorUtilizado !== null && d.saldoFavorUtilizado === 1 ? ' (<span class="saldo-favor-utilizado">utilizado</span>)' : '') + '</b></small>' : '');
          }},
        {data: "fechaRegistro", name: "pago.fechaRegistro", render: function (e, t, d, m) {
            return formatoFecha(d.fechaRegistro, true);
          }, className: "text-center"},
        {data: "estado", name: "estado", render: function (e, t, d, m) {
            return '<span class="label ' + estados[d.estado][1] + ' btn-estado">' + estados[d.estado][0] + '</span>';
          }, className: "text-center"}
      ],
      initComplete: function (s, j) {
        establecerBotonRecargaTabla("tab-lista");
      }
    });
    cargarFiltrosBusqueda(function () {
      $("#tab-lista").DataTable().ajax.reload();
    });
  }
}
