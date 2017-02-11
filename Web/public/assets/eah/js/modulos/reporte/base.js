var datosMontosReporte = false;
var nombreEntidadReporte = "";
var detalleSingularReporte = "";
var detallePluralReporte = "";
function cargarReporte(datosMontos, nombreEntidad, detalleSingular, detallePlural) {
  datosMontosReporte = datosMontos;
  nombreEntidadReporte = nombreEntidad;
  detalleSingularReporte = detalleSingular;
  detallePluralReporte = detallePlural;
  cargarDatosGrafico();
  establecerCalendario("bus-fecha-mes-inicio", false, false, cargarDatosGrafico, true);
  establecerCalendario("bus-fecha-mes-fin", false, false, cargarDatosGrafico, true);
  establecerCalendario("bus-fecha-anho-inicio", false, false, cargarDatosGrafico, false, true);
  establecerCalendario("bus-fecha-anho-fin", false, false, cargarDatosGrafico, false, true);
  establecerCalendario("bus-fecha-inicio", false, false, cargarDatosGrafico);
  establecerCalendario("bus-fecha-fin", false, false, cargarDatosGrafico);
  $("#bus-estado, #bus-tipo-fecha, #bus-tipo-pago").change(cargarDatosGrafico);
  $("#bus-tipo-fecha").change(function () {
    $('[id*="sec-bus-fecha-"]').hide();
    $("#sec-bus-fecha-" + $(this).val()).show();
  });
  $("#bus-tipo-fecha").trigger("change");
}

function cargarDatosGrafico() {
  urlListar = (typeof (urlListar) === "undefined" ? "" : urlListar);
  estados = (typeof (estados) === "undefined" ? "" : estados);
  meses = (typeof (meses) === "undefined" ? "" : meses);
  if (urlListar !== "" && estados !== "" && meses !== "") {
    var datos = {};
    datos.estado = $("#bus-estado").val();
    datos.tipoBusquedaFecha = $("#bus-tipo-fecha").val();
    datos.tipoPago = $("#bus-tipo-pago").val();
    datos.fechaMesInicio = $("#bus-fecha-mes-inicio").val();
    datos.fechaMesFin = $("#bus-fecha-mes-fin").val();
    datos.fechaAnhoInicio = $("#bus-fecha-anho-inicio").val();
    datos.fechaAnhoFin = $("#bus-fecha-anho-fin").val();
    datos.fechaInicio = $("#bus-fecha-inicio").val();
    datos.fechaFin = $("#bus-fecha-fin").val();

    $('#sec-grafico').block({message: '<h4>Cargando...</h4>'});
    llamadaAjax(urlListar, "POST", datos, true, function (d) {
      var datosBar = [];
      if (d.length > 0) {
        var datosCabecera = [(d[0].mes !== undefined ? "Mes" : (d[0].anho !== undefined ? "Año" : "Día"))];
        var total = 0;
        var colores = [];
        $.each(estados, function (ind, ele) {
          datosCabecera.push(ele[0]);
          colores.push(ele[2]);
        });
        datosBar.push(datosCabecera);

        for (var i = 0; i < d.length; i++) {
          var grupo = "" + (d[i].mes !== undefined ? meses[d[i].mes] : (d[i].anho !== undefined ? d[i].anho : formatoFecha((d[i].fechaInicio !== undefined ? d[i].fechaInicio : d[i].fechaRegistro))));
          var itemSel = null;
          for (var j = 0; j < datosBar.length; j++) {
            var item = datosBar[j];
            if (item[0] === grupo) {
              itemSel = item;
            }
          }
          auxNumEstado = 0;
          $.each(estados, function (ind, ele) {
            auxNumEstado++;
            if (ind === d[i].estado) {
              return false;
            }
          });
          if (auxNumEstado !== 0) {
            if (itemSel === null) {
              var mod = [grupo];
              for (var k = 1; k < datosBar[0].length; k++) {
                mod.push(0);
              }
              itemSel = mod;
              datosBar.push(itemSel);
            }
            itemSel[auxNumEstado] += parseFloat(d[i].total);
            total += parseFloat(d[i].total);
          }
        }

        var datosGrafico = google.visualization.arrayToDataTable(datosBar);
        var opcionesGrafico = {
          chart: {
            title: "Reporte de " + nombreEntidadReporte,
            subtitle: (datosMontosReporte ? ("S/. " + redondear(total, 2)) : total) + " " + (total === 1 ? detalleSingularReporte : detallePluralReporte)
          },
          bars: "vertical",
          vAxis: {format: (datosMontosReporte ? "decimal" : "")},
          height: 400,
          colors: colores
        };
        var grafico = new google.charts.Bar(document.getElementById("grafico"));
        grafico.draw(datosGrafico, google.charts.Bar.convertOptions(opcionesGrafico));
      } else {
        $("#grafico").html("Sin resultados");
      }
    }, function () {
      setTimeout(function () {
        $('#sec-grafico').unblock("slow");
      }, 100);
    }
    );
  }
}