var formularioPagoAlumno = {};
formularioPagoAlumno = (function () {
  //Privado
  function limpiarCampos(formulario) {
    $(formulario).find(":input, select").each(function (i, e) {
      if (e.name !== "estado" && e.name !== "fecha" && e.name !== "costoXHoraClase" && e.name !== "periodoClases" && e.name !== "_token" && e.type !== "hidden") {
        if ($(e).is("select")) {
          $(e).prop("selectedIndex", 0);
        } else if ($(e).is(":checkbox")) {
          $(e).attr("checked", false);
          $(e).closest("label").removeClass("checked");
        } else {
          e.value = "";
        }
      }
    });
    archivosAdjuntos.limpiarCampos(formulario, "ImagenesComprobantes");
    $(formulario).find("input[name='fecha']").datepicker("setDate", "today");
    $("form .help-block-error").remove();
  }

  //Público
  var saldoFavorTotal = 0;
  function cargar(formulario) {
    $(formulario).validate({
      ignore: ":hidden",
      rules: {
        fecha: {
          required: true,
          validarFecha: true
        },
        imagenComprobante: {
          validarImagen: true
        },
        monto: {
          required: true,
          validarDecimal: true
        },
        costoXHoraClase: {
          required: true,
          validarDecimal: true
        },
        periodoClases: {
          required: true,
          validarEntero: true
        },
        pagoXHoraProfesor: {
          required: true,
          validarDecimal: true
        }
      },
      submitHandler: function (f) {
        var idPago = $(formulario).find("input[name='idPago']").val();
        if (confirm("¿Está seguro que desea " + (idPago !== "" ? "actualizar" : "registrar") + " los datos de este pago?")) {
          $.blockUI({message: "<h4>Guardando cambios...</h4>"});

          var datos = utilFormularios.procesarDatos(f);
          motivoPagoXClases = (typeof (motivoPagoXClases) === "undefined" ? "" : motivoPagoXClases);

          if (datos["motivo"] === motivoPagoXClases) {
            $.blockUI({message: "<h4>" + (idPago !== "" ? "Guardando datos..." : "Registrando datos...") + "</h4>"});
            f.submit();
          } else {
            util.llamadaAjax($(f).attr("action"), "POST", datos, true,
                    function (d) {
                      $("body").unblock();
                    },
                    function (d) {
                      listaPagosAlumno.mostrar();
                      listaPagosAlumno.reCargar();
                    },
                    function (de) {
                      var rj = de.responseJSON;
                      $("body").unblock({
                        onUnblock: function () {
                          if (rj !== undefined && rj.mensaje !== undefined) {
                            mensajes.agregar("errores", rj.mensaje, true, "#sec-pago-mensajes");
                          } else if (rj !== undefined && rj[Object.keys(rj)[0]] !== undefined) {
                            mensajes.agregar("errores", rj[Object.keys(rj)[0]][0], true, "#sec-pago-mensajes");
                          }
                        }
                      });
                    }
            );
          }

        }
      },
      highlight: function () {
      },
      unhighlight: function () {
      },
      errorElement: "div",
      errorClass: "help-block-error",
      errorPlacement: function (error, element) {
        if (element.closest("div[class*=col-sm-]").length > 0) {
          element.closest("div[class*=col-sm-]").append(error);
        } else if (element.parent(".input-group").length) {
          error.insertAfter(element.parent());
        } else {
          error.insertAfter(element);
        }
      },
      onfocusout: false,
      onkeyup: false,
      onclick: false
    });
    utilFechasHorarios.establecerCalendario($(formulario).find("input[name='fecha']"), false, false, false);
    $(formulario).find("select[name='motivo']").change(function () {
      if ($(this)[0].selectedIndex !== 0) {
        $(formulario).find("#sec-pago-datos-clases").hide();
      } else {
        $(formulario).find("#sec-pago-datos-clases").show();
      }
    });

    $(formulario).find("input[name='monto']").change(function () {
      if (!$(this).valid()) {
        $(formulario).find("#pago-usar-saldo-favor").attr("checked", false);
        $(formulario).find("#pago-usar-saldo-favor").closest("label").removeClass("checked");
      }
    });
    $(formulario).find("#pago-usar-saldo-favor").click(function (e) {
      if ($(formulario).find("input[name='monto']").valid() && saldoFavorTotal > 0) {
        var seleccionado = $(formulario).find("#pago-usar-saldo-favor").closest("label").hasClass("checked");

        $(formulario).find("input[name='monto']").val(util.redondear(parseFloat($(formulario).find("input[name='monto']").val()) + saldoFavorTotal * (seleccionado ? -1 : 1), 2));

        $(this).attr("checked", !seleccionado);
        if (seleccionado) {
          $(formulario).find("#pago-usar-saldo-favor").closest("label").removeClass("checked");
        } else {
          $(formulario).find("#pago-usar-saldo-favor").closest("label").addClass("checked");
        }
      } else {
        e.stopPropagation();
        return false;
      }
    });
  }
  function establecerDatos(formulario, datos) {
    limpiarCampos(formulario);

    $(formulario).find("input[name='idPago']").val("");
    if (saldoFavorTotal > 0) {
      $(formulario).find("#lbl-pago-usar-saldo-favor").text("Utilizar saldo a favor total (S/. " + util.redondear(saldoFavorTotal, 2) + ")");
      $(formulario).find("#sec-pago-saldo-favor").show();
    }
    $(formulario).find("#sec-pago-datos-clases").show();

    urlArchivos = (typeof (urlArchivos) === "undefined" ? "" : urlArchivos);
    motivoPagoXClases = (typeof (motivoPagoXClases) === "undefined" ? "" : motivoPagoXClases);
    if (datos && urlArchivos !== "") {
      $(formulario).find("input[name='idPago']").val(datos.id);
      $(formulario).find("select[name='motivo']").val(datos.motivo);
      $(formulario).find("select[name='cuenta']").val(datos.cuenta);
      $(formulario).find("select[name='estado']").val(datos.estado);
      $(formulario).find("input[name='descripcion']").val(datos.descripcion);
      $(formulario).find("input[name='monto']").val(util.redondear(datos.monto, 2));

      var datFecha = utilFechasHorarios.formatoFecha(datos.fecha).split("/");
      $(formulario).find("input[name='fecha']").datepicker("setDate", (new Date(datFecha[1] + "/" + datFecha[0] + "/" + datFecha[2])));

      if (datos.imagenesComprobante !== null && datos.imagenesComprobante !== "") {
        var imagenes = datos.imagenesComprobante.split(",");
        for (var i = 0; i < imagenes.length; i++) {
          if (imagenes[i].trim() !== "") {
            var datosImagen = imagenes[i].split(":");
            archivosAdjuntos.agregar(formulario, "imagenes-comprobantes", datosImagen[0], datosImagen[datosImagen.length === 2 ? 1 : 0], true);
          }
        }
      }

      if (datos.motivo === motivoPagoXClases) {
        $(formulario).find("input[name='costoXHoraClase']").val(util.redondear(datos.costoXHoraClase, 2));
        $(formulario).find("input[name='periodoClases']").val(datos.periodoClases);
        $(formulario).find("input[name='pagoXHoraProfesor']").val(util.redondear(datos.pagoXHoraProfesor, 2));
      }

      $(formulario).find("select[name='motivo']").change();
    }
  }

  function establecerSaldoFavor(saldoFavor) {
    saldoFavorTotal = saldoFavor;
  }
  function obtenerSaldoFavor() {
    return saldoFavorTotal;
  }

  return {
    cargar: cargar,
    establecerDatos: establecerDatos,
    establecerSaldoFavor: establecerSaldoFavor,
    obtenerSaldoFavor: obtenerSaldoFavor
  };
}());