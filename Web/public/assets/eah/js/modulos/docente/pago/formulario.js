var formularioPagoDocente = {};
formularioPagoDocente = (function ()/* - */ {
  //Privado  
  function limpiarCampos(formulario) {
    $(formulario).find(":input, select").each(function (i, e) {
      if (e.name !== "fecha" && e.name !== "motivo" && e.name !== "monto" && e.name !== "_token" && e.type !== "hidden") {
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
      rules: {
        fecha: {
          required: true,
          validarFecha: true
        }
      },
      submitHandler: function (f) {
        var idPago = $(formulario).find("input[name='idPago']").val();
        if (confirm("¿Está seguro que desea " + (idPago !== "" ? "actualizar los datos de este pago" : "realizar este pago") + "?")) {
          $("#mod-pago").modal("hide");
          $.blockUI({message: "<h4>Guardando cambios...</h4>"});

          var datos = utilFormularios.procesarDatos(f);
          $.extend(datos, listaPagosDocente.obtenerDatosFiltrosBusqueda());
          util.llamadaAjax($(f).attr("action"), "POST", datos, true,
                  function (d) {
                    $("body").unblock();
                  },
                  function (d) {
                    listaPagosDocente.reCargar();
                  },
                  function (de) {
                    var rj = de.responseJSON;
                    $("body").unblock({
                      onUnblock: function () {
                        if (rj !== undefined && rj.mensaje !== undefined) {
                          mensajes.agregar("errores", rj.mensaje, true);
                        } else if (rj !== undefined && rj[Object.keys(rj)[0]] !== undefined) {
                          mensajes.agregar("errores", rj[Object.keys(rj)[0]][0], true);
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
    $(formulario).find("input[name='idProfesor']").val(datos.idProfesor);
    $(formulario).find("input[name='monto']").val(util.redondear(datos.montoTotalXClases, 2));

    if (datos.idPagoProfesor !== null) {
      $(formulario).find("input[name='idPago']").val(datos.idPagoProfesor);
      $(formulario).find("input[name='descripcion']").val(datos.descripcionPagoProfesor);

      var datFecha = utilFechasHorarios.formatoFecha(datos.fechaPagoProfesor).split("/");
      $(formulario).find("input[name='fecha']").datepicker("setDate", (new Date(datFecha[1] + "/" + datFecha[0] + "/" + datFecha[2])));

      if (datos.imagenesComprobantePagoProfesor !== null && datos.imagenesComprobantePagoProfesor !== "") {
        var imagenes = datos.imagenesComprobantePagoProfesor.split(",");
        for (var i = 0; i < imagenes.length; i++) {
          var datosImagen = imagenes[i].split(":");
          if (datosImagen.length === 2) {
            archivosAdjuntos.agregar(formulario, "imagenes-comprobantes", datosImagen[0], datosImagen[1], true);
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