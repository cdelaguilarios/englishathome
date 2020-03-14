var listaPostulantes = {};
listaPostulantes = (function ()/* - */ {
  $(document).ready(function ()/* - */ {
    cargarLista();
  });

  function cargarLista()/* - */ {
    urlListar = (typeof (urlListar) === "undefined" ? "" : urlListar);
    urlPerfilProfesorPostulante = (typeof (urlPerfilProfesorPostulante) === "undefined" ? "" : urlPerfilProfesorPostulante);
    urlPerfilPostulante = (typeof (urlPerfilPostulante) === "undefined" ? "" : urlPerfilPostulante);
    urlEditarPostulante = (typeof (urlEditarPostulante) === "undefined" ? "" : urlEditarPostulante);
    urlEliminar = (typeof (urlEliminar) === "undefined" ? "" : urlEliminar);

    estados = (typeof (estados) === "undefined" ? "" : estados);
    estadosDisponibleCambio = (typeof (estadosDisponibleCambio) === "undefined" ? "" : estadosDisponibleCambio);
    estadoProfesorRegistrado = (typeof (estadoProfesorRegistrado) === "undefined" ? "" : estadoProfesorRegistrado);

    if (urlListar !== "" && urlPerfilProfesorPostulante !== "" && urlPerfilPostulante !== "" && urlEditarPostulante !== "" && urlEliminar !== "" && estados !== "" && estadosDisponibleCambio !== "" && estadoProfesorRegistrado !== "") {
      $("#tab-lista-postulantes").DataTable({
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
        dom: "<'row'<'col-sm-6'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'i>>" + "<'row'<'col-sm-12'tr>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        autoWidth: false,
        responsive: true,
        order: [[5, "desc"]],
        rowId: 'idEntidad',
        columns: [
          {data: "", name: "", orderable: false, "searchable": false, render: function (e, t, d, m) {
              return m.row + m.settings._iDisplayStart + 1;
            }, "className": "text-center not-mobile"},
          {data: "nombre", name: "entidad.nombre", render: function (e, t, d, m) {
              return '<a href="' + (urlPerfilPostulante.replace("/0", "/" + d.id)) + '">' + (d.nombre !== null ? d.nombre : "") + " " + (d.apellido !== null ? d.apellido : "") + '</a>';
            }},
          {data: "correoElectronico", name: "entidad.correoElectronico"},
          {data: "telefono", name: "entidad.telefono", render: function (e, t, d, m) {
              return  (d.telefono ? '<span class="text-info"><i class="fa  fa-mobile"></i> ' + util.incluirEnlaceWhatsApp(d.telefono) + '</span>' : '');
            }},
          {data: "estado", name: "entidad.estado", render: function (e, t, d, m) {
              var estado = '';
              if (estados[d.estado] !== undefined) {
                if (estadosDisponibleCambio[d.estado] !== undefined) {
                  estado = '<div class="sec-btn-editar-estado" data-idtabla="tab-lista-postulantes" data-idselestados="sel-estados" data-tipocambio="2">' +
                          '<a href="javascript:void(0);" class="btn-editar-estado" data-id="' + d.id + '" data-estado="' + d.estado + '">' +
                          '<span class="label ' + estados[d.estado][1] + ' btn-estado">' + estados[d.estado][0] + '</span>' +
                          '</a>' +
                          '</div>';
                } else {
                  estado = '<span class="label ' + estados[d.estado][1] + ' btn-estado">' + estados[d.estado][0] + '</span>' +
                          ((d.estado === estadoProfesorRegistrado) ? '<a href="' + (urlPerfilProfesorPostulante.replace("/0", "/" + d.id)) + '" title="Ver perfil del profesor" target="_blank" class="btn-perfil-relacion-entidad"><i class="fa fa-eye"></i></a>' : '');
                }
              }
              return estado;
            }, className: "text-center not-mobile"},
          {data: "fechaRegistro", name: "entidad.fechaRegistro", render: function (e, t, d, m) {
              return utilFechasHorarios.formatoFecha(d.fechaRegistro, true);
            }, className: "text-center not-mobile"},
          {data: "id", name: "entidad.id", orderable: false, "searchable": false, width: "5%", render: function (e, t, d, m) {
              return '<ul class="buttons">' +
                      '<li>' +
                      '<a href="' + (urlPerfilPostulante.replace("/0", "/" + d.id)) + '" title="Ver perfil"><i class="fa fa-eye"></i></a>' +
                      '</li>' +
                      '<li>' +
                      '<a href="' + (urlEditarPostulante.replace("/0", "/" + d.id)) + '" title="Editar datos"><i class="fa fa-pencil"></i></a>' +
                      '</li>' +
                      '<li>' +
                      '<a href="javascript:void(0);" title="Eliminar postulante" onclick="utilTablas.eliminarElemento(this, \'¿Está seguro que desea eliminar los datos de este postulante?\', \'tab-lista-postulantes\')" data-id="' + d.id + '" data-urleliminar="' + ((urlEliminar.replace("/0", "/" + d.id))) + '">' +
                      '<i class="fa fa-trash"></i>' +
                      '</a>' +
                      '</li>' +
                      '</ul>';
            }, className: "text-center"}
        ],
        initComplete: function (s, j) {
          utilTablas.establecerBotonRecargaTabla($("#tab-lista-postulantes"));
          utilTablas.establecerCabecerasBusquedaTabla($("#tab-lista-postulantes"));
        }
      });
    }
  }
}());