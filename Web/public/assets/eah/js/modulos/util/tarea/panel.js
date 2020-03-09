var PanelTareas = PanelTareas || (function () {
  'use strict';

  //Privado
  var esperarCargaJquery = function () {
    var self = this;
    ((window.jQuery && jQuery.ui) ? cargar.call(self) : window.setTimeout(function () {
      esperarCargaJquery.call(self);
    }, 100));
  };

  var cargar = function ()/* - */ {
    var self = this;

    $("#" + self._args.idBtnNuevaTarea).click(function () {
      window[self._args.nombreModuloCrearEditar].crear();
    });
    $("#" + self._args.idBtnVerTareas).click(function () {
      cargarDatos.call(self);
    });
    $("#" + self._args.idSelTipoTareas).change(function () {
      cargarDatos.call(self);
    });
    actualizarNumeroTareasNoRevisadas.call(self);
  };
  var cargarDatos = function ()/* - */ {
    var self = this;

    $("#" + self._args.idPanel).html("");
    self._args.panel = new jKanban({
      element: "#" + self._args.idPanel,
      responsivePercentage: true,
      dragBoards: false,
      dropEl: function (ele, des) {
        var idTarea = $(ele).data("eid");
        var nuevoEstado = $(des).closest("div").data("id");
        util.llamadaAjax(self._args.urlActualizarEstadoTarea.replace("/0", "/" + idTarea), "POST", {"estado": nuevoEstado}, true, function () {
          actualizarNumeroTareasNoRevisadas.call(self);
        });
      },
      boards: [
        {
          'id': self._args.estadoTareaPendiente,
          'title': 'Pendientes',
          'class': 'bg-aqua'
        },
        {
          'id': self._args.estadoTareaEnProceso,
          'title': 'En proceso',
          'class': 'bg-yellow'
        },
        {
          'id': self._args.estadoTareaRealizada,
          'title': 'Realizadas',
          'class': 'bg-green'
        }
      ]
    });

    util.llamadaAjax(self._args.urlListarTareasPanel, "POST", {seleccionarMisTareas: $("#" + self._args.idSelTipoTareas).val()}, true,
            function (tareas) {
              self._args.tareas = tareas;
              tareas.forEach(function (tarea) {
                var htmlTarea = '<b>' + tarea.titulo + '</b><br/><br/>';
                if (tarea.mensaje !== "" || tarea.adjuntos !== null) {
                  htmlTarea += '<div>' + (tarea.mensaje !== "" ? tarea.mensaje : "");

                  if (tarea.adjuntos !== null && tarea.adjuntos !== "") {
                    var adjuntos = tarea.adjuntos.split(",");
                    $.each(adjuntos, function (e, v) {
                      if (v !== null && v !== "") {
                        var datosAdjunto = v.split(":");

                        var rutaAdjunto = urlArchivos.replace("/0", "/" + datosAdjunto[0]);
                        var nombreAdjunto = datosAdjunto[datosAdjunto.length === 2 ? 1 : 0];

                        htmlTarea += '<a href="' + rutaAdjunto + '" target="_blank">' +
                                (util.urlEsImagen(rutaAdjunto) ? '<img src="' + rutaAdjunto + '" class="margin" width="200">' : nombreAdjunto) +
                                '</a><br/>';
                      }
                    });
                  }
                  htmlTarea += '</div>';
                }

                if ($("#" + self._args.idSelTipoTareas).val() === 1 && tarea.idUsuarioCreador !== null) {
                  htmlTarea += '<br/><b>Creado por</b> ' + tarea.nombreUsuarioCreador + ' ' + tarea.apellidoUsuarioCreador + '<br/>';
                } else if ($("#" + self._args.idSelTipoTareas).val() !== 1 && tarea.idUsuarioAsignado !== null) {
                  htmlTarea += '<br/><b>Asignado a</b> ' + tarea.nombreUsuarioAsignado + ' ' + tarea.apellidoUsuarioAsignado + '<br/>';
                }

                self._args.panel.addElement(
                        tarea.estado,
                        {
                          'id': tarea.id,
                          'title': tarea.titulo
                        }
                );

                var elemento = $("#" + self._args.idPanel).find('[data-eid="' + tarea.id + '"]');
                $(elemento).html(htmlTarea);
              });
              revisarMultiple.call(self);
            }
    );
  };
  var actualizarNumeroTareasNoRevisadas = function () {
    var self = this;
    util.llamadaAjax(self._args.urlListarTareasNoRealizadas, "POST", {}, true, function (d) {
      if (d.length > 0) {
        $("#" + self._args.idBtnVerTareas).append('<span class="label label-warning">' + d.length + '</span>');
      } else {
        $("#" + self._args.idBtnVerTareas).find(".label-warning").remove();
      }
    });
  };
  var revisarMultiple = function () {
    var self = this;
    if ($("#" + self._args.idPanel).is(":visible")) {
      var idsTareas = jQuery.map(self._args.tareas, function (tarea) {
        if (tarea.fechaRevision === null) {
          return tarea.id;
        }
      });

      if (idsTareas.length > 0) {
        util.llamadaAjax(self._args.urlRevisarMultiple, "POST", {"idsTareas": idsTareas}, true, function () {
          actualizarNumeroTareasNoRevisadas.call(self);
        });
      }
    }
  };

  //Público
  var Constructor = function (args) {
    this._args = {
      idPrefijoSeccionTareas: "sec-tareas-",
      idSeccionPanel: "sec-tareas-panel",
      idPanel: "panel-tareas",
      idSelTipoTareas: "sel-tipo-tareas",
      idBtnNuevaTarea: "btn-nueva-tarea",
      panel: null,
      tareas: null
    };
    Object.assign(this._args, args);
    esperarCargaJquery.call(this);
  };
  Constructor.prototype.mostrar = function ()/* - */ {
    $("div[id^=" + this._args.idPrefijoSeccionTareas + "]").hide();
    $("#" + this._args.idSeccionPanel).show();
  };
  Constructor.prototype.reCargar = function ()/* - */ {
    cargarDatos.call(this);
  };

  return Constructor;
})();