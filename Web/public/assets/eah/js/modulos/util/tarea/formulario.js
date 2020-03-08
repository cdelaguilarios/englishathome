var formularioTarea = {};
formularioTarea = (function () {
  //Privado
  function limpiarCampos(formulario) {
    $(formulario).find(":input, select").each(function (i, e) {
      if (e.name !== "fechaProgramada" && e.name !== "_token" && e.type !== "hidden") {
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
    archivosAdjuntos.limpiarCampos(formulario, "Adjuntos");
    var fechaProgramada = new Date();
    fechaProgramada.setDate(fechaProgramada.getDate() + 1);
    $(formulario).find("input[name='fechaProgramada']").datetimepicker("setDate", fechaProgramada);
    $("form .help-block-error").remove();
  }

  //Público
  function cargar(formulario) {
    $(formulario).validate({
      ignore: ":hidden",
      rules: {
        titulo: {
          required: true
        },
        fechaProgramada: {
          validarFechaHora: true
        }
      },
      submitHandler: function (f) {
        var idTarea = $(f).find("input[name='idTarea']").val();

        if (confirm("¿Está seguro que desea " + (idTarea !== "" ? "actualizar" : "registrar") + " los datos de esta tarea?")) {
          $.blockUI({message: "<h4>Guardando cambios...</h4>"});

          var datos = utilFormularios.procesarDatos(f);
          datos.mensaje = CKEDITOR.instances["mensaje"].getData();
          util.llamadaAjax($(f).attr("action"), "POST", datos, true,
                  function (d) {
                    $("body").unblock();
                    listaTareas.mostrar();
                  },
                  function (d) {
                    listaTareas.reiniciar();
                  },
                  function (de) {
                    var rj = de.responseJSON;
                    $("body").unblock({
                      onUnblock: function () {
                        if (rj !== undefined && rj.mensaje !== undefined) {
                          mensajes.agregar("errores", rj.mensaje, true, "#sec-tareas-mensajes");
                        } else if (rj !== undefined && rj[Object.keys(rj)[0]] !== undefined) {
                          mensajes.agregar("errores", rj[Object.keys(rj)[0]][0], true, "#sec-tareas-mensajes");
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

    CKEDITOR.replace("mensaje");
    utilFechasHorarios.establecerCalendario($(formulario).find("input[name='fechaProgramada']"), true, false);

    var seccionFechaProgramada = $(formulario).find("#sec-tareas-fecha");
    $(formulario).find("input[name='notificarInmediatamente']").change(function () {
      (($(this).is(":visible") && $(this).is(":checked")) ? $(seccionFechaProgramada).hide() : $(seccionFechaProgramada).show());
    });
  }
  function establecerDatos(formulario, datos) {
    limpiarCampos(formulario);
    $(formulario).find("input[name='idTarea']").val("");

    var seccionProgramacion = $(formulario).find("#sec-tarea-programacion");
    $(seccionProgramacion).show();

    if (datos) {
      $(formulario).find("input[name='idTarea']").val(datos.id);
      $(formulario).find("input[name='titulo']").val(datos.titulo);
      CKEDITOR.instances["mensaje"].setData(datos.mensaje);

      if (datos.adjuntos !== null && datos.adjuntos !== "") {
        var adjuntos = datos.adjuntos.split(",");
        for (var i = 0; i < adjuntos.length; i++) {
          if (adjuntos[i].trim() !== "") {
            var datosAdjunto = adjuntos[i].split(":");
            archivosAdjuntos.agregar(formulario, "adjuntos", datosAdjunto[0], datosAdjunto[datosAdjunto.length === 2 ? 1 : 0], true);
          }
        }
      }

      var fechaActual = new Date();
      var fechaProgramada = ((!isNaN(Date.parse(datos.fechaProgramada))) ? new Date(datos.fechaProgramada) : fechaActual);
      var fechaNotificacion = ((!isNaN(Date.parse(datos.fechaNotificacion))) ? new Date(datos.fechaNotificacion) : fechaActual);

      if (fechaActual >= fechaNotificacion) {
        $(seccionProgramacion).hide();
      } else {
        if (datos.notificarInmediatamente === 1) {
          $(formulario).find("input[name='notificarInmediatamente']").attr("checked", true);
          $(formulario).find("input[name='notificarInmediatamente']").closest("label").addClass("checked");
        }
      }
      $(formulario).find("input[name='fechaProgramada']").datetimepicker("setDate", fechaProgramada);
    } else {
      CKEDITOR.instances["mensaje"].setData("");
    }
    $(formulario).find("input[name='notificarInmediatamente']").change();
  }

  return {
    cargar: cargar,
    establecerDatos: establecerDatos
  };
}());