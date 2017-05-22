window.addEventListener("load", verificarJqueryNotificaciones, false);
function verificarJqueryNotificaciones() {
  ((window.jQuery && jQuery.ui) ? cargarNotificaciones() : window.setTimeout(verificarJqueryNotificaciones, 100));
}

var idsNuevasNotificaciones = [];
var revisionRealizada = false;
function cargarNotificaciones() {
  urlListaNotificaciones = (typeof (urlListaNotificaciones) === "undefined" ? "" : urlListaNotificaciones);
  urlNuevasNotificaciones = (typeof (urlNuevasNotificaciones) === "undefined" ? "" : urlNuevasNotificaciones);
  if (urlListaNotificaciones !== "" && urlNuevasNotificaciones !== "") {
    llamadaAjax(urlNuevasNotificaciones, "POST", {}, true,
        function (d) {
          var datosHistorial = d.datos;
          var htmlListaNuevasNotificaciones = "";
          for (var fecha in datosHistorial) {
            if (!datosHistorial.hasOwnProperty(fecha))
              continue;
            var datHistorial = datosHistorial[fecha];
            for (var i = 0; i < datHistorial.length; i++) {
              htmlListaNuevasNotificaciones += '<li><a href="' + urlListaNotificaciones + '?id=' + datHistorial[i].id + '"><i class="fa ' + datHistorial[i].icono + ' ' + datHistorial[i].claseTextoColorIcono + '"></i> ' + datHistorial[i].titulo + '</a></li>';
              idsNuevasNotificaciones.push(datHistorial[i].id);
            }
          }
          if (idsNuevasNotificaciones.length > 0) {
            $("#sec-total-nuevas-notificaciones").html(idsNuevasNotificaciones.length);
            $("#sec-titulo-nuevas-notificaciones").html("Tienes " + idsNuevasNotificaciones.length + (idsNuevasNotificaciones.length > 1 ? " nuevas notificaciones" : " nueva notificación"));
            $("#sec-lista-nuevas-notificaciones").html(htmlListaNuevasNotificaciones);
          } else {
            $("#btn-ver-nuevas-notificaciones").attr("href", urlListaNotificaciones).removeAttr("data-toggle");
            $("#sec-total-nuevas-notificaciones").remove();
            $("#sec-titulo-nuevas-notificaciones").remove();
          }
        }
    );
  }

  $("#btn-ver-nuevas-notificaciones").click(function () {
    setTimeout(function () {
      urlRevisarNuevasNotificaciones = (typeof (urlRevisarNuevasNotificaciones) === "undefined" ? "" : urlRevisarNuevasNotificaciones);
      if (urlRevisarNuevasNotificaciones !== "" && idsNuevasNotificaciones.length > 0 && !revisionRealizada) {
        llamadaAjax(urlRevisarNuevasNotificaciones, "POST", {idsNuevasNotificaciones: idsNuevasNotificaciones}, true, undefined, function () {
          revisionRealizada = true;
        });
      }
    }, 1500);
  });
}

