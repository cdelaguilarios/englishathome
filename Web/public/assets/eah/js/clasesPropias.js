$(document).ready(function () {
  cargarLista();
});
function cargarLista() {
  urlListar = (typeof (urlListar) === "undefined" ? "" : urlListar);
  estados = (typeof (estados) === "undefined" ? "" : estados);
  estadosClase = (typeof (estadosClase) === "undefined" ? "" : estadosClase);
  usuarioActualEsAlumno = (typeof (usuarioActualEsAlumno) === "undefined" ? false : usuarioActualEsAlumno);

  if (urlListar !== "" && estados !== "" && estadosClase !== "" && usuarioActualEsAlumno !== "") {
    $("#tab-lista").DataTable({
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
      autoWidth: false,
      responsive: true,
      order: [[1, "desc"]],
      columns: [
        {data: "", name: "", orderable: false, "searchable": false, "className": "text-center not-mobile",
          render: function (data, type, row, meta) {
            return meta.row + meta.settings._iDisplayStart + 1;
          }},
        {data: "fechaInicio", name: "fechaInicio", render: function (e, t, d, m) {
            return '<b>Fecha:</b> ' + formatoFecha(d.fechaInicio) + ' - De ' + formatoFecha(d.fechaInicio, false, true) + ' a ' + formatoFecha(d.fechaFin, false, true) + '<br/>'
                + '<b>Duración:</b> ' + formatoHora(d.duracion) + '<br/>'
                + (d.idHistorial !== null ?
                    '<b>Notificar:</b> ' + ' <i class="fa fa-check icon-notificar-clase"></i>' + '<br/>' : '')
                + (usuarioActualEsAlumno ? '<b>Profesor:</b> ' + (d.idProfesor !== null && d.nombreProfesor !== null && d.nombreProfesor !== '' ? d.nombreProfesor + ' ' + d.apellidoProfesor : 'Sin profesor asignado') : '<b>Alumno:</b> ' + (d.idAlumno !== null && d.nombreAlumno !== null && d.nombreAlumno !== '' ? d.nombreAlumno + ' ' + d.apellidoAlumno : ''));
          }},
        {data: "estado", name: "estado", render: function (e, t, d, m) {
            return (estadosClase[d.estado] !== undefined ?
                '<span class="label ' + estadosClase[d.estado][1] + ' btn-estado">' + estadosClase[d.estado][0] + '</span>' : '');
          }, className: "text-center"},
        {data: "comentarioEntidad", name: "comentarioEntidad"},
        {data: "comentarioAdministrador", name: "comentarioAdministrador"}
      ],
      initComplete: function (s, j) {
        establecerBotonRecargaTabla("tab-lista");
        establecerCabecerasBusquedaTabla("tab-lista");
      }
    });
  }
  $("#bus-estado").change(function () {
    $("#tab-lista").DataTable().ajax.reload();
  });
}