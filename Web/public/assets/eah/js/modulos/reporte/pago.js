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
            return (d.idEntidad !== null && d.nombreEntidad !== null && d.nombreEntidad !== '' ? '<a target="_blank" href="' + (d.esEntidadProfesor === 1 ? urlPerfilProfesor : urlPerfilAlumno).replace("/0", "/" + d.idEntidad) + '">' + d.nombreEntidad + ' ' + d.apellidoEntidad + '</a>' : "");
          }},
        {data: "motivo", name: "motivo", render: function (e, t, d, m) {
            return motivosPago[d.motivo];
          }},
        {data: "cuenta", name: "cuenta", render: function (e, t, d, m) {
            return cuentasBanco[d.cuenta];
          }, className: "text-center"},
        {data: "fecha", name: "fecha", render: function (e, t, d, m) {
            return utilFechasHorarios.formatoFecha(d.fecha);
          }, className: "text-center", type: "fecha"},
        {data: "estado", name: "estado", render: function (e, t, d, m) {
            return '<span class="label ' + estados[d.estado][1] + ' btn-estado">' + estados[d.estado][0] + '</span>';
          }, className: "text-center"},
        {data: "monto", name: "monto", render: function (e, t, d, m) {
            return 'S/. ' + util.redondear(d.monto, 2);
          }, className: "text-center", type: "monto"}
      ],
      initComplete: function (s, j) {
        utilTablas.establecerBotonRecargaTabla($("#tab-lista"));
      },
      footerCallback: function (r, d, s, e, di) {
        var api = this.api();

        var montoTotal = 0, montoTotalPagina = 0;
        $('#tab-lista').DataTable().rows({filter: 'applied'}).data().each(function (i) {
          montoTotal += parseFloat(i.monto);
        });
        $('#tab-lista').DataTable().rows({page: 'current'}).data().each(function (i) {
          montoTotalPagina += parseFloat(i.monto);
        });
        $(api.column(6).footer()).html("Total S/. " + util.redondear(montoTotal, 2) + (montoTotal !== montoTotalPagina ? "<br/>Total de la página S/." + util.redondear(montoTotalPagina, 2) : ""));
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
