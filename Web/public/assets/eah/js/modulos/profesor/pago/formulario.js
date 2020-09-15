var formularioPagoGeneralProfesor = {};
formularioPagoGeneralProfesor = (function () {
  //Privado
  function limpiarCampos(formulario) {
    $(formulario).find(":input, select").each(function (i, e) {
      if (e.name !== "fecha" && e.name !== "motivo" && e.name !== "_token" && e.type !== "hidden") {
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
        }
      },
      submitHandler: function (f) {
        var idPago = $(formulario).find("input[name='idPago']").val();
        if (confirm("¿Está seguro que desea " + (idPago !== "" ? "actualizar" : "registrar") +
                " los datos de este pago?")) {
          $.blockUI({message: "<h4>Guardando cambios...</h4>"});

          var datos = utilFormularios.procesarDatos(f);
          util.llamadaAjax($(f).attr("action"), "POST", datos, true,
                  function (d) {
                    $("body").unblock();
                  },
                  function (d) {
                    listaPagosProfesor.mostrar();
                    listaPagosProfesor.reCargar();
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
  }
  function establecerDatos(formulario, datos) {
    limpiarCampos(formulario);

    $(formulario).find("input[name='idPago']").val("");
    urlArchivos = (typeof (urlArchivos) === "undefined" ? "" : urlArchivos);
    if (datos && urlArchivos !== "") {
      $(formulario).find("input[name='idPago']").val(datos.id);
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
    }
  }

  return {
    cargar: cargar,
    establecerDatos: establecerDatos
  };
}());