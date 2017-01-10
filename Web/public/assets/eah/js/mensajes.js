var mensajesVisibles;
var tMostrarNuevosMensajes = 2000;
$(function () {
  establecerTiempoMensajeVisible();
});
function agregarMensaje(tipo, mensaje, mostrarTemporalmente, idSecContenedor) {
  var plantillaMen =
      '<div class="alert alert-[TIPO] alert-dismissible" role="alert">' +
      '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' +
      '[MENSAJE]' +
      '</div>';
  plantillaMen = plantillaMen.replace("[TIPO]", (tipo === "exitosos" ? "success" : (tipo === "advertencias" ? "warning" : (tipo === "alertas" ? "info" : "danger"))));
  plantillaMen = plantillaMen.replace("[MENSAJE]", mensaje);
  $("div" + (idSecContenedor !== undefined ? idSecContenedor : ".contenedor-alerta")).append(plantillaMen);
  if (mostrarTemporalmente)
    establecerTiempoMensajeVisible(idSecContenedor);
  $("div" + (idSecContenedor !== undefined ? idSecContenedor : ".contenedor-alerta"))[0].focus();
}
function establecerTiempoMensajeVisible(idSecContenedor) {
  idSecContenedor = (idSecContenedor !== undefined ? idSecContenedor : ".contenedor-alerta");
  if ($("div" + idSecContenedor + " div[role = alert]") !== undefined && $("div" + idSecContenedor + " div[role = alert]").length > 0) {

    if (!mensajesVisibles) {
      mensajesVisibles = true;
      setTimeout(function () {
        mensajesVisibles = false;
      }, tMostrarNuevosMensajes);
    }

    $.each($("div" + idSecContenedor + " div[role=alert]"), function (i, e) {
      var timeout;
      e = $(e);
      e.mouseenter(function () {
        clearTimeout(timeout);
        e.stop().animate({opacity: 1});
      });
      e.mouseleave(function () {
        timeout = setTimeout(function () {
          e.fadeOut(3000, function () {
            $(this).remove();
          });
        }, 5000);
      });
      timeout = setTimeout(function () {
        e.fadeOut(3000, function () {
          $(this).remove();
        });
      }, 5000);
    });
  }
}