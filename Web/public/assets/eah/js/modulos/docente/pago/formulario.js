var formularioPagoDocente = {};
formularioPagoDocente = (function ()/* - */ {
  window.addEventListener("load", esperarCargaJquery, false);
  function esperarCargaJquery()/* - */ {
    ((window.jQuery && jQuery.ui) ? cargar() : window.setTimeout(esperarCargaJquery, 100));
  }

  //Privado  
  function cargar() {
    $("#formulario-pago").validate({
      rules: {
        fecha: {
          required: true,
          validarFecha: true
        }
      },
      submitHandler: function (f) {
        var idPago = $("#formulario-pago").find("input[name='idPago']").val();
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
    utilFechasHorarios.establecerCalendario($("#fecha-pago"), false, false, false);
  }
  function limpiarCampos() {
    $("#formulario-pago").find(":input, select").each(function (i, e) {
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
    archivosAdjuntos.limpiarCampos($("#formulario-pago"), "ImagenesComprobantes");
    $("#fecha-pago").datepicker("setDate", "today");
    $("form .help-block-error").remove();
  }

  function establecerDatos(datos) {
    limpiarCampos();
    $("#formulario-pago").find("input[name='idPago']").val("");
    $("#formulario-pago").find("input[name='idProfesor']").val(datos.idProfesor);
    $("#formulario-pago").find("input[name='monto']").val(util.redondear(datos.montoTotalXClases, 2));

    if (datos.idPago !== null) {
      $("#formulario-pago").find("input[name='idPago']").val(datos.idPago);
      $("#descripcion-pago").val(datos.descripcionPago);

      var datFecha = utilFechasHorarios.formatoFecha(datos.fechaPago).split("/");
      $("#fecha-pago").datepicker("setDate", (new Date(datFecha[1] + "/" + datFecha[0] + "/" + datFecha[2])));

      if (datos.imagenesComprobantePago !== null && datos.imagenesComprobantePago !== "") {
        var imagenes = datos.imagenesComprobantePago.split(",");
        for (var i = 0; i < imagenes.length; i++) {
          var datosImagen = imagenes[i].split(":");
          if (datosImagen.length === 2) {
            archivosAdjuntos.agregar("imagenes-comprobantes", datosImagen[0], datosImagen[1], true);
          }
        }
      }
    }
  }

  //Público
  function registrar(datos)/* - */ {
    establecerDatos(datos);
    $("#formulario-pago").submit();
  }
  function editar(datos)/* - */ {
    establecerDatos(datos);
    $("#mod-pago").find(".modal-title").html("Pago al profesor(a) " + datos.profesor);
    $("#mod-pago").modal("show");
  }

  return {
    registrar: registrar,
    editar: editar
  };
}());