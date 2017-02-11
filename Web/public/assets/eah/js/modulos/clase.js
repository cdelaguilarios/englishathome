$(document).ready(function () {
  cargarLista();
});

//Lista
function cargarLista() {
  urlListar = (typeof (urlListar) === "undefined" ? "" : urlListar);
  urlPerfilAlumno = (typeof (urlPerfilAlumno) === "undefined" ? "" : urlPerfilAlumno);
  urlPerfilProfesor = (typeof (urlPerfilProfesor) === "undefined" ? "" : urlPerfilProfesor);
  estados = (typeof (estados) === "undefined" ? "" : estados);
  estadosPago = (typeof (estadosPago) === "undefined" ? "" : estadosPago);
  if (urlListar !== "" && urlPerfilAlumno !== "" && urlPerfilProfesor !== "" && estados !== "" && estadosPago !== "") {
    $("#tab-lista").DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: urlListar,
        type: "POST",
        data: function (d) {
          d._token = $("meta[name=_token]").attr("content");
          d.estado = $("#bus-estado").val();
          d.tipoBusquedaFecha = $("#bus-tipo-fecha").val();
          d.fechaDia = $("#bus-fecha-dia").val();
          d.fechaMesInicio = $("#bus-fecha-mes-inicio").val();
          d.fechaAnhoInicio = $("#bus-fecha-anho-inicio").val();
          d.fechaInicio = $("#bus-fecha-inicio").val();
          d.fechaFin = $("#bus-fecha-fin").val();
        }
      },
      autoWidth: false,
      responsive: true,
      order: [[0, "desc"]],
      columns: [
        {data: "numeroPeriodo", name: "numeroPeriodo", render: function (e, t, d, m) {
            return d.numeroPeriodo;
          }},
        {data: "idAlumno", name: "idAlumno", render: function (e, t, d, m) {
            return '<a target="_blank" href="' + urlPerfilAlumno.replace("/0", "/" + d.idAlumno) + '">' + d.nombreAlumno + ' ' + d.apellidoAlumno + '</a>';
          }},
        {data: "idProfesor", name: "idProfesor", render: function (e, t, d, m) {
            return (d.idProfesor !== null ? '<a target="_blank" href="' + urlPerfilProfesor.replace("/0", "/" + d.idProfesor) + '">' + d.nombreProfesor + ' ' + d.apellidoProfesor + '</a>' : "Sin profesor asignado");
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
      ]
    });

    establecerCalendario("bus-fecha-dia");
    establecerCalendario("bus-fecha-mes-inicio", false, false, false, true);
    establecerCalendario("bus-fecha-anho-inicio", false, false, false, false, true);
    establecerCalendario("bus-fecha-inicio");
    establecerCalendario("bus-fecha-fin");
    $("#bus-estado, #bus-tipo-fecha, #bus-fecha-dia, #bus-fecha-mes-inicio, #bus-fecha-anho-inicio, #bus-fecha-inicio, #bus-fecha-fin").change(function () {
      $("#tab-lista").DataTable().ajax.reload();
    });

    $("#bus-tipo-fecha").change(function () {
      $('[id*="sec-bus-fecha-"]').hide();
      $("#sec-bus-fecha-" + $(this).val()).show();
    });
    $("#bus-tipo-fecha").trigger("change");
  }
}