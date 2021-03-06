var listaPostulantes = {};
listaPostulantes = (function () {
  $(document).ready(function () {
    cargarLista();
  });


  function cargarLista() {
    urlListar = (typeof (urlListar) === "undefined" ? "" : urlListar);
    urlPerfilProfesor = (typeof (urlPerfilProfesor) === "undefined" ? "" : urlPerfilProfesor);
    urlEditar = (typeof (urlEditar) === "undefined" ? "" : urlEditar);
    urlEliminar = (typeof (urlEliminar) === "undefined" ? "" : urlEliminar);

    estados = (typeof (estados) === "undefined" ? "" : estados);
    estadosDisponibleCambio = (typeof (estadosDisponibleCambio) === "undefined" ? "" : estadosDisponibleCambio);

    if (urlListar !== "" && urlPerfilProfesor !== "" && urlEditar !== "" && urlEliminar !== "" && estados !== "" && estadosDisponibleCambio !== "") {
      utilTablas.iniciarTabla($("#tab-lista-profesores"), {
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
        columns: [
          {data: "", name: "", orderable: false, "searchable": false, render: function (e, t, d, m) {
              return m.row + m.settings._iDisplayStart + 1;
            }, "className": "text-center", responsivePriority: 0},
          {data: "nombre", name: "entidad.nombre", render: function (e, t, d, m) {
              return '<a href="' + (urlPerfilProfesor.replace("/0", "/" + d.id)) + '">' + (d.nombre !== null ? d.nombre : "") + " " + (d.apellido !== null ? d.apellido : "") + '</a>';
            }, responsivePriority: 0},
          {data: "correoElectronico", name: "entidad.correoElectronico", "className": "min-tablet-l"},
          {data: "telefono", name: "entidad.telefono", render: function (e, t, d, m) {
              return  (d.telefono ? '<span class="text-info"><i class="fa  fa-mobile"></i> ' + util.incluirEnlaceWhatsApp(d.telefono) + '</span>' : '<span class="text-info">-</span>');
            }, "className": "min-tablet-l"},
          {data: "estado", name: "entidad.estado", render: function (e, t, d, m) {
              var estado = '';
              if (estados[d.estado] !== undefined) {
                if (estadosDisponibleCambio[d.estado] !== undefined) {
                  estado = '<div class="sec-btn-editar-estado" data-idtabla="tab-lista-profesores" data-idselestados="sel-estados" data-tipocambio="1">' +
                          '<a href="javascript:void(0);" class="btn-editar-estado" data-id="' + d.id + '" data-estado="' + d.estado + '">' +
                          '<span class="label ' + estados[d.estado][1] + ' btn-estado">' + estados[d.estado][0] + '</span>' +
                          '</a>' +
                          '</div>';
                } else {
                  estado = '<span class="label ' + estados[d.estado][1] + ' btn-estado">' + estados[d.estado][0] + '</span><br/>';
                }
              }
              return estado;
            }, className: "text-center min-tablet-p"},
          {data: "fechaRegistro", name: "entidad.fechaRegistro", render: function (e, t, d, m) {
              return utilFechasHorarios.formatoFecha(d.fechaRegistro, true);
            }, className: "text-center desktop"},
          {data: "id", name: "entidad.id", orderable: false, "searchable": false, width: "5%", render: function (e, t, d, m) {
              return '<ul class="buttons">' +
                      '<li>' +
                      '<a href="' + (urlPerfilProfesor.replace("/0", "/" + d.id)) + '" title="Ver perfil"><i class="fa fa-eye"></i></a>' +
                      '</li>' +
                      '<li>' +
                      '<a href="' + (urlEditar.replace("/0", "/" + d.id)) + '" title="Editar datos"><i class="fa fa-pencil"></i></a>' +
                      '</li>' +
                      '<li>' +
                      '<a href="javascript:void(0);" title="Eliminar profesor" onclick="utilTablas.eliminarElemento(this, \'¿Está seguro que desea eliminar los datos de este profesor?\', \'tab-lista-profesores\')" data-id="' + d.id + '" data-urleliminar="' + ((urlEliminar.replace("/0", "/" + d.id))) + '">' +
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
          {data: "descripcionPropia", name: "", orderable: false, "searchable": false, "className": "never"},
          {data: "ensayo", name: "", orderable: false, "searchable": false, "className": "never"},
          {data: "experienciaOtrosIdiomas", name: "", orderable: false, "searchable": false, "className": "never"},
          {data: "ultimosTrabajos", name: "", orderable: false, "searchable": false, "className": "never"},
          {data: "comentarioAdministrador", name: "", orderable: false, "searchable": false, "className": "never"},
          {data: "cuentasBancarias", name: "", orderable: false, "searchable": false, render: function (e, t, d, m) {
              return (d.cuentasBancarias ? d.cuentasBancarias.replaceAll("|", " ").replaceAll(";", " - ") : "");
            }, "className": "never"},
          {data: "", name: "", orderable: false, "searchable": false, render: function (e, t, d, m) {
              return (estados[d.estado] !== undefined ? estados[d.estado][0] : '');
            }, "className": "never"},
          {data: "numeroDocumento", name: "", orderable: false, "searchable": false, "className": "never"},
          {data: "", name: "", orderable: false, "searchable": false, render: function (e, t, d, m) {
              return utilFechasHorarios.formatoFecha(d.fechaNacimiento);
            }, "className": "never"},
          {data: "geoLatitud", name: "", orderable: false, "searchable": false, "className": "never"},
          {data: "geoLongitud", name: "", orderable: false, "searchable": false, "className": "never"},
          {data: "direccion", name: "", orderable: false, "searchable": false, "className": "never"},
          {data: "referenciaDireccion", name: "", orderable: false, "searchable": false, "className": "never"},
          {data: "numeroDepartamento", name: "", orderable: false, "searchable": false, "className": "never"},
          {data: "distrito", name: "", orderable: false, "searchable": false, "className": "never"},
          {data: "", name: "", orderable: false, "searchable": false, render: function (e, t, d, m) {
              return utilFechasHorarios.formatoFecha(d.fechaRegistro, true);
            }, "className": "never"}
          //----------------------------------------------------------------
        ],
        initComplete: function (s, j) {
          utilTablas.establecerBotonRecargaTabla($("#tab-lista-profesores"));
          utilTablas.establecerCabecerasBusquedaTabla($("#tab-lista-profesores"));
        }
      }, true, [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26]);
    }
  }
}());