var ListaNotificacionesHistorial = ListaNotificacionesHistorial || (function () {
  'use strict';

  //Privado
  window.addEventListener("load", esperarCargaJquery, false);
  var esperarCargaJquery = function () {
    var self = this;
    ((window.jQuery && jQuery.ui) ? cargar.call(self) : window.setTimeout(function () {
      esperarCargaJquery.call(self);
    }, 100));
  };

  var cargar = function ()/* - */ {
    var self = this;
    cargarDatos.call(self);

    $("#" + self._args.idBtnNuevaNotificacion).click(function () {
      window[self._args.nombreModuloCrearEditar].crear();
    });
  };
  var cargarDatos = function ()/* - */ {
    var self = this;
    var meses = utilFechasHorarios.obtenerMeses();
    var eLista = $("#" + self._args.idSeccionLista + " .lista");
    var eSecBtnCargaMas = $("#" + self._args.idSeccionLista + " .sec-btn-carga-mas");
    var eNumeroCarga = $("#" + self._args.idSeccionLista).find("input[name='numeroCarga']");

    $(eNumeroCarga).val(($(eLista).html() !== "") ? $(eNumeroCarga).val() : 0);
    var numeroCarga = $(eNumeroCarga).val();

    if (!isNaN(parseInt(numeroCarga))) {
      $(eSecBtnCargaMas).hide();
      $(eLista).find(".sec-cierre").hide("slow");
      $(eLista).find(".sec-cierre").remove();

      $(eLista).append('<li class="sec-cargando">' +
              '<div class="box cargando">' +
              '<div class="overlay">' +
              '<i class="fa fa-refresh fa-spin"></i>' +
              '</div>' +
              '</div>' +
              '</li>');

      var datosLLamada = {numeroCarga: numeroCarga};
      util.llamadaAjax(self._args.urlListarNotificacionesHistorial, "POST", datosLLamada, true,
              function (d) {
                var htmlHistorial = "";
                var datosGrupoNotificaciones = d.datos;

                for (var fecha in datosGrupoNotificaciones) {
                  if (!datosGrupoNotificaciones.hasOwnProperty(fecha))
                    continue;

                  if (self._args.ultimaFechaCargada !== fecha) {
                    self._args.ultimaFechaCargada = fecha;
                    var fechaBase = new Date(fecha);
                    htmlHistorial += '<li class="time-label">' +
                            '<span class="bg-blue">' +
                            fechaBase.getDate() + ' ' + meses[fechaBase.getMonth() + 1] + ' ' + fechaBase.getFullYear() +
                            '</span>' +
                            '</li>';
                  }

                  var datosNotificaciones = datosGrupoNotificaciones[fecha];
                  for (var i = 0; i < datosNotificaciones.length; i++) {
                    htmlHistorial += '<li>' +
                            '<i class="fa ' + datosNotificaciones[i].icono + ' ' + datosNotificaciones[i].claseColorIcono + '"></i>' +
                            '<div class="timeline-item">' +
                            '<span class="time"><i class="fa fa-clock-o"></i> ' + datosNotificaciones[i].horaNotificacion + '</span>' +
                            '<h3 class="timeline-header">' + datosNotificaciones[i].titulo + '</h3>';

                    if (datosNotificaciones[i].mensaje !== "" || datosNotificaciones[i].adjuntos !== null) {
                      htmlHistorial += '<div class="timeline-body">' + (datosNotificaciones[i].mensaje !== "" ? datosNotificaciones[i].mensaje : "");

                      if (datosNotificaciones[i].adjuntos !== null && datosNotificaciones[i].adjuntos !== "") {
                        var adjuntos = datosNotificaciones[i].adjuntos.split(",");
                        $.each(adjuntos, function (e, v) {
                          if (v !== null && v !== "") {
                            var datosAdjunto = v.split(":");

                            var rutaAdjunto = urlArchivos.replace("/0", "/" + datosAdjunto[0]);
                            var nombreAdjunto = datosAdjunto[datosAdjunto.length === 2 ? 1 : 0];

                            htmlHistorial += '<a href="' + rutaAdjunto + '" target="_blank">' +
                                    (util.urlEsImagen(rutaAdjunto) ? '<img src="' + rutaAdjunto + '" class="margin" width="200">' : nombreAdjunto) +
                                    '</a><br/>';
                          }
                        });
                      }
                      htmlHistorial += '</div>';
                    }
                    if (datosNotificaciones[i].titulo === datosNotificaciones[i].tituloOriginal && datosNotificaciones[i].mensaje === datosNotificaciones[i].mensajeOriginal) {
                      htmlHistorial += '<div class="timeline-footer">' +
                              '<a href="javascript:void(0);" onclick="' + self._args.nombreModuloCrearEditar + '.editar(' + datosNotificaciones[i].id + ');" class="btn btn-primary btn-xs">Editar</a>' +
                              '<a href="javascript:void(0);" title="Eliminar evento" onclick="utilTablas.eliminarElemento(this, \'¿Está seguro que desea eliminar los datos de este evento?\', null, false, function(){listaNotificacionesHistorial.reCargar();}, true)" data-urleliminar="' + ((self._args.urlEliminarNotificacion.replace("/0", "/" + datosNotificaciones[i].id))) + '" class="btn btn-danger btn-xs">Eliminar</a>' +
                              '</div>';
                    }
                    htmlHistorial += '</div></li>';
                  }
                }

                htmlHistorial += (htmlHistorial !== "" ? '<li class="sec-cierre"><i class="fa fa-clock-o bg-gray"></i></li>' : '');
                var nuevoDatos = $(htmlHistorial).hide();

                $(eLista).find(".sec-cargando").fadeOut('slow', function () {
                  $(eNumeroCarga).val(parseInt($(eNumeroCarga).val()) + 1);
                  $(eLista).append(nuevoDatos);
                  nuevoDatos.show("normal");

                  $(eLista).find(".sec-cargando").remove();
                  if (d.mostrarBotonCargar) {
                    $(eSecBtnCargaMas).show("slow");
                  } else {
                    $(eSecBtnCargaMas).remove();
                  }
                });
              }
      );
    }
  };

  //Público
  var Constructor = function (args) {
    this._args = {
      idPrefijoSeccionNotificaciones: "sec-notificaciones-historial-",
      idSeccionLista: "sec-notificaciones-historial-lista",
      idBtnNuevaNotificacion: "btn-nueva-notificacion-historial",
      ultimaFechaCargada: ""
    };
    Object.assign(this._args, args);
    esperarCargaJquery.call(this);
  };
  Constructor.prototype.mostrar = function ()/* - */ {
    $("div[id^=" + this._args.idPrefijoSeccionNotificaciones + "]").hide();
    $("#" + this._args.idSeccionLista).show();
  };
  Constructor.prototype.cargarDatos = function ()/* - */ {
    cargarDatos.call(this);
  };
  Constructor.prototype.reCargar = function () {
    $("#" + this._args.idSeccionLista).find("input[name='numeroCarga']").val(0);
    $("#" + this._args.idSeccionLista + " .lista").html("");
    this._args.ultimaFechaCargada = "";
    cargarDatos.call(this);
  };

  return Constructor;
})();