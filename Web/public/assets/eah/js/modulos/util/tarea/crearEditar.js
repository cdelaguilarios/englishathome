var CrearEditarTarea = CrearEditarTarea || (function () {
  'use strict';

  //Privado
  window.addEventListener("load", esperarCargaJquery, false);
  var esperarCargaJquery = function () {
    var self = this;
    ((window.jQuery && jQuery.ui) ? cargarSeccion.call(self) : window.setTimeout(function () {
      esperarCargaJquery.call(self);
    }, 100));
  };
  var cargarSeccion = function () {
    var self = this;
    var formulario = obtenerReferenciaFormulario.call(self);
    self._args.formularioTarea.cargar(formulario);

    $("#" + self._args.idBtnCancelar).click(function () {
      self._args.panelTareas.mostrar();
    });
  };
  var obtenerReferenciaFormulario = function () {
    var self = this;
    return $("#" + self._args.idFormulario);
  };
  var obtenerDatos = function (idTarea, funcionRetorno) {
    var self = this;
    $.blockUI({message: "<h4>Cargando...</h4>", baseZ: 2000});
    util.llamadaAjax(self._args.urlDatosTarea.replace("/0", "/" + idTarea), "POST", {}, true,
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
                  mensajes.agregar("errores", "Ocurrió un problema durante la carga de datos de la tarea seleccionada. Por favor inténtelo nuevamente.", true, "#" + self._args.idSeccionMensajes);
                }
              });
            }
    );
  };

  //Público
  var Constructor = function (args) {
    this._args = {
      idSeccionMensajes: "sec-tareas-mensajes",
      idPrefijoSeccionTareas: "sec-tareas-",
      idSeccionCrearEditar: "sec-tareas-crear-editar",
      idFormulario: "formulario-tarea",
      idBtnCancelar: "btn-cancelar-tarea"
    };
    Object.assign(this._args, args);
    esperarCargaJquery.call(this);
  };
  Constructor.prototype.crear = function () {
    var formulario = obtenerReferenciaFormulario.call(this);

    $("#" + this._args.idSeccionCrearEditar).find(".box-title").html("Nueva tarea");
    $("div[id^=" + this._args.idPrefijoSeccionTareas + "]").hide();
    this._args.formularioTarea.establecerDatos(formulario);
    $("#" + this._args.idSeccionCrearEditar).show();
  };
  Constructor.prototype.editar = function (idTarea) {
    var self = this;
    obtenerDatos.call(self, idTarea, function (d) {
      var formulario = obtenerReferenciaFormulario.call(self);

      $("#" + self._args.idSeccionCrearEditar).find(".box-title").html("Editar tarea");
      $("div[id^=" + self._args.idPrefijoSeccionTareas + "]").hide();
      self._args.formularioTarea.establecerDatos(formulario, d);
      $("#" + self._args.idSeccionCrearEditar).show();
    });
  };

  return Constructor;
})();