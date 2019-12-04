var filtrosBusquedaFechas = {};
filtrosBusquedaFechas = (function ()/* - */ {
  var primerCambio = true;
  function cargar(funcionBusquedaCambio)/* - */ {
    if (funcionBusquedaCambio !== undefined) {
      var funcionCambioSel = funcionBusquedaCambio;
      var funcionCambio = function () {
        if (primerCambio) {
          primerCambio = false;
        } else {
          funcionCambioSel();
        }
      };

      utilFechasHorarios.establecerCalendario($("#filtro-busqueda-fechas-dia"), false, false, false, funcionCambio);
      utilFechasHorarios.establecerCalendario($("#filtro-busqueda-fechas-mes"), false, false, false, funcionCambio, true);
      utilFechasHorarios.establecerCalendario($("#filtro-busqueda-fechas-anio"), false, false, false, funcionCambio, false, true);
      utilFechasHorarios.establecerCalendario($("#filtro-busqueda-fechas-dia-inicio"), false, false, false, funcionCambio);
      utilFechasHorarios.establecerCalendario($("#filtro-busqueda-fechas-dia-fin"), false, false, false, funcionCambio);
      utilFechasHorarios.establecerCalendario($("#filtro-busqueda-fechas-mes-inicio"), false, false, false, funcionCambio, true);
      utilFechasHorarios.establecerCalendario($("#filtro-busqueda-fechas-mes-fin"), false, false, false, funcionCambio, true);
      utilFechasHorarios.establecerCalendario($("#filtro-busqueda-fechas-anio-inicio"), false, false, false, funcionCambio, false, true);
      utilFechasHorarios.establecerCalendario($("#filtro-busqueda-fechas-anio-fin"), false, false, false, funcionCambio, false, true);

      $("#filtro-busqueda-fechas-tipo").change(funcionCambio);
      $("#filtro-busqueda-fechas-tipo").change(function () {
        $('[id*="sec-filtro-busqueda-fechas-"]').hide();
        $("#sec-filtro-busqueda-fechas-" + $(this).val()).show();
      });
      $("#filtro-busqueda-fechas-tipo").trigger("change");
    }
  }
  
  function actualizarTitulo(titulo){
    $("#lbl-filtro-busqueda-fechas-tipo").html(titulo);
  }

  function obtenerDatos()/* - */ {
    var datos = {};
    datos.tipoBusquedaFecha = ($("#filtro-busqueda-fechas-tipo").length > 0 ? $("#filtro-busqueda-fechas-tipo").val() : "");

    datos.fechaDia = $("#filtro-busqueda-fechas-dia").val();
    datos.fechaMes = $("#filtro-busqueda-fechas-mes").val();
    datos.fechaAnio = $("#filtro-busqueda-fechas-anio").val();
    datos.fechaDiaInicio = $("#filtro-busqueda-fechas-dia-inicio").val();
    datos.fechaDiaFin = $("#filtro-busqueda-fechas-dia-fin").val();
    datos.fechaMesInicio = $("#filtro-busqueda-fechas-mes-inicio").val();
    datos.fechaMesFin = $("#filtro-busqueda-fechas-mes-fin").val();
    datos.fechaAnioInicio = $("#filtro-busqueda-fechas-anio-inicio").val();
    datos.fechaAnioFin = $("#filtro-busqueda-fechas-anio-fin").val();
    return datos;
  }

  return {
    cargar: cargar,
    actualizarTitulo: actualizarTitulo,
    obtenerDatos: obtenerDatos
  };
}());