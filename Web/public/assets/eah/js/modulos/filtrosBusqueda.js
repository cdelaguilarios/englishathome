function cargarFiltrosBusqueda(funcionBusquedaCambio) {
  if (funcionBusquedaCambio !== undefined) {
    establecerCalendario("bus-fecha-dia", false, false, funcionBusquedaCambio);
    establecerCalendario("bus-fecha-mes-inicio", false, false, funcionBusquedaCambio, true);
    establecerCalendario("bus-fecha-mes-fin", false, false, funcionBusquedaCambio, true);
    establecerCalendario("bus-fecha-anho-inicio", false, false, funcionBusquedaCambio, false, true);
    establecerCalendario("bus-fecha-anho-fin", false, false, funcionBusquedaCambio, false, true);
    establecerCalendario("bus-fecha-inicio", false, false, funcionBusquedaCambio);
    establecerCalendario("bus-fecha-fin", false, false, funcionBusquedaCambio);
    $("#bus-estado-clase, #bus-estado-pago, #bus-tipo-fecha, #bus-tipo-pago").change(funcionBusquedaCambio);
    $("#bus-tipo-fecha").change(function () {
      $('[id*="sec-bus-fecha-"]').hide();
      $("#sec-bus-fecha-" + $(this).val()).show();
    });
    $("#bus-tipo-fecha").trigger("change");
  }
}

function obtenerDatosFiltrosBusqueda() {
  var datos = {};
  datos.estadoClase = ($("#bus-estado-clase").length > 0 ? $("#bus-estado-clase").val() : "");
  datos.estadoPago = ($("#bus-estado-pago").length > 0 ? $("#bus-estado-pago").val() : "");
  datos.tipoBusquedaFecha = ($("#bus-tipo-fecha").length > 0 ? $("#bus-tipo-fecha").val() : "");
  datos.tipoPago = ($("#bus-tipo-pago").length > 0 ? $("#bus-tipo-pago").val() : "");
  datos.fechaDia = $("#bus-fecha-dia").val();
  datos.fechaMesInicio = $("#bus-fecha-mes-inicio").val();
  datos.fechaMesFin = $("#bus-fecha-mes-fin").val();
  datos.fechaAnhoInicio = $("#bus-fecha-anho-inicio").val();
  datos.fechaAnhoFin = $("#bus-fecha-anho-fin").val();
  datos.fechaInicio = $("#bus-fecha-inicio").val();
  datos.fechaFin = $("#bus-fecha-fin").val();
  return datos;
}