var FormularioTarea = FormularioTarea || (function () {
  'use strict';

  //Privado
  var limpiarCampos = function (formulario) {
    $(formulario).find(":input, select").each(function (i, e) {
      if (e.name !== "fechaProgramada" && e.name !== "fechaFinalizacion" && e.name !== "_token" && e.type !== "hidden") {
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

    archivosAdjuntos.limpiarCampos(formulario, "AdjuntosTarea");

    var fechaProgramada = new Date();
    fechaProgramada.setDate(fechaProgramada.getDate() + 1);
    $(formulario).find("input[name='fechaProgramada']").datetimepicker("setDate", fechaProgramada);
    $(formulario).find("input[name='fechaFinalizacion']").val("");

    $("form .help-block-error").remove();
  };

  //Público
  var Constructor = function (args) {
    this._args = {
      idSeccionMensajes: "sec-tareas-mensajes",
      idSelUsuarioAsignado: "sel-usuario-tarea",
      idSeccionProgramacion: "sec-tareas-programacion",
      idSeccionFechaProgramada: "sec-tareas-fecha-programada"
    };
    Object.assign(this._args, args);
  };
  Constructor.prototype.cargar = function (formulario) {
    var self = this;
    $(formulario).validate({
      ignore: ":hidden",
      rules: {
        idUsuarioAsignado: {
          required: true
        },
        fechaProgramada: {
          required: true,
          validarFechaHora: true
        },
        fechaFinalizacion: {
          validarFechaHora: true
        }
      },
      submitHandler: function (f) {
        var idTarea = $(f).find("input[name='idTarea']").val();

        if (confirm("¿Está seguro que desea " + (idTarea !== "" ? "actualizar" : "registrar") + " los datos de esta tarea?")) {
          $.blockUI({message: "<h4>Guardando cambios...</h4>"});

          var datos = utilFormularios.procesarDatos(f);
          datos.mensaje = CKEDITOR.instances["mensaje-tarea"].getData();
          util.llamadaAjax($(f).attr("action"), "POST", datos, true,
                  function (d) {
                    $("body").unblock();
                    self._args.panelTareas.mostrar();
                  },
                  function (d) {
                    self._args.panelTareas.reCargar();
                    if (self._args.listaTareas !== undefined) {
                      self._args.listaTareas.reCargar();
                    }
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

    CKEDITOR.replace("mensaje-tarea");
    utilBusqueda.establecerListaBusqueda($("#" + self._args.idSelUsuarioAsignado), self._args.urlBuscarUsuarios);
    utilFechasHorarios.establecerCalendario($(formulario).find("input[name='fechaProgramada']"), true, false);
    utilFechasHorarios.establecerCalendario($(formulario).find("input[name='fechaFinalizacion']"), true, false);

    var seccionFechaProgramada = $(formulario).find("#" + self._args.idSeccionFechaProgramada);
    $(formulario).find("input[name='notificarInmediatamente']").change(function () {
      (($(this).is(":visible") && $(this).is(":checked")) ? $(seccionFechaProgramada).hide() : $(seccionFechaProgramada).show());
    });
  };
  Constructor.prototype.establecerDatos = function (formulario, datos) {
    limpiarCampos.call(this, formulario);
    $(formulario).find("input[name='idTarea']").val("");

    var seccionProgramacion = $(formulario).find("#" + this._args.idSeccionProgramacion);
    $(seccionProgramacion).show();

    if (datos) {
      $(formulario).find("input[name='idTarea']").val(datos.id);
      $(formulario).find("#" + this._args.idSelUsuarioAsignado).empty().append('<option value="' + datos.idUsuarioAsignado + '">' + datos.nombreUsuarioAsignado + ' ' + datos.apellidoUsuarioAsignado + '</option>').val(datos.idUsuarioAsignado);
      CKEDITOR.instances["mensaje-tarea"].setData(datos.mensaje);

      if (datos.adjuntos !== null && datos.adjuntos !== "") {
        var adjuntos = datos.adjuntos.split(",");
        for (var i = 0; i < adjuntos.length; i++) {
          if (adjuntos[i].trim() !== "") {
            var datosAdjunto = adjuntos[i].split(":");
            archivosAdjuntos.agregar(formulario, "adjuntos-tarea", datosAdjunto[0], datosAdjunto[datosAdjunto.length === 2 ? 1 : 0], true);
          }
        }
      }

      var fechaActual = new Date();
      var fechaProgramada = ((!isNaN(Date.parse(datos.fechaProgramada))) ? new Date(datos.fechaProgramada) : null);
      var fechaNotificacion = ((!isNaN(Date.parse(datos.fechaNotificacion))) ? new Date(datos.fechaNotificacion) : fechaActual);
      var fechaFinalizacion = ((!isNaN(Date.parse(datos.fechaFinalizacion))) ? new Date(datos.fechaFinalizacion) : null);

      if (fechaActual >= fechaNotificacion) {
        $(seccionProgramacion).hide();
      } else {
        if (datos.notificarInmediatamente === 1) {
          $(formulario).find("input[name='notificarInmediatamente']").attr("checked", true);
          $(formulario).find("input[name='notificarInmediatamente']").closest("label").addClass("checked");
        }
      }
      if (fechaProgramada !== null)
        $(formulario).find("input[name='fechaProgramada']").datetimepicker("setDate", fechaProgramada);
      if (fechaFinalizacion !== null)
        $(formulario).find("input[name='fechaFinalizacion']").datetimepicker("setDate", fechaFinalizacion);
    } else {
      CKEDITOR.instances["mensaje-tarea"].setData("");
    }
    $(formulario).find("input[name='notificarInmediatamente']").change();
  };

  return Constructor;
})();