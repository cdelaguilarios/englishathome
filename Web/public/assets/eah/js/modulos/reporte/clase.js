google.charts.load("current", {"packages": ["bar"]});
google.charts.setOnLoadCallback(cargarReporteClases);

function cargarReporteClases() {
  cargarListaClases();
  cargarGrafico(false, "clases", "clase", "clases");
}

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
          }},
        {data: "nombreProfesor", name: "nombreProfesor", render: function (e, t, d, m) {
            return (d.idProfesor !== null ? '<a target="_blank" href="' + urlPerfilProfesor.replace("/0", "/" + d.idProfesor) + '">' + d.nombreProfesor + ' ' + d.apellidoProfesor + '</a>' : "Sin profesor asignado");
          }},
        {data: "nombreAlumno", name: "nombreAlumno", render: function (e, t, d, m) {
            return '<a target="_blank" href="' + urlPerfilAlumno.replace("/0", "/" + d.idAlumno) + '">' + d.nombreAlumno + ' ' + d.apellidoAlumno + '</a>';
          }},
        {data: "fechaInicio", name: "fechaInicio", render: function (e, t, d, m) {
            return formatoFecha(d.fechaInicio) + ' - De ' + formatoFecha(d.fechaInicio, false, true) + ' a ' + formatoFecha(d.fechaFin, false, true);
          }},
        {data: "duracion", name: "duracion", render: function (e, t, d, m) {
            return formatoHora(d.duracion);
          }},
        {data: "costoHoraProfesor", name: "costoHoraProfesor", render: function (e, t, d, m) {
            return "S/. " + redondear(d.costoHoraProfesor, 2);
          }},
        {data: "estado", name: "estado", render: function (e, t, d, m) {
            return '<span class="label ' + estados[d.estado][1] + ' btn-estado">Clase - ' + estados[d.estado][0] + '</span>' + (d.estadoPago !== null ? '<br/><span class="label ' + estadosPago[d.estadoPago][1] + ' btn-estado">Pago al profesor - ' + estadosPago[d.estadoPago][0] + '</span>' : '');
          }, className: "text-center"}
      ],
      initComplete: function (s, j) {
        $("#tab-lista").DataTable().ajax.reload();
        establecerBotonRecargaTabla("tab-lista");
      },
      footerCallback: function (r, d, s, e, di) {
        var api = this.api();

        var intVal = function (i) {
          return typeof i === 'string' ?
              i.replace(/[\$,]/g, '') * 1 :
              typeof i === 'number' ?
              i : 0;
        };
        total = api
            .column(5)
            .data()
            .reduce(function (a, b) {
              return intVal(a) + intVal(b);
            }, 0);
        pageTotal = api
            .column(5, {page: 'current'})
            .data()
            .reduce(function (a, b) {
              return intVal(a) + intVal(b);
            }, 0);
            //$('#tab-lista').DataTable().rows( { filter : 'applied'} ).data()
            //$('#tab-lista').dataTable().fnGetData()
        $(api.column(5).footer()).html("S/. " + redondear(pageTotal, 2) + " (Un total de S/." + redondear(total, 2) + ")");
      }
    });
    cargarFiltrosBusqueda(function () {
      $("#tab-lista").DataTable().ajax.reload();
    });
  }
}