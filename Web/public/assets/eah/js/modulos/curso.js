$(document).ready(function () {
  cargarLista();
});

//Lista
function cargarLista() {
  urlListar = (typeof (urlListar) === "undefined" ? "" : urlListar);
  urlEditar = (typeof (urlEditar) === "undefined" ? "" : urlEditar);
  urlEliminar = (typeof (urlEliminar) === "undefined" ? "" : urlEliminar);

  if (urlListar !== "" && urlEditar !== "" && urlEliminar !== "") {
    $("#tab-lista").DataTable({
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
      order: [[0, "desc"]],
      columns: [
        {data: "nombre", name: "nombre", render: function (e, t, d, m) {
            return d.nombre;
          }},
        {data: "descripcion", name: "descripcion", render: function (e, t, d, m) {
            return d.descripcion;
          }},
        {data: "id", name: "id", orderable: false, "searchable": false, width: "5%", render: function (e, t, d, m) {
            return '<ul class="buttons">' +
                '<a href="' + (urlEditar.replace("/0", "/" + d.id)) + '" title="Editar datos"><i class="fa fa-pencil"></i></a>' +
                '</li>' +
                '<li>' +
                '<a href="javascript:void(0);" title="Eliminar curso" onclick="eliminarElemento(this, \'¿Está seguro que desea eliminar los datos de este curso?\', \'tab-lista\')" data-id="' + d.id + '" data-urleliminar="' + ((urlEliminar.replace("/0", "/" + d.id))) + '">' +
                '<i class="fa fa-trash"></i>' +
                '</a>' +
                '</li>' +
                '</ul>';
          }, className: "text-center"}
      ]
    });
  }
}