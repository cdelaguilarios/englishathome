var misClases = {};
misClases = (function () {
  $(document).ready(function () {
    cargarLista();
  });

  function cargarLista() {
    urlListar = (typeof (urlListar) === "undefined" ? "" : urlListar);
    urlConfirmar = (typeof (urlConfirmar) === "undefined" ? "" : urlConfirmar);

    estadosClase = (typeof (estadosClase) === "undefined" ? "" : estadosClase);
    estadoClaseConfirmadaProfesor = (typeof (estadoClaseConfirmadaProfesor) === "undefined" ? "" : estadoClaseConfirmadaProfesor);
    estadoClaseConfirmadaProfesorAlumno = (typeof (estadoClaseConfirmadaProfesorAlumno) === "undefined" ? "" : estadoClaseConfirmadaProfesorAlumno);
    estadoClaseRealizada = (typeof (estadoClaseRealizada) === "undefined" ? "" : estadoClaseRealizada);

    if (urlListar !== "" && urlConfirmar !== "" && estadosClase !== "" && estadoClaseConfirmadaProfesor !== "" && estadoClaseConfirmadaProfesorAlumno !== "" && estadoClaseRealizada !== "") {
      $("#tab-lista-clases").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: urlListar,
          type: "POST",
          data: function (d) {
            d._token = $("meta[name=_token]").attr("content");
          }
        },
        autoWidth: false,
        responsive: true,
        order: [[1, "desc"]],
        rowId: 'id',
        columns: [
          {data: "", name: "", width: "2%", orderable: false, "searchable": false, render: function (e, t, d, m) {
              return m.row + m.settings._iDisplayStart + 1;
            }, "className": "text-center", responsivePriority: 0},
          {data: "fechaConfirmacion", name: "fechaConfirmacion", width: "20%", render: function (e, t, d, m) {
              var fechaConfirmacionIni = "";
              if (d.fechaConfirmacion !== null && !isNaN(Date.parse(d.fechaConfirmacion))) {
                fechaConfirmacionIni = new Date(d.fechaConfirmacion);
                fechaConfirmacionIni.setSeconds(fechaConfirmacionIni.getSeconds() - d.duracion);
              }
              d.estado = (d.estado === estadoClaseConfirmadaProfesorAlumno ? estadoClaseRealizada : d.estado);
              return '<b>Fecha:</b> ' + (d.fechaConfirmacion !== null ?
                      utilFechasHorarios.formatoFecha(d.fechaConfirmacion) + ' - De ' + utilFechasHorarios.formatoFecha(fechaConfirmacionIni, false, true) + ' a ' + utilFechasHorarios.formatoFecha(d.fechaConfirmacion, false, true) :
                      utilFechasHorarios.formatoFecha(d.fechaInicio) + ' - De ' + utilFechasHorarios.formatoFecha(d.fechaInicio, false, true) + ' a ' + utilFechasHorarios.formatoFecha(d.fechaFin, false, true)) + '<br/>'
                      + '<b>Profesor:</b> ' + d.nombreProfesor + ' ' + d.apellidoProfesor + '<br/>'
                      + '<b>Duración:</b> ' + utilFechasHorarios.formatoHora(d.duracion) + '<br/>'
                      + '<b>Estado:</b> ' + (estadosClase[d.estado] !== undefined ? '<span class="label ' + estadosClase[d.estado][1] + ' btn-estado">' + estadosClase[d.estado][0] + '</span>' : '');
            }, responsivePriority: 0},
          {data: "comentarioProfesor", name: "comentarioProfesor", render: function (e, t, d, m) {
              return d.comentarioProfesor !== null && d.comentarioProfesor !== "" ? d.comentarioProfesor : "-";
            }, "className": "desktop"},
          {data: "comentarioParaAlumno", name: "comentarioParaAlumno", render: function (e, t, d, m) {
              return d.comentarioParaAlumno !== null && d.comentarioParaAlumno !== "" ? d.comentarioParaAlumno : "-";
            }, "className": "desktop"},
          {data: "id", name: "id", orderable: false, "searchable": false, width: "5%", render: function (e, t, d, m) {
              d.estado = (d.estado === estadoClaseConfirmadaProfesorAlumno ? estadoClaseRealizada : d.estado);

              if (d.estado === estadoClaseRealizada) {
                return '<input type="checkbox" checked="checked" disabled>';
              } else if (d.estado === estadoClaseConfirmadaProfesor) {
                return '<a href="javascript:void(0);" onclick="misClases.confirmar(' + d.id + ');" class="btn btn-success btn-xs">' +
                        'Confirmar' +
                        '</a>';
              } else {
                return '';
              }
            }, "className": "text-center min-mobile-l"}
        ],
        initComplete: function (s, j) {
          utilTablas.establecerBotonRecargaTabla($("#tab-lista-clases"));
          utilTablas.establecerCabecerasBusquedaTabla($("#tab-lista-clases"));
        }
      });
    }
  }

  function confirmar(idClase) {
    if (confirm("¿Está seguro que desea confirmar la realización de la clase seleccionada?")) {
      urlConfirmar = (typeof (urlConfirmar) === "undefined" ? "" : urlConfirmar);
      if (urlConfirmar !== "") {
        util.llamadaAjax(urlConfirmar.replace("/0", "/" + idClase), "POST", {}, true, undefined,
                function () {
                  $("#tab-lista-clases").DataTable().ajax.reload();
                }
        );
      }
    } else {
      $("#tab-lista-clases").DataTable().ajax.reload();
    }
  }

  return {
    confirmar: confirmar
  };
}());