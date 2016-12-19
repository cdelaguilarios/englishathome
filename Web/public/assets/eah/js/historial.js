window.addEventListener("load", verificarJqueryHistorial, false);
function verificarJqueryHistorial() {
  ((window.jQuery && jQuery.ui) ? cargarHistorial() : window.setTimeout(verificarJqueryHistorial, 100));
}

var ultimaFechaCargada = "";
function  cargarHistorial() {
  urlCargarHistorial = (typeof (urlCargarHistorial) === "undefined" ? "" : urlCargarHistorial);
  urlImagenesHistorial = (typeof (urlImagenesHistorial) === "undefined" ? "" : urlImagenesHistorial);
  meses = (typeof (meses) === "undefined" ? "" : meses);

  var numeroCarga = $("input[name='numeroCarga']").val();

  if (urlCargarHistorial !== "" && urlImagenesHistorial !== "" && meses !== "" && !isNaN(parseInt(numeroCarga))) {
    $("#sec-historial").find("#sec-cierre-historial").hide("slow");
    $("#sec-historial").find("#sec-cierre-historial").remove();
    $("#sec-boton-carga-mas-historial").hide();
    $("#sec-historial").append('<li id="sec-cargando-historial">' +
        '<div class="box cargando">' +
        '<div class="overlay">' +
        '<i class="fa fa-refresh fa-spin"></i>' +
        '</div>' +
        '</div>' +
        '</li>');

    llamadaAjax(urlCargarHistorial, "POST", {numeroCarga: numeroCarga}, true,
        function (d) {
          var datosHistorial = d.datos;
          var htmlHistorial = "";
          for (var fecha in datosHistorial) {
            if (!datosHistorial.hasOwnProperty(fecha))
              continue;

            if (ultimaFechaCargada !== fecha) {
              ultimaFechaCargada = fecha;
              var fechaBase = new Date(fecha);
              htmlHistorial += '<li class="time-label">' +
                  '<span class="bg-blue">' +
                  fechaBase.getDate() + ' ' + meses[fechaBase.getMonth() + 1] + ' ' + fechaBase.getFullYear() +
                  '</span>' +
                  '</li>';
            }
            var datHistorial = datosHistorial[fecha];
            for (var i = 0; i < datHistorial.length; i++) {
              htmlHistorial += '<li>' +
                  '<i class="fa ' + datHistorial[i].icono + ' ' + datHistorial[i].claseColorIcono + '"></i>' +
                  '<div class="timeline-item">' +
                  '<span class="time"><i class="fa fa-clock-o"></i> ' + datHistorial[i].fechaNotificacion + '</span>' +
                  '<h3 class="timeline-header">' + datHistorial[i].titulo + '</h3>';
              if (datHistorial[i].mensaje !== "" || datHistorial[i].rutasImagenes !== null) {
                htmlHistorial += '<div class="timeline-body">' + (datHistorial[i].mensaje !== "" ? datHistorial[i].mensaje : "");
                if (datHistorial[i].rutasImagenes !== null) {
                  var rutasImagenes = datHistorial[i].rutasImagenes.split(",");
                  $.each(rutasImagenes, function (e, v) {
                    var rutaImagen = urlImagenesHistorial.replace("/0", "/" + v);
                    htmlHistorial += '<a href="' + rutaImagen + '" target="_blank"><img src="' + rutaImagen + '" class="margin" width="100"></a>';
                  });
                }
                htmlHistorial += '</div>';
              }
              htmlHistorial += '</div></li>';
            }
          }
          htmlHistorial += (htmlHistorial !== "" ? '<li id="sec-cierre-historial"><i class="fa fa-clock-o bg-gray"></i></li>' : '');
          var nuevoDatos = $(htmlHistorial).hide();

          $("#sec-historial").find("#sec-cargando-historial").fadeOut('slow', function () {
            $("input[name='numeroCarga']").val(parseInt(numeroCarga) + 1);
            $("#sec-historial").append(nuevoDatos);
            nuevoDatos.show("normal");
            $("#sec-historial").find("#sec-cargando-historial").remove();
            if (d.mostrarBotonCargar) {
              $("#sec-boton-carga-mas-historial").show("slow");
            } else {
              $("#sec-boton-carga-mas-historial").remove();
            }
          });
        },
        function (data) {
        },
        function (dataError) {
        }
    );
  }
}