var datosMontosReporte = false;
var nombreEntidadReporte = "";
var detalleSingularReporte = "";
var detallePluralReporte = "";
function cargarGrafico(datosMontos, nombreEntidad, detalleSingular, detallePlural) {
  datosMontosReporte = datosMontos;
  nombreEntidadReporte = nombreEntidad;
  detalleSingularReporte = detalleSingular;
  detallePluralReporte = detallePlural;
}

var auxTimeoutCargarDatosGrafico;
function cargarDatosGrafico(idsSel, nuevaCarga) {
  if (!$("#sec-grafico").is(":visible")) {
    if (nuevaCarga) {
      clearTimeout(auxTimeoutCargarDatosGrafico);
    }
    auxTimeoutCargarDatosGrafico = setTimeout(function () {
      cargarDatosGrafico(idsSel);
    }, 100);
  } else {
    urlListarGrafico = (typeof (urlListarGrafico) === "undefined" ? "" : urlListarGrafico);
    estados = (typeof (estados) === "undefined" ? "" : estados);
    meses = (typeof (meses) === "undefined" ? "" : meses);
    if (urlListarGrafico !== "" && estados !== "" && meses !== "") {
      var datos = obtenerDatosFiltrosBusqueda();
      datos["ids"] = idsSel;
      $('#sec-grafico').block({message: '<h4>Cargando...</h4>'});
      llamadaAjax(urlListarGrafico, "POST", datos, true, function (d) {
        var datosBar = [];
        if (d.length > 0) {
          var datosCabecera = [(d[0].mes !== undefined ? "Mes" : (d[0].anho !== undefined ? "Año" : "Día"))];
          var total = 0;
          var colores = [];
          $.each(estados, function (i, e) {
            datosCabecera.push(e[0]);
            colores.push(e[2]);
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
}