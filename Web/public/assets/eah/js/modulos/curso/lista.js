var listaCursos = {};
listaCursos = (function ()/* - */ {
  $(document).ready(function ()/* - */ {
    cargarLista();
  });

  function cargarLista()/* - */ {
    urlListar = (typeof (urlListar) === "undefined" ? "" : urlListar);
    urlEditar = (typeof (urlEditar) === "undefined" ? "" : urlEditar);
    urlEliminar = (typeof (urlEliminar) === "undefined" ? "" : urlEliminar);

    if (urlListar !== "" && urlEditar !== "" && urlEliminar !== "") {
      $("#tab-lista-cursos").DataTable({
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
        columns: [
          {data: "", name: "", orderable: false, "searchable": false, render: function (e, t, d, m) {
              return m.row + m.settings._iDisplayStart + 1;
            }, "className": "text-center not-mobile"},
          {data: "nombre", name: "nombre", render: function (e, t, d, m) {
              return '<a href="' + (urlEditar.replace("/0", "/" + d.id)) + '">' + d.nombre + '</a>';
            }},
          {data: "descripcion", name: "descripcion", render: function (e, t, d, m) {
              return ((d.descripcion.length > 200) ? (d.descripcion.substr(0, d.descripcion.lastIndexOf(' ', 197)) + '...') : d.descripcion);
            }},
          {data: "activo", name: "activo", orderable: false, "searchable": false, render: function (e, t, d, m) {
              return '<input type="checkbox"' + (d.activo.toString() === "1" ? ' checked="checked"' : '') + ' disabled="disabled"/>';
            }, className: "text-center"},
          {data: "id", name: "id", orderable: false, "searchable": false, width: "5%", render: function (e, t, d, m) {
              return '<ul class="buttons">' +
                      '<li><a href="' + (urlEditar.replace("/0", "/" + d.id)) + '" title="Editar datos"><i class="fa fa-pencil"></i></a>' +
                      '</li>' +
                      '<li>' +
                      '<a href="javascript:void(0);" title="Eliminar curso" onclick="utilTablas.eliminarElemento(this, \'¿Está seguro que desea eliminar los datos de este curso?\', \'tab-lista-cursos\')" data-id="' + d.id + '" data-urleliminar="' + ((urlEliminar.replace("/0", "/" + d.id))) + '">' +
                      '<i class="fa fa-trash"></i>' +
                      '</a>' +
                      '</li>' +
                      '</ul>';
            }, className: "text-center"}
        ],
        initComplete: function (s, j) {
          utilTablas.establecerBotonRecargaTabla($("#tab-lista-cursos"));
          utilTablas.establecerCabecerasBusquedaTabla($("#tab-lista-cursos"));
        }
      });
    }
  }
}());