var listaInteresados = {};
listaInteresados = (function () {
  $(document).ready(function () {
    cargarLista();
  });

  function cargarLista() {
    urlListar = (typeof (urlListar) === "undefined" ? "" : urlListar);
    urlPerfilAlumnoInteresado = (typeof (urlPerfilAlumnoInteresado) === "undefined" ? "" : urlPerfilAlumnoInteresado);
    urlEditar = (typeof (urlEditar) === "undefined" ? "" : urlEditar);
    urlCotizar = (typeof (urlCotizar) === "undefined" ? "" : urlCotizar);
    urlEliminar = (typeof (urlEliminar) === "undefined" ? "" : urlEliminar);

    estados = (typeof (estados) === "undefined" ? "" : estados);
    origenes = (typeof (origenes) === "undefined" ? "" : origenes);
    estadosDisponibleCambio = (typeof (estadosDisponibleCambio) === "undefined" ? "" : estadosDisponibleCambio);
    estadoFichaCompleta = (typeof (estadoFichaCompleta) === "undefined" ? "" : estadoFichaCompleta);
    estadoAlumnoRegistrado = (typeof (estadoAlumnoRegistrado) === "undefined" ? "" : estadoAlumnoRegistrado);

    if (urlListar !== "" && urlPerfilAlumnoInteresado !== "" && urlEditar !== "" && urlCotizar !== "" && urlEliminar !== "" && estados !== "" && origenes !== "" && estadosDisponibleCambio !== "" && estadoFichaCompleta !== "" && estadoAlumnoRegistrado !== "") {
      utilTablas.iniciarTabla($("#tab-lista-interesados"), {
        processing: true,
        serverSide: true,
        ajax: {
          url: urlListar,
          type: "POST",
          data: function (d) {
            d._token = $("meta[name=_token]").attr("content");
            d.estado = $("#bus-estado").val();
          }
        },
        autoWidth: false,
        responsive: true,
        order: [[5, "desc"]],
        rowId: 'idEntidad',
        columns: [
          {data: "", name: "", orderable: false, "searchable": false, render: function (e, t, d, m) {
              return m.row + m.settings._iDisplayStart + 1;
            }, "className": "text-center", responsivePriority: 0},
          {data: "nombre", name: "entidad.nombre", render: function (e, t, d, m) {
              return '<a href="' + (urlEditar.replace("/0", "/" + d.id)) + '">' + (d.nombre !== null ? d.nombre : "") + " " + (d.apellido !== null ? d.apellido : "") + '</a>';
            }, responsivePriority: 0},
          {data: "consulta", name: "consulta", width: "35%", render: function (e, t, d, m) {
              var datos = (d.consulta !== null ? d.consulta : '');
              var cursoSel = (d.curso !== null && d.curso !== "" ? d.curso : (d.cursoInteres !== null && d.cursoInteres !== "" ? d.cursoInteres : ""));
              if (cursoSel !== "") {
                datos += (datos !== '' ? '<br/><br/>' : '') + '<b>Curso de interes:</b> ' + cursoSel;
              }
              if (d.origen !== null && d.origen !== "" && origenes[d.origen] !== undefined) {
                datos += (datos !== '' ? '<br/>' + (cursoSel !== "" ? '' : '<br/>') : '') + '<b>Origen:</b> ' + origenes[d.origen];
              }
              return datos;
            }, "className": "min-tablet-p"},
          {data: "correoElectronico", name: "entidad.correoElectronico", render: function (e, t, d, m) {
              return (d.correoElectronico !== null ? '<b>Correo electrónico:</b> ' + d.correoElectronico : '') +
                      (d.telefono !== null ? (d.correoElectronico !== null ? '<br/>' : '') + '<b>Teléfono:</b> ' + util.incluirEnlaceWhatsApp(d.telefono) : '');
            }, "className": "min-tablet-p"},
          {data: "estado", name: "entidad.estado", render: function (e, t, d, m) {
              var estado = '';
              if (estados[d.estado] !== undefined) {
                if (estadosDisponibleCambio[d.estado] !== undefined) {
                  estado = '<div class="sec-btn-editar-estado" data-idtabla="tab-lista-interesados" data-idselestados="sel-estados" data-tipocambio="2">' +
                          '<a href="javascript:void(0);" class="btn-editar-estado" data-id="' + d.id + '" data-estado="' + d.estado + '">' +
                          '<span class="label ' + estados[d.estado][1] + ' btn-estado">' + estados[d.estado][0] + '</span>' +
                          '</a>' +
                          '</div>';
                } else {
                  estado = '<span class="label ' + estados[d.estado][1] + ' btn-estado">' + estados[d.estado][0] + '</span>' +
                          ((d.estado === estadoFichaCompleta || d.estado === estadoAlumnoRegistrado) ? '<a href="' + (urlPerfilAlumnoInteresado.replace("/0", "/" + d.id)) + '" title="Ver perfil del alumno" target="_blank" class="btn-perfil-relacion-entidad"><i class="fa fa-eye"></i></a>' : '');
                }
              }
              return estado;
            }, className: "text-center min-tablet-l"},
          {data: "fechaRegistro", name: "entidad.fechaRegistro", render: function (e, t, d, m) {
              return utilFechasHorarios.formatoFecha(d.fechaRegistro, true);
            }, className: "text-center desktop"},
          {data: "id", name: "entidad.id", orderable: false, "searchable": false, width: "5%", render: function (e, t, d, m) {
              return '<ul class="buttons">' +
                      '<li>' +
                      '<a href="' + (urlEditar.replace("/0", "/" + d.id)) + '" title="Editar datos"><i class="fa fa-pencil"></i></a>' +
                      '</li>' +
                      '<li>' +
                      '<a href="' + (urlCotizar.replace("/0", "/" + d.id)) + '" title="Enviar cotización"><i class="fa fa-envelope"></i></a>' +
                      '</li>' +
                      '<li>' +
                      '<a href="javascript:void(0);" title="Eliminar interesado" onclick="utilTablas.eliminarElemento(this, \'¿Está seguro que desea eliminar los datos de esta persona interesada?\', \'tab-lista-interesados\')" data-id="' + d.id + '" data-urleliminar="' + ((urlEliminar.replace("/0", "/" + d.id))) + '">' +
                      '<i class="fa fa-trash"></i>' +
                      '</a>' +
                      '</li>' +
                      '</ul>';
            }, className: "text-center min-mobile-l"},
          //--------- Columnas ocultas solo para exportación excel ---------
          {data: "nombre", name: "", orderable: false, "searchable": false, "className": "never"},
          {data: "apellido", name: "", orderable: false, "searchable": false, "className": "never"},
          {data: "telefono", name: "", orderable: false, "searchable": false, "className": "never"},
          {data: "correoElectronico", name: "", orderable: false, "searchable": false, "className": "never"},
          {data: "consulta", name: "", orderable: false, "searchable": false, "className": "never"},
          {data: "", name: "", orderable: false, "searchable": false, render: function (e, t, d, m) {
              return (d.curso !== null && d.curso !== "" ? d.curso : (d.cursoInteres !== null && d.cursoInteres !== "" ? d.cursoInteres : ""));
            }, "className": "never"},
          {data: "origen", name: "", orderable: false, "searchable": false, "className": "never"},
          {data: "costoXHoraClase", name: "", orderable: false, "searchable": false, "className": "never"},
          {data: "comentarioAdicional", name: "", orderable: false, "searchable": false, "className": "never"},
          {data: "", name: "", orderable: false, "searchable": false, render: function (e, t, d, m) {
              return (estados[d.estado] !== undefined ? estados[d.estado][0] : '');
            }, "className": "never"},
          {data: "", name: "", orderable: false, "searchable": false, render: function (e, t, d, m) {
              return utilFechasHorarios.formatoFecha(d.fechaRegistro, true);
            }, "className": "never"}
          //----------------------------------------------------------------
        ],
        initComplete: function (s, j) {
          utilTablas.establecerBotonRecargaTabla($("#tab-lista-interesados"));
          utilTablas.establecerCabecerasBusquedaTabla($("#tab-lista-interesados"));
        }
      }, true, [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17]/*Indices de las columnas que se exportarán al excel*/);
    }
  }
}());
