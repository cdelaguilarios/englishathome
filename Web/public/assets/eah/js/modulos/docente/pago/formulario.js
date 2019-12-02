var formularioPagoDocente = {};
formularioPagoDocente = (function ()/* - */ {
  window.addEventListener("load", esperarCargaJquery, false);
  function esperarCargaJquery()/* - */ {
    ((window.jQuery && jQuery.ui) ? cargarFormulario() : window.setTimeout(esperarCargaJquery, 100));
  }

  //Privado  
  function cargarFormulario() {
    $("#formulario-pago").validate({
      rules: {
        fecha: {
          required: true,
          validarFecha: true
        }
      },
      submitHandler: function (f) {
        if (confirm("¿Está seguro que desea registrar los datos de este pago?")) {
          $("#mod-pago").modal("hide");
          $.blockUI({message: "<h4>Registrando datos...</h4>"});

          var datos = utilFormularios.procesarDatos(f);
          $.extend(datos, listaPagosDocente.obtenerDatosFiltrosBusqueda());
          util.llamadaAjax($(f).attr("action"), "POST", datos, true,
                  function (d) {
                    $("body").unblock({
                      onUnblock: function () {
                        mensajes.agregar("exitosos", "Pago registrado.", true);
                      }
                    });
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
    $("#fecha-pago").datepicker("setDate", "today");
    $("form .help-block-error").remove();
  }

  //Público
  function mostrar(datosIniciales)/* - */ {
    limpiarCampos();
    $("#mod-pago").find(".modal-title").html("Nuevo pago para el profesor(a) " + datosIniciales.profesor);
    $("#formulario-pago").find("input[name='idProfesor']").val(datosIniciales.idProfesor);
    $("#formulario-pago").find("input[name='monto']").val(util.redondear(datosIniciales.montoTotalXClases, 2));
    $("#mod-pago").modal("show");
  }

  return {
    mostrar: mostrar
  };
}());