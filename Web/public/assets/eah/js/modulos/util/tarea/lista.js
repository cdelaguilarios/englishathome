var listaTareas = {};
listaTareas = (function ()/* - */ {
  window.addEventListener("load", esperarCargaJquery, false);
  function esperarCargaJquery()/* - */ {
    ((window.jQuery && jQuery.ui) ? cargar() : window.setTimeout(esperarCargaJquery, 100));
  }

  //Privado
  var idSeccion = "tareas";
  var primeraCarga = true;
  function cargar()/* - */ {
    cargarLista();
  }
  function cargarLista()/* - */ {
    urlListarTareas = (typeof (urlListarTareas) === "undefined" ? "" : urlListarTareas);
    urlPerfilEntidad = (typeof (urlPerfilEntidad) === "undefined" ? "" : urlPerfilEntidad);
    urlEliminarTarea = (typeof (urlEliminarTarea) === "undefined" ? "" : urlEliminarTarea);

    if (urlListarTareas !== "" && urlPerfilEntidad !== "" && urlEliminarTarea !== "") {
      $("#tab-lista-tareas").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: urlListarTareas,
          type: "POST",
          data: function (d) {
            d._token = $("meta[name=_token]").attr("content");
            $.extend(d, filtrosBusquedaFechas.obtenerDatos(idSeccion));
          }
        },
        autoWidth: false,
        responsive: true,
        order: [[2, "desc"]],
        rowId: 'id',
        columns: [
          {data: "", name: "", orderable: false, "searchable": false, render: function (e, t, d, m) {
              return m.row + m.settings._iDisplayStart + 1;
            }, "className": "text-center", responsivePriority: 0},
          {data: "titulo", name: "titulo", render: function (e, t, d, m) {
              var titulo = d.titulo;
              var mensaje = d.mensaje;
              var involucrados = "<ul>";
              var usuarioCreador = "";

              if (d.entidadesInvolucradas !== null) {
                var entidadesInvolucradas = d.entidadesInvolucradas.split(";");
                for (var i = 0; i < entidadesInvolucradas.length; i++) {
                  var entidad = entidadesInvolucradas[i];

                  var datosEntidad = entidad.split(":")[0];
                  var tipoEntidad = datosEntidad.split("-")[0];
                  var idEntidad = parseInt(datosEntidad.split("-")[1]);

                  var nombreEntidad = entidad.split(":")[1];
                  var urlEntidad = '<a href="' + (urlPerfilEntidad.replace("/0", "/" + idEntidad)) + '" target="_blank">' + nombreEntidad + '</a>';

                  titulo = titulo.replaceAll("[" + tipoEntidad + "]", urlEntidad);
                  mensaje = mensaje.replaceAll("[" + tipoEntidad + "]", urlEntidad);

                  if (idEntidad === d.idUsuarioCreador) {
                    usuarioCreador = urlEntidad;
                  } else {
                    involucrados += "<li>" + tiposEntidades[tipoEntidad][1] + " " + urlEntidad + "</li>";
                  }
                }
                involucrados += "</ul>";
              }

              var tarea = titulo + '<br/><br/>' + mensaje;
              if (titulo === d.titulo && mensaje === d.mensaje) {
                if (involucrados.includes("<li>")) {
                  tarea += "<br/>Involucrado(s): " + involucrados;
                }
                if (usuarioCreador !== "") {
                  tarea += "<br/>Creador por " + usuarioCreador;
                }
                tarea += '<br/><br/><a href="javascript:void(0);" title="Eliminar tarea" onclick="utilTablas.eliminarElemento(this, \'¿Está seguro que desea eliminar los datos de esta tarea?\', \'tab-lista-tareas\', false, function(){utilTablas.recargarDatosTabla($(\'#tab-lista-tareas\'));}, true)" data-id="' + d.id + '" data-urleliminar="' + ((urlEliminarTarea.replace("/0", "/" + d.id))) + '" class="btn btn-danger btn-xs">Eliminar</a>'
              }

              return tarea;
            }},
          {data: "fechaNotificacion", name: "fechaNotificacion", width: "15%", render: function (e, t, d, m) {
              return utilFechasHorarios.formatoFecha(d.fechaNotificacion, true);
            }, className: "text-center not-mobile"},
          {data: "id", name: "id", orderable: false, searchable: false, width: "5%", render: function (e, t, d, m) {
              return '<input name="realizado" data-id="' + d.id + '" type="checkbox" ' + (d.fechaRealizada !== null ? 'checked' : '') + ' onclick="listaTareas.actualizarRealizacion(this);">';
            }, className: "text-center"}
        ],
        initComplete: function (s, j) {
          utilTablas.establecerBotonRecargaTabla($("#tab-lista-tareas"));
          utilTablas.establecerCabecerasBusquedaTabla($("#tab-lista-tareas"));

          if (primeraCarga) {
            primeraCarga = false;
            actualizarNumeroTareasNuevas();
          }
        }
      });
      var funcionCambio = function () {
        reCargar();
      };
      filtrosBusquedaFechas.cargar(idSeccion, funcionCambio);
    }
  }
  function actualizarNumeroTareasNuevas() {
    urlListarTareasNuevas = (typeof (urlListarTareasNuevas) === "undefined" ? "" : urlListarTareasNuevas);

    if (urlListarTareasNuevas !== "") {
      util.llamadaAjax(urlListarTareasNuevas, "POST", {}, true, function (d) {
        if (d.length > 0) {
          $("#btn-ver-tareas").append('<span class="label label-danger">' + d.length + '</span>');
        } else {
          $("#btn-ver-tareas").find(".label-danger").remove();
        }
      });
    }
  }

  //Público
  function mostrar()/* - */ {
    $("div[id^=sec-tareas-]").hide();
    utilTablas.recargarDatosTabla($("#tab-lista-tareas"));
    $("#sec-tareas-lista").show();
  }
  function reCargar()/* - */ {
    $("#tab-lista-tareas").DataTable().ajax.reload();
  }

  function actualizarRealizacion(elemento) {
    urlActualizarRealizacion = (typeof (urlActualizarRealizacion) === "undefined" ? "" : urlActualizarRealizacion);

    if (urlActualizarRealizacion !== "") {
      var idTarea = $(elemento).data("id");
      var realizado = $(elemento).is(':checked');
      util.llamadaAjax(urlActualizarRealizacion.replace("/0", "/" + idTarea) + "?realizado=" + realizado, "POST", {}, true, function () {
        actualizarNumeroTareasNuevas();
      });
    }
  }

  return {
    mostrar: mostrar,
    reCargar: reCargar,
    actualizarRealizacion: actualizarRealizacion
  };
}());