var Notificaciones = Notificaciones || (function () {
  'use strict';

  //Privado
  var esperarCargaJquery = function () {
    var self = this;
    ((window.jQuery && jQuery.ui) ? cargarSeccion.call(self) : window.setTimeout(function () {
      esperarCargaJquery.call(self);
    }, 100));
  };
  var cargarSeccion = function () {
    this._args.listaNotificaciones.mostrar();
  };

  //Público
  var Constructor = function (args) {
    this._args = {};
    Object.assign(this._args, args);
    esperarCargaJquery.call(this);
  };

  return Constructor;
})();