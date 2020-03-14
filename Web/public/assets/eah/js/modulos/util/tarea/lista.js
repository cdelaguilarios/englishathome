var ListaTareas = ListaTareas || (function () {
  'use strict';

  //Privado
  var esperarCargaJquery = function () {
    var self = this;
    (typeof (util) !== "undefined" && util.jQueryCargado() ? cargarLista.call(self) : window.setTimeout(function () {
      esperarCargaJquery.call(self);
    }, 100));
  };
  var cargarLista = function ()/* - */ {
    var self = this;
    $("#" + self._args.idTablaLista).DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: self._args.urlListarTareas,
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
        {data: "mensaje", name: "mensaje", render: function (e, t, tarea, m) {
            var htmlTarea = '<div>' +
                    '<small class="label label-primary">Tarea #' + tarea.id + '</small>' +
                    '<div class="pull-right">' +
                    '<a title="Editar tarea" href="javascript:void(0);" onclick="' + self._args.nombreModuloCrearEditar + '.editar(' + tarea.id + ');"><i class="fa fa-pencil"></i></a>&nbsp;&nbsp;' +
                    '<a title="Eliminar tarea" href="javascript:void(0);" onclick="utilTablas.eliminarElemento(this, \'¿Está seguro que desea eliminar los datos de esta tarea?\', null, true, function(d){ ' + self._args.nombreModuloPanel + '.reCargar(); }, true)" data-id="' + tarea.id + '" data-urleliminar="' + self._args.urlEliminarTarea.replace("/0", "/" + tarea.id) + '"><i class="fa fa-trash"></i></a>' +
                    '</div>' +
                    '</div><br/>';
            htmlTarea += '<div>' + tarea.mensaje;
            if (tarea.adjuntos !== null && tarea.adjuntos !== "") {
              var adjuntos = tarea.adjuntos.split(",");
              $.each(adjuntos, function (e, v) {
                if (v !== null && v !== "") {
                  var datosAdjunto = v.split(":");

                  var rutaAdjunto = urlArchivos.replace("/0", "/" + datosAdjunto[0]);
                  var nombreAdjunto = datosAdjunto[datosAdjunto.length === 2 ? 1 : 0];

                  htmlTarea += '<a href="javascript:void(0)" onclick="util.cargarUrl(\'' + rutaAdjunto + '\');" target="_blank">' +
                          (util.urlEsImagen(rutaAdjunto) ? '<img src="' + rutaAdjunto + '" class="margin" width="100">' : nombreAdjunto) +
                          '</a><br/>';
                }
              });
            }
            htmlTarea += '</div>';
            htmlTarea += '<br/><b>Creado por</b> ' + tarea.nombreUsuarioCreador + ' ' + tarea.apellidoUsuarioCreador;
            htmlTarea += '<br/><b>Asignado a</b> ' + tarea.nombreUsuarioAsignado + ' ' + tarea.apellidoUsuarioAsignado;
            
            if (tarea.fechaProgramada !== null) {
              htmlTarea += '<br/><b>Fecha notificacion: </b> ' + utilFechasHorarios.formatoFecha(tarea.fechaProgramada, true);
            }
            if (tarea.fechaFinalizacion !== null) {
              htmlTarea += '<br/><b>Fecha finalización: </b> ' + utilFechasHorarios.formatoFecha(tarea.fechaFinalizacion, true);
            }

            return htmlTarea;
          }, responsivePriority: 0},
        {data: "estado", name: "estado", width: "15%", "className": "text-center desktop"}
      ],
      initComplete: function (s, j) {
        utilTablas.establecerBotonRecargaTabla($("#" + self._args.idTablaLista));
        utilTablas.establecerCabecerasBusquedaTabla($("#" + self._args.idTablaLista));
      }
    });
    var funcionCambio = function () {
      reCargar.call(self);
    };
    filtrosBusquedaFechas.cargar(self._args.idSeccion, funcionCambio);
  };
  var reCargar = function () {
    var self = this;
    $("#" + self._args.idTablaLista).DataTable().ajax.reload();
  };

  //Público
  var Constructor = function (args) {
    this._args = {
      idSeccion: "tareas",
      idPrefijoSeccionTareas: "sec-tareas-",
      idSeccionLista: "sec-tareas-lista",
      idTablaLista: "tab-lista-tareas"
    };
    Object.assign(this._args, args);
    esperarCargaJquery.call(this);
  };
  Constructor.prototype.mostrar = function ()/* - */ {
    $("div[id^=" + this._args.idPrefijoSeccionTareas + "]").hide();
    utilTablas.recargarDatosTabla($("#" + this._args.idTablaLista));
    $("#" + this._args.idSeccionLista).show();
  };
  Constructor.prototype.reCargar = function ()/* - */ {
    reCargar.call(this);
  };

  return Constructor;
})();