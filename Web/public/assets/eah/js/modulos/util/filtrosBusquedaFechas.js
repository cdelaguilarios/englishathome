if (typeof (filtrosBusquedaFechas) === "undefined") {
  var filtrosBusquedaFechas = {};
  filtrosBusquedaFechas = (function ()/* - */ {
    var auxPrimerCambio = [];
    function cargar(idSeccion, funcionBusquedaCambio)/* - */ {
      if (funcionBusquedaCambio !== undefined) {
        var funcionCambioSel = funcionBusquedaCambio;
        var funcionCambio = function () {
          if (auxPrimerCambio[idSeccion] === undefined) {
            auxPrimerCambio[idSeccion] = true;
          } else {
            funcionCambioSel();
          }
        };

        var seccionBusqueda = $("#sec-filtro-busqueda-fechas-" + idSeccion);
        utilFechasHorarios.establecerCalendario($(seccionBusqueda).find("input[name='fechaDia']"), false, false, false, funcionCambio);
        utilFechasHorarios.establecerCalendario($(seccionBusqueda).find("input[name='fechaMes']"), false, false, false, funcionCambio, true);
        utilFechasHorarios.establecerCalendario($(seccionBusqueda).find("input[name='fechaAnio']"), false, false, false, funcionCambio, false, true);
        utilFechasHorarios.establecerCalendario($(seccionBusqueda).find("input[name='fechaDiaInicio']"), false, false, false, funcionCambio);
        utilFechasHorarios.establecerCalendario($(seccionBusqueda).find("input[name='fechaDiaFin']"), false, false, false, funcionCambio);
        utilFechasHorarios.establecerCalendario($(seccionBusqueda).find("input[name='fechaMesInicio']"), false, false, false, funcionCambio, true);
        utilFechasHorarios.establecerCalendario($(seccionBusqueda).find("input[name='fechaMesFin']"), false, false, false, funcionCambio, true);
        utilFechasHorarios.establecerCalendario($(seccionBusqueda).find("input[name='fechaAnioInicio']"), false, false, false, funcionCambio, false, true);
        utilFechasHorarios.establecerCalendario($(seccionBusqueda).find("input[name='fechaAnioFin']"), false, false, false, funcionCambio, false, true);

        $(seccionBusqueda).find("select[name='tipoBusquedaFecha']").change(funcionCambio);
        $(seccionBusqueda).find("select[name='tipoBusquedaFecha']").change(function () {
          $(seccionBusqueda).find('[id*="sec-filtro-busqueda-fechas-"]').hide();
          $(seccionBusqueda).find("#sec-filtro-busqueda-fechas-" + $(this).val()).show();
        });
        $(seccionBusqueda).find("select[name='tipoBusquedaFecha']").trigger("change");
      }
    }

    function actualizarTitulo(idSeccion, titulo) {
      $("#sec-filtro-busqueda-fechas-" + idSeccion).find("#lbl-filtro-busqueda-fechas-titulo").html(titulo);
    }

    function obtenerDatos(idSeccion)/* - */ {
      var seccionBusqueda = $("#sec-filtro-busqueda-fechas-" + idSeccion);

      var datos = {};
      datos.tipoBusquedaFecha = $(seccionBusqueda).find("select[name='tipoBusquedaFecha']").val();
      datos.fechaDia = $(seccionBusqueda).find("input[name='fechaDia']").val();
      datos.fechaMes = $(seccionBusqueda).find("input[name='fechaMes']").val();
      datos.fechaAnio = $(seccionBusqueda).find("input[name='fechaAnio']").val();
      datos.fechaDiaInicio = $(seccionBusqueda).find("input[name='fechaDiaInicio']").val();
      datos.fechaDiaFin = $(seccionBusqueda).find("input[name='fechaDiaFin']").val();
      datos.fechaMesInicio = $(seccionBusqueda).find("input[name='fechaMesInicio']").val();
      datos.fechaMesFin = $(seccionBusqueda).find("input[name='fechaMesFin']").val();
      datos.fechaAnioInicio = $(seccionBusqueda).find("input[name='fechaAnioInicio']").val();
      datos.fechaAnioFin = $(seccionBusqueda).find("input[name='fechaAnioFin']").val();
      return datos;
    }

    return {
      cargar: cargar,
      actualizarTitulo: actualizarTitulo,
      obtenerDatos: obtenerDatos
    };
  }());
}