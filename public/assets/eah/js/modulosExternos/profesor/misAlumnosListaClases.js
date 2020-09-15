var misAlumnosListaClases = {};
misAlumnosListaClases = (function () {
  $(document).ready(function () {
    cargarLista();
  });

  function cargarLista() {
    urlListar = (typeof (urlListar) === "undefined" ? "" : urlListar);
    estadosClase = (typeof (estadosClase) === "undefined" ? "" : estadosClase);
    estadoClaseConfirmadaProfesorAlumno = (typeof (estadoClaseConfirmadaProfesorAlumno) === "undefined" ? "" : estadoClaseConfirmadaProfesorAlumno);
    estadoClaseRealizada = (typeof (estadoClaseRealizada) === "undefined" ? "" : estadoClaseRealizada);

    if (urlListar !== "" && estadosClase !== "" && estadoClaseConfirmadaProfesorAlumno !== "" && estadoClaseRealizada !== "") {
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
        order: [[1, "asc"]],
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
                      + '<b>Duración:</b> ' + utilFechasHorarios.formatoHora(d.duracion) + '<br/>'
                      + '<b>Estado:</b> ' + (estadosClase[d.estado] !== undefined ? '<span class="label ' + estadosClase[d.estado][1] + ' btn-estado">' + estadosClase[d.estado][0] + '</span>' : '');
            }, responsivePriority: 0},
          {data: "comentarioProfesor", name: "comentarioProfesor", render: function (e, t, d, m) {
              return (d.comentarioProfesor ?
                      d.comentarioProfesor !== "" ? d.comentarioProfesor : "-" :
                      '<div class="text-center">' +
                      '<a href="javascript:void(0);" onclick="formularioAvancesClase.abrirModal(' + d.id + ');" class="btn btn-primary btn-xs">' +
                      '<i class="fa fa-commenting-o"></i> Registrar avance' +
                      '</a>' +
                      '</div>');
            }, "className": "desktop"},
          {data: "comentarioParaProfesor", name: "comentarioParaProfesor", render: function (e, t, d, m) {
              return d.comentarioParaProfesor !== null && d.comentarioParaProfesor !== "" ? d.comentarioParaProfesor : "-";
            }, "className": "desktop"}
        ],
        initComplete: function (s, j) {
          utilTablas.establecerBotonRecargaTabla($("#tab-lista-clases"));
          utilTablas.establecerCabecerasBusquedaTabla($("#tab-lista-clases"));
        }
      });
    }
  }
}());