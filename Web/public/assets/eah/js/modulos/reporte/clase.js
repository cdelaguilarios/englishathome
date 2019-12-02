google.charts.load("current", {"packages": ["bar"]});
google.charts.setOnLoadCallback(cargarReporteClases);

function cargarReporteClases() {
  cargarListaClases();
  cargarGrafico(false, "clases", "clase", "clases");
}

var primeraRecargaListaClases = true;
function cargarListaClases() {
  urlListar = (typeof (urlListar) === "undefined" ? "" : urlListar);
  urlPerfilAlumno = (typeof (urlPerfilAlumno) === "undefined" ? "" : urlPerfilAlumno);
  urlPerfilProfesor = (typeof (urlPerfilProfesor) === "undefined" ? "" : urlPerfilProfesor);
  estados = (typeof (estados) === "undefined" ? "" : estados);
  estadosPago = (typeof (estadosPago) === "undefined" ? "" : estadosPago);
  if (urlListar !== "" && urlPerfilAlumno !== "" && urlPerfilProfesor !== "" && estados !== "" && estadosPago !== "") {
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
      order: [[3, "desc"]],
      columns: [
        {data: "numeroPeriodo", name: "numeroPeriodo", render: function (e, t, d, m) {
            return d.numeroPeriodo;
          }, className: "text-center"},
        {data: "nombreProfesor", name: "nombreProfesor", render: function (e, t, d, m) {
            return (d.idProfesor !== null && d.nombreProfesor !== null && d.nombreProfesor !== '' ? '<a target="_blank" href="' + urlPerfilProfesor.replace("/0", "/" + d.idProfesor) + '">' + d.nombreProfesor + ' ' + d.apellidoProfesor + '</a>' : "Sin profesor asignado");
          }},
        {data: "nombreAlumno", name: "nombreAlumno", render: function (e, t, d, m) {
            return '<a target="_blank" href="' + urlPerfilAlumno.replace("/0", "/" + d.idAlumno) + '">' + d.nombreAlumno + ' ' + d.apellidoAlumno + '</a>';
          }},
        {data: "fechaInicio", name: "fechaInicio", render: function (e, t, d, m) {
            return utilFechasHorarios.formatoFecha(d.fechaInicio) + ' - De ' + utilFechasHorarios.formatoFecha(d.fechaInicio, false, true) + ' a ' + utilFechasHorarios.formatoFecha(d.fechaFin, false, true);
          }, className: "text-center", type: "fecha"},
        {data: "estado", name: "estado", render: function (e, t, d, m) {
            return '<span class="label ' + estados[d.estado][1] + ' btn-estado">Clase - ' + estados[d.estado][0] + '</span>' + (d.estadoPago !== null ? '<br/><span class="label ' + estadosPago[d.estadoPago][1] + ' btn-estado">Pago al profesor - ' + estadosPago[d.estadoPago][0] + '</span>' : '');
          }, className: "text-center"},
        {data: "duracion", name: "duracion", render: function (e, t, d, m) {
            return utilFechasHorarios.formatoHora(d.duracion);
          }, className: "text-center"},
        {data: "costoHoraProfesor", name: "costoHoraProfesor", render: function (e, t, d, m) {
            return "S/. " + util.redondear(d.costoHoraProfesor, 2) + (d.pagoTotalProfesor !== null ? ("<br/>(Pago total de S/. " + util.redondear(d.pagoTotalProfesor, 2) + ")") : "");
          }, className: "text-center", type: "monto"}
      ],
      initComplete: function (s, j) {
        utilTablas.establecerBotonRecargaTabla($("#tab-lista"));
      },
      footerCallback: function (r, d, s, e, di) {
        var api = this.api();

        var totalPagoProfesor = 0, totalPagoProfesorPagina = 0;
        $('#tab-lista').DataTable().rows({filter: 'applied'}).data().each(function (i) {
          totalPagoProfesor += (i.idProfesor !== null && i.nombreProfesor !== null && i.nombreProfesor !== '' ? (i.pagoTotalProfesor !== null ? parseFloat(i.pagoTotalProfesor) : ((i.duracion !== 0 ? (i.duracion / 3600) : 0) * parseFloat(i.costoHoraProfesor))) : 0);
        });
        $('#tab-lista').DataTable().rows({page: 'current'}).data().each(function (i) {
          totalPagoProfesorPagina += (i.idProfesor !== null && i.nombreProfesor !== null && i.nombreProfesor !== '' ? (i.pagoTotalProfesor !== null ? parseFloat(i.pagoTotalProfesor) : ((i.duracion !== 0 ? (i.duracion / 3600) : 0) * parseFloat(i.costoHoraProfesor))) : 0);
        });
        $(api.column(5).footer()).html("Total S/. " + util.redondear(totalPagoProfesor, 2) + (totalPagoProfesor !== totalPagoProfesorPagina ? "<br/>Total de la página S/." + util.redondear(totalPagoProfesorPagina, 2) : ""));
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
      if (!primeraRecargaListaClases) {
        $("#tab-lista").DataTable().ajax.reload();
      } else {
        primeraRecargaListaClases = false;
      }
    });
  }
}