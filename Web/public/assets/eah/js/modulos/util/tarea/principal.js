var Tareas = Tareas || (function () {
  'use strict';

  //Privado
  var esperarCargaComponentes = function () {
    var self = this;
    (typeof (util) !== "undefined" && util.jQueryCargado() ? cargarSeccion.call(self) : window.setTimeout(function () {
      esperarCargaComponentes.call(self);
    }, 100));
  };
  var cargarSeccion = function () {
    var self = this;

    $(".nav-tabs").find("a[href='#pest-panel-tareas']").click(function () {
      self._args.seccionActual = "PANEL";
      self._args.panelTareas.mostrar();
    });
    $(".nav-tabs").find("a[href='#pest-lista-tareas']").click(function () {
      self._args.seccionActual = "LISTA";
      self._args.listaTareas.mostrar();
    });

    self._args.panelTareas.mostrar();
  };

  //Público
  var Constructor = function (args) {
    this._args = {
      seccionActual: "PANEL"
    };
    Object.assign(this._args, args);
    esperarCargaComponentes.call(this);
  };
  Constructor.prototype.mostrarVistaActual = function () {
    if (this._args.seccionActual === "PANEL") {
      this._args.panelTareas.mostrar();
    } else {
      this._args.listaTareas.mostrar();

    }
  };

  return Constructor;
})();