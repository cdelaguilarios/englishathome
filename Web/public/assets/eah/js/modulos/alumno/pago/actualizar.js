var actualizarPagoAlumno = {};
actualizarPagoAlumno = (function () {
  //Privado
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
  var idSeccion = "";
  function establecerIdSeccion(id) {
    idSeccion = id;
  }
  function mostrar(idPago) {
    $("div[id^=sec-pago-]").hide();
    obtenerDatosPago(idPago, function (d) {
      urlArchivos = (typeof (urlArchivos) === "undefined" ? "" : urlArchivos);
      if (urlArchivos !== "") {
        var saldoFavorTotal = formularioPagoAlumno.obtenerSaldoFavor();
        var saldoFavorPago = (d.saldoFavor !== null ? parseFloat(d.saldoFavor + "") : 0);

        formularioPagoAlumno.establecerSaldoFavor(saldoFavorTotal - saldoFavorPago);
        formularioPagoAlumno.cargar(idSeccion);

        $("#motivo-pago-" + idSeccion).val(d.motivo);
        $("#cuenta-pago-" + idSeccion).val(d.cuenta);

        var datFecha = utilFechasHorarios.formatoFecha(d.fecha).split("/");
        $("#fecha-pago-" + idSeccion).datepicker("setDate", (new Date(datFecha[1] + "/" + datFecha[0] + "/" + datFecha[2])));

        $("#estado-pago-" + idSeccion).val(d.estado);
        $("#descripcion-pago-" + idSeccion).val(d.descripcion);

        if (d.imagenesComprobante !== null && d.imagenesComprobante !== "") {
          var datImagen = d.imagenesComprobante.split(",");
          if (datImagen.length > 0) {
            if (datImagen[0] !== "") {
              var rutaImagen = urlArchivos.replace("/0", "/" + datImagen[0]);
              $("#sec-pago-imagen-comprobante-actual-" + idSeccion).find("a").attr("href", rutaImagen);
              $("#sec-pago-imagen-comprobante-actual-" + idSeccion).find("img").attr("src", rutaImagen);
              $("#sec-pago-imagen-comprobante-actual-" + idSeccion).show();
            }
          }
        }

        $("#monto-pago-" + idSeccion).val(util.redondear(d.monto, 2));

        if (d.motivo === motivoPagoClases) {
          $("#costo-x-hora-clase-pago-" + idSeccion).val(util.redondear(d.costoXHoraClase, 2));
          $("#periodo-clases-pago-" + idSeccion).val(d.periodoClases);

          if (d.idProfesorClases !== null) {
            formularioPagoAlumno.establecerDocente(idSeccion, d.datosProfesor);
            $("#pago-x-hora-docente-" + idSeccion).val(util.redondear(d.pagoXHoraProfesor, 2));
          }
        }

        $("#formulario-pago-" + idSeccion).find("input[name='idPago']").val(d.id);
        $("#motivo-pago-" + idSeccion).change();

        $("#sec-pago-actualizar").show();
      }
    });
  }

  return {
    establecerIdSeccion: establecerIdSeccion,
    mostrar: mostrar
  };
}());