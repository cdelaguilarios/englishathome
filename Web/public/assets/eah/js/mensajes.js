var mensajes = {};
mensajes = (function ()/* - */ {
  var mensajesVisibles;
  var tiempoMostrarNuevosMensajes = 2000;

  $(function ()/* - */ {
    establecerTiempoMensajeVisible();
  });
  function establecerTiempoMensajeVisible(idSecContenedor)/* - */ {
    idSecContenedor = (idSecContenedor !== undefined ? idSecContenedor : ".contenedor-alerta");
    if ($("div" + idSecContenedor + " div[role = alert]") !== undefined && $("div" + idSecContenedor + " div[role = alert]").length > 0) {
      if (!mensajesVisibles) {
        mensajesVisibles = true;
        setTimeout(function () {
          mensajesVisibles = false;
        }, tiempoMostrarNuevosMensajes);
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

  function agregar(tipo, mensaje, mostrarTemporalmente, idSecContenedor, refrescar)/* - */ {
    var plantillaMen =
            '<div class="alert alert-[TIPO] alert-dismissible" role="alert">' +
            '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' +
            '[MENSAJE]' +
            '</div>';
    plantillaMen = plantillaMen.replace("[TIPO]", (tipo === "exitosos" ? "success" : (tipo === "advertencias" ? "warning" : (tipo === "alertas" ? "info" : "danger"))));
    plantillaMen = plantillaMen.replace("[MENSAJE]", mensaje);

    $("div" + (idSecContenedor !== undefined ? idSecContenedor : ".contenedor-alerta")).show();
    if (refrescar) {
      $("div" + (idSecContenedor !== undefined ? idSecContenedor : ".contenedor-alerta")).html("");
    }
    $("div" + (idSecContenedor !== undefined ? idSecContenedor : ".contenedor-alerta")).append(plantillaMen);

    if (mostrarTemporalmente) {
      establecerTiempoMensajeVisible(idSecContenedor);
    }
    $("div" + (idSecContenedor !== undefined ? idSecContenedor : ".contenedor-alerta"))[0].focus();
  }

  return {
    agregar: agregar
  };
}());