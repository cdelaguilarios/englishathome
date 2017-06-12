function cargarFiltrosBusqueda(funcionBusquedaCambio) {
  if (funcionBusquedaCambio !== undefined) {
    establecerCalendario("bus-fecha-dia", false, false, false, funcionBusquedaCambio);
    establecerCalendario("bus-fecha-mes", false, false, false, funcionBusquedaCambio, true);
    establecerCalendario("bus-fecha-anho", false, false, false, funcionBusquedaCambio, false, true);
    establecerCalendario("bus-fecha-dia-inicio", false, false, false, funcionBusquedaCambio);
    establecerCalendario("bus-fecha-dia-fin", false, false, false, funcionBusquedaCambio);
    establecerCalendario("bus-fecha-mes-inicio", false, false, false, funcionBusquedaCambio, true);
    establecerCalendario("bus-fecha-mes-fin", false, false, false, funcionBusquedaCambio, true);
    establecerCalendario("bus-fecha-anho-inicio", false, false, false, funcionBusquedaCambio, false, true);
    establecerCalendario("bus-fecha-anho-fin", false, false, false, funcionBusquedaCambio, false, true);
    $("#bus-estado-clase, #bus-estado-pago, #bus-tipo-pago, #bus-tipo-fecha").change(funcionBusquedaCambio);
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
  datos.tipoPago = ($("#bus-tipo-pago").length > 0 ? $("#bus-tipo-pago").val() : "");
  
  datos.tipoBusquedaFecha = ($("#bus-tipo-fecha").length > 0 ? $("#bus-tipo-fecha").val() : "");
  datos.fechaDia = $("#bus-fecha-dia").val();
  datos.fechaMes = $("#bus-fecha-mes").val();
  datos.fechaAnho = $("#bus-fecha-anho").val();
  datos.fechaDiaInicio = $("#bus-fecha-dia-inicio").val();
  datos.fechaDiaFin = $("#bus-fecha-dia-fin").val();
  datos.fechaMesInicio = $("#bus-fecha-mes-inicio").val();
  datos.fechaMesFin = $("#bus-fecha-mes-fin").val();
  datos.fechaAnhoInicio = $("#bus-fecha-anho-inicio").val();
  datos.fechaAnhoFin = $("#bus-fecha-anho-fin").val();
  return datos;
}