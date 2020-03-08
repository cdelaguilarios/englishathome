var ListaNotificacionesGenerales = ListaNotificacionesGenerales || (function () {
  'use strict';

  //Privado
  var esperarCargaJquery = function () {
    var self = this;
    ((window.jQuery && jQuery.ui) ? cargarLista.call(self) : window.setTimeout(function(){
      esperarCargaJquery.call(self);
    }, 100));
  };
  var cargarLista = function ()/* - */ {
    var self = this;
    $("#" + self._args.idTablaLista).DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: self._args.urlListarNotificaciones,
        type: "POST",
        data: function (d) {
          d._token = $("meta[name=_token]").attr("content");
          $.extend(d, filtrosBusquedaFechas.obtenerDatos(self._args.idSeccion));
        }
      },
      autoWidth: false,
      responsive: true,
      orderCellsTop: true,
      fixedHeader: true,
      order: [[2, "desc"]],
      rowId: 'id',
      columns: [
        {data: "", name: "", orderable: false, "searchable": false, render: function (e, t, d, m) {
            return m.row + m.settings._iDisplayStart + 1;
          }, "className": "text-center", responsivePriority: 0},
        {data: "titulo", name: "titulo", render: function (e, t, d, m) {
            var tituloPro = d.titulo;
            var mensajePro = (d.mensaje !== null ? d.mensaje : "");

            //Entidades involucradas
            var involucrados = "<ul>";
            var idEntidadPrincipal = null;
            if (d.entidadesInvolucradas !== null) {
              var entidadesInvolucradas = d.entidadesInvolucradas.split(";");
              for (var i = 0; i < entidadesInvolucradas.length; i++) {
                var entidad = entidadesInvolucradas[i];

                var datosEntidad = entidad.split(":")[0];
                var tipoEntidad = datosEntidad.split("-")[0];
                var idEntidad = parseInt(datosEntidad.split("-")[1]);

                var nombreEntidad = entidad.split(":")[1];
                var urlEntidad = '<a href="' + self._args.urlPerfilEntidad.replace("/0", "/" + idEntidad) + '" target="_blank">' + nombreEntidad + '</a>';

                tituloPro = tituloPro.replaceAll("[" + tipoEntidad + "]", urlEntidad);
                mensajePro = mensajePro.replaceAll("[" + tipoEntidad + "]", urlEntidad);

                if (idEntidad !== d.idUsuarioCreador) {
                  involucrados += "<li>" + self._args.tiposEntidades[tipoEntidad][1] + " " + urlEntidad + "</li>";
                }

                if (tipoEntidad !== self._args.tipoEntidadUsuario) {
                  idEntidadPrincipal = idEntidad;
                }
              }
              involucrados += "</ul>";
            }

            //Adjuntos
            var htmlAdjuntos = "";
            if (d.adjuntos !== null && d.adjuntos !== "") {
              var adjuntos = d.adjuntos.split(",");
              $.each(adjuntos, function (e, v) {
                if (v !== null && v !== "") {
                  var datosAdjunto = v.split(":");

                  var rutaAdjunto = urlArchivos.replace("/0", "/" + datosAdjunto[0]);
                  var nombreAdjunto = datosAdjunto[datosAdjunto.length === 2 ? 1 : 0];

                  htmlAdjuntos += '<a href="' + rutaAdjunto + '" target="_blank">' +
                          (util.urlEsImagen(rutaAdjunto) ? '<img src="' + rutaAdjunto + '" class="margin" width="200">' : nombreAdjunto) +
                          '</a><br/>';
                }
              });
            }

            var htmlNotificacion = '';
            if (!window.mobilecheck()) {
              htmlNotificacion += '<div style="float: left;">' +
                      '<i class="fa ' + self._args.tiposNotificaciones[d.tipo][1] + ' ' + self._args.tiposNotificaciones[d.tipo][2] + '"></i>' +
                      '</div>' +
                      '<div style="margin-left: 40px;">';
            }
            htmlNotificacion += tituloPro;
            if (mensajePro !== "") {
              htmlNotificacion += '<br/><br/>' + mensajePro;
            }
            if (htmlAdjuntos !== "") {
              htmlNotificacion += '<br/><br/>' + htmlAdjuntos;
            }
            if (window.mobilecheck()) {
              htmlNotificacion += '<br/><br/><b>Fecha:</b> ' + utilFechasHorarios.formatoFecha(d.fechaNotificacion, true);
            }

            if (d.idUsuarioCreador !== null) {
              if (involucrados.includes("<li>")) {
                htmlNotificacion += "<br/><br/><b>Involucrado(s):<b> " + involucrados;
              }
              htmlNotificacion += '<br/><br/><b>Creado por<b> <a href="' + self._args.urlPerfilEntidad.replace("/0", "/" + d.idUsuarioCreador) + '" target="_blank">' + d.nombreUsuarioCreador + ' ' + d.apellidoUsuarioCreador + '</a><br/><br/>';

              if (idEntidadPrincipal !== null) {
                htmlNotificacion += '<a href="javascript:void(0);" onclick="' + self._args.nombreModuloCrearEditar + '.editar(' + d.id + ', ' + idEntidadPrincipal + ');" class="btn btn-primary btn-xs">Editar</a>';
              }
              htmlNotificacion += '<a href="javascript:void(0);" title="Eliminar notificación" onclick="utilTablas.eliminarElemento(this, \'¿Está seguro que desea eliminar los datos de esta notificación?\', \'' + self._args.idTablaLista + '\', false, function(){utilTablas.recargarDatosTabla($(\'#' + self._args.idTablaLista + '\'));}, true)" data-id="' + d.id + '" data-urleliminar="' + self._args.urlEliminarNotificacion.replace("/0", "/" + d.id) + '" class="btn btn-danger btn-xs">Eliminar</a>';
            }
            if (!window.mobilecheck()) {
              htmlNotificacion += '</div>';
            }

            return htmlNotificacion;
          }, responsivePriority: 0},
        {data: "fechaNotificacion", name: "fechaNotificacion", width: "15%", render: function (e, t, d, m) {
            return utilFechasHorarios.formatoFecha(d.fechaNotificacion, true);
          }, "className": "text-center desktop"}
      ],
      initComplete: function (s, j) {
        utilTablas.establecerBotonRecargaTabla($("#" + self._args.idTablaLista));
        utilTablas.establecerCabecerasBusquedaTabla($("#" + self._args.idTablaLista));

        if (self._args.primeraCarga) {
          self._args.primeraCarga = false;
          actualizarNumeroNotificacionesNuevas.call(self);
        }
      },
      drawCallback: function (s) {
        revisarMultiple.call(self);
      }
    });
    var funcionCambio = function () {
      reCargar.call(self);
    };
    filtrosBusquedaFechas.cargar(self._args.idSeccion, funcionCambio);
  };
  var actualizarNumeroNotificacionesNuevas = function () {
    var self = this;
    util.llamadaAjax(self._args.urlListarNotificacionesNuevas, "POST", {}, true, function (d) {
      if (d.length > 0) {
        $("#" + self._args.idBtnVerNotificaciones).append('<span class="label label-warning">' + d.length + '</span>');
      } else {
        $("#" + self._args.idBtnVerNotificaciones).find(".label-warning").remove();
      }
    });
  };
  var revisarMultiple = function () {
    var self = this;
    if ($("#" + self._args.idTablaLista).is(":visible")) {
      var idsNotificaciones = jQuery.map($("#" + self._args.idTablaLista).DataTable().rows({page: 'current'}).data(), function (notificacion) {
        if (notificacion.fechaRevision === null) {
          return notificacion.id;
        }
      });

      if (idsNotificaciones.length > 0) {
        util.llamadaAjax(self._args.urlRevisarMultiple, "POST", {"idsNotificaciones": idsNotificaciones}, true, function () {
          actualizarNumeroNotificacionesNuevas.call(self);
        });
      }
    }
  };
  var reCargar = function () {
    var self = this;
    $("#" + self._args.idTablaLista).DataTable().ajax.reload();
  };

  //Público
  var Constructor = function (args) {
    this._args = {
      idSeccion: "notificaciones",
      idPrefijoSeccionNotificaciones: "sec-notificaciones-generales-",
      idSeccionLista: "sec-notificaciones-generales-lista",
      idTablaLista: "tab-lista-notificaciones-generales",
      idBtnVerNotificaciones: "btn-ver-notificaciones-generales",
      primeraCarga: true
    };
    Object.assign(this._args, args);
    esperarCargaJquery.call(this);
  };
  Constructor.prototype.mostrar = function ()/* - */ {
    $("div[id^=" + this._args.idPrefijoSeccionNotificaciones + "]").hide();
    utilTablas.recargarDatosTabla($("#" + this._args.idTablaLista));
    $("#" + this._args.idSeccionLista).show();
  };
  Constructor.prototype.reCargar = function ()/* - */ {
    reCargar.call(this);
  };

  return Constructor;
})();