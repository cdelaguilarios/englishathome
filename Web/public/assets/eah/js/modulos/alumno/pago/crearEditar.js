var crearEditarPagoAlumno = {};
crearEditarPagoAlumno = (function ()/* - */ {
  window.addEventListener("load", esperarCargaJquery, false);
  function esperarCargaJquery()/* - */ {
    ((window.jQuery && jQuery.ui) ? cargar() : window.setTimeout(esperarCargaJquery, 100));
  }

  //Privado
  function obtenerReferenciaFormulario() {
    return $("#formulario-pago");
  }
  function cargar() {
    var formulario = obtenerReferenciaFormulario();
    formularioPagoAlumno.cargar(formulario);

    $("#btn-cancelar-pago").click(function () {
      listaPagosAlumno.mostrar();
    });
  }
  function obtenerDatosPago(idPago, funcionRetorno) {
    urlDatosPago = (typeof (urlDatosPago) === "undefined" ? "" : urlDatosPago);
    if (urlDatosPago !== "") {
      $.blockUI({message: "<h4>Cargando...</h4>", baseZ: 2000});
      util.llamadaAjax(urlDatosPago.replace("/0", "/" + idPago), "POST", {}, true,
              function (d) {
                if (funcionRetorno !== undefined) {
                  funcionRetorno(d);
                }
                $("body").unblock();
              },
              function (d) {
              },
              function (de) {
                $('body').unblock({
                  onUnblock: function () {
                    mensajes.agregar("errores", "Ocurrió un problema durante la carga de datos del pago seleccionado. Por favor inténtelo nuevamente.", true, "#sec-pago-mensajes");
                  }
                });
              }
      );
    }
  }

  //Público
  function crear() {
    var formulario = obtenerReferenciaFormulario();

    $("#sec-pago-crear-editar").find(".box-title").html("Nuevo pago");
    $("div[id^=sec-pago-]").hide();
    formularioPagoAlumno.establecerDatos(formulario);
    $("#sec-pago-crear-editar").show();
  }
  function editar(idPago) {
    obtenerDatosPago(idPago, function (d) {
      var saldoFavorTotal = formularioPagoAlumno.obtenerSaldoFavor();
      var saldoFavorPago = (d.saldoFavor !== null ? parseFloat(d.saldoFavor + "") : 0);

      var formulario = obtenerReferenciaFormulario();
      formularioPagoAlumno.establecerSaldoFavor(saldoFavorTotal - saldoFavorPago);

      $("#sec-pago-crear-editar").find(".box-title").html("Editar pago");
      $("div[id^=sec-pago-]").hide();
      formularioPagoAlumno.establecerDatos(formulario, d);
      $("#sec-pago-crear-editar").show();
    });
  }

  return {
    crear: crear,
    editar: editar
  };
}());