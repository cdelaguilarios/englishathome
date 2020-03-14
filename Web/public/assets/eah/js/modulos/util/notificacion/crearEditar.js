var CrearEditarNotificacion = CrearEditarNotificacion || (function () {
  'use strict';

  //Privado
  window.addEventListener("load", esperarCargaJquery, false);
  var esperarCargaJquery = function () {
    var self = this;
    (typeof (util) !== "undefined" && util.jQueryCargado() ? cargarSeccion.call(self) : window.setTimeout(function () {
      esperarCargaJquery.call(self);
    }, 100));
  };
  var cargarSeccion = function () {
    var self = this;
    var formulario = obtenerReferenciaFormulario.call(self);
    self._args.formularioNotificacion.cargar(formulario);

    $("#" + self._args.idBtnCancelar).click(function () {
      self._args.listaNotificaciones.mostrar();
    });
  };
  var obtenerReferenciaFormulario = function () {
    var self = this;
    return $("#" + self._args.idFormulario);
  };
  var obtenerDatos = function (idNotificacion, funcionRetorno) {
    var self = this;
    $.blockUI({message: "<h4>Cargando...</h4>", baseZ: 2000});
    util.llamadaAjax(self._args.urlDatosNotificacion.replace("/0", "/" + idNotificacion), "POST", {}, true,
            function (d) {
              if (funcionRetorno !== undefined) {
                funcionRetorno(d);
              }
              $("body").unblock();
            },
            function () {
            },
            function () {
              $('body').unblock({
                onUnblock: function () {
                  mensajes.agregar("errores", "Ocurrió un problema durante la carga de datos del evento seleccionado. Por favor inténtelo nuevamente.", true, "#" + self._args.idSeccionMensajes);
                }
              });
            }
    );
  };

  //Público
  var Constructor = function (args) {
    this._args = {
      idSeccionMensajes: "sec-notificaciones-" + args.idSeccion + "-mensajes",
      idPrefijoSeccionNotificaciones: "sec-notificaciones-" + args.idSeccion + "-",
      idSeccionCrearEditar: "sec-notificaciones-" + args.idSeccion + "-crear-editar",
      idFormulario: "formulario-notificacion-" + args.idSeccion,
      idBtnCancelar: "btn-cancelar-notificacion-" + args.idSeccion
    };
    Object.assign(this._args, args);
    esperarCargaJquery.call(this);
  };
  Constructor.prototype.crear = function () {
    var formulario = obtenerReferenciaFormulario.call(this);

    $("#" + this._args.idSeccionCrearEditar).find(".box-title").html("Nuevo evento");
    $("div[id^=" + this._args.idPrefijoSeccionNotificaciones + "]").hide();
    this._args.formularioNotificacion.establecerDatos(formulario);
    $("#" + this._args.idSeccionCrearEditar).show();
  };
  Constructor.prototype.editar = function (idNotificacion, idEntidad) {
    var self = this;
    obtenerDatos.call(self, idNotificacion, function (d) {
      var formulario = obtenerReferenciaFormulario.call(self);

      $("#" + self._args.idSeccionCrearEditar).find(".box-title").html("Editar evento");
      $("div[id^=" + self._args.idPrefijoSeccionNotificaciones + "]").hide();
      self._args.formularioNotificacion.establecerDatos(formulario, d, idEntidad);
      $("#" + self._args.idSeccionCrearEditar).show();
    });
  };

  return Constructor;
})();