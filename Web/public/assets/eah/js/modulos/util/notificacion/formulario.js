var FormularioNotificacion = FormularioNotificacion || (function () {
  'use strict';

  //Privado
  var limpiarCampos = function (formulario) {
    var self = this;
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
    archivosAdjuntos.limpiarCampos(formulario, "Adjuntos" + util.letraCapital(self._args.idSeccion));
    $(formulario).find("input[name='mostrarEnPerfil']").attr("checked", true);
    $(formulario).find("input[name='mostrarEnPerfil']").closest("label").addClass("checked");
    var fechaProgramada = new Date();
    fechaProgramada.setDate(fechaProgramada.getDate() + 1);
    $(formulario).find("input[name='fechaProgramada']").datetimepicker("setDate", fechaProgramada);
    $("form .help-block-error").remove();
  };

  //Público
  var Constructor = function (args) {
    this._args = {
      idSeccionMensajes: "sec-notificaciones-" + args.idSeccion + "-mensajes",
      idSeccionProgramacion: "sec-notificaciones-" + args.idSeccion + "-programacion",
      idSeccionFecha: "sec-notificaciones-" + args.idSeccion + "-fecha"
    };
    Object.assign(this._args, args);
  };
  Constructor.prototype.cargar = function (formulario) {
    var self = this;
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
        var idNotificacion = $(f).find("input[name='idNotificacion']").val();

        if (!($(f).find("input[name='enviarCorreo']").is(":checked")
                || $(f).find("input[name='enviarCorreoEntidad']").is(":checked")
                || $(f).find("input[name='mostrarEnPerfil']").is(":checked"))) {
          mensajes.agregar("advertencias", 'Por favor selecione por lo menos una de las siguientes opciones: "Enviar correo" (administrador o entidad) o "Mostrar en perfil"', true, "#" + self._args.idSeccionMensajes);
        } else if (confirm("¿Está seguro que desea " + (idNotificacion !== "" ? "actualizar" : "registrar") + " los datos de este evento?")) {
          $.blockUI({message: "<h4>Guardando cambios...</h4>"});

          var datos = utilFormularios.procesarDatos(f);
          datos.mensaje = CKEDITOR.instances["mensaje-notificacion-" + self._args.idSeccion].getData();
          util.llamadaAjax($(f).attr("action"), "POST", datos, true,
                  function (d) {
                    $("body").unblock();
                    (window["listaNotificaciones" + util.letraCapital(self._args.idSeccion)]).mostrar();
                  },
                  function (d) {
                    (window["listaNotificaciones" + util.letraCapital(self._args.idSeccion)]).reCargar();
                  },
                  function (de) {
                    var rj = de.responseJSON;
                    $("body").unblock({
                      onUnblock: function () {
                        if (rj !== undefined && rj.mensaje !== undefined) {
                          mensajes.agregar("errores", rj.mensaje, true, "#" + self._args.idSeccionMensajes);
                        } else if (rj !== undefined && rj[Object.keys(rj)[0]] !== undefined) {
                          mensajes.agregar("errores", rj[Object.keys(rj)[0]][0], true, "#" + self._args.idSeccionMensajes);
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

    CKEDITOR.replace("mensaje-notificacion-" + self._args.idSeccion);
    utilFechasHorarios.establecerCalendario($(formulario).find("input[name='fechaProgramada']"), true, false);

    var seccionFechaProgramada = $(formulario).find("#" + self._args.idSeccionFecha);
    $(formulario).find("input[name='notificarInmediatamente']").change(function () {
      (($(this).is(":visible") && $(this).is(":checked")) ? $(seccionFechaProgramada).hide() : $(seccionFechaProgramada).show());
    });
  };
  Constructor.prototype.establecerDatos = function (formulario, datos, idEntidad) {
    limpiarCampos.call(this, formulario);
    $(formulario).find("input[name='idNotificacion']").val("");

    var seccionProgramacion = $(formulario).find("#" + this._args.idSeccionProgramacion);
    $(seccionProgramacion).show();

    if (datos) {
      $(formulario).find("input[name='idNotificacion']").val(datos.id);
      if (idEntidad) {
        $(formulario).find("input[name='idEntidad']").val(idEntidad);
      }
      $(formulario).find("input[name='titulo']").val(datos.titulo);
      CKEDITOR.instances["mensaje-notificacion-" + this._args.idSeccion].setData(datos.mensaje);

      if (datos.adjuntos !== null && datos.adjuntos !== "") {
        var adjuntos = datos.adjuntos.split(",");
        for (var i = 0; i < adjuntos.length; i++) {
          if (adjuntos[i].trim() !== "") {
            var datosAdjunto = adjuntos[i].split(":");
            archivosAdjuntos.agregar(formulario, "adjuntos-" + this._args.idSeccion, datosAdjunto[0], datosAdjunto[datosAdjunto.length === 2 ? 1 : 0], true);
          }
        }
      }

      var fechaActual = new Date();
      var fechaProgramada = ((!isNaN(Date.parse(datos.fechaProgramada))) ? new Date(datos.fechaProgramada) : fechaActual);
      var fechaNotificacion = ((!isNaN(Date.parse(datos.fechaNotificacion))) ? new Date(datos.fechaNotificacion) : fechaActual);

      if (fechaActual >= fechaNotificacion) {
        $(seccionProgramacion).hide();
      } else {
        if (datos.enviarCorreo === 1) {
          $(formulario).find("input[name='enviarCorreo']").attr("checked", true);
          $(formulario).find("input[name='enviarCorreo']").closest("label").addClass("checked");
        }
        if (datos.enviarCorreoEntidades === 1) {
          $(formulario).find("input[name='enviarCorreoEntidad']").attr("checked", true);
          $(formulario).find("input[name='enviarCorreoEntidad']").closest("label").addClass("checked");
        }
        if (datos.mostrarEnPerfil === 1) {
          $(formulario).find("input[name='mostrarEnPerfil']").attr("checked", true);
          $(formulario).find("input[name='mostrarEnPerfil']").closest("label").addClass("checked");
        }
        if (datos.notificarInmediatamente === 1) {
          $(formulario).find("input[name='notificarInmediatamente']").attr("checked", true);
          $(formulario).find("input[name='notificarInmediatamente']").closest("label").addClass("checked");
        }
      }
      $(formulario).find("input[name='fechaProgramada']").datetimepicker("setDate", fechaProgramada);
    } else {
      CKEDITOR.instances["mensaje-notificacion-" + this._args.idSeccion].setData("");
    }
    $(formulario).find("input[name='notificarInmediatamente']").change();
  };

  return Constructor;
})();