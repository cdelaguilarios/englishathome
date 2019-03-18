$(document).ready(function () {
  cargarLista();
});
function cargarLista() {
  urlListar = (typeof (urlListar) === "undefined" ? "" : urlListar);
  estados = (typeof (estados) === "undefined" ? "" : estados);

  if (urlListar !== "" && estados !== "") {
    $("#tab-lista").DataTable({
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
      order: [[4, "desc"]],
      columns: [
        {data: "", name: "", orderable: false, "searchable": false, "className": "text-center not-mobile",
          render: function (data, type, row, meta) {
            return meta.row + meta.settings._iDisplayStart + 1;
          }},
        {data: "nombre", name: "entidad.nombre", render: function (e, t, d, m) {
            return (d.nombre !== null ? d.nombre : "") + " " + (d.apellido !== null ? d.apellido : "");
          }},
        {data: "email", name: "usuario.email"},
        {data: "rol", name: "usuario.rol", render: function (e, t, d, m) {
            return roles[d.rol];
          }},
        {data: "estado", name: "entidad.estado", render: function (e, t, d, m) {
            if (estados[d.estado] !== undefined && estadosCambio[d.estado] !== undefined) {
              return '<div class="sec-btn-editar-estado"><a href="javascript:void(0);" class="btn-editar-estado" data-id="' + d.id + '" data-estado="' + d.estado + '"><span class="label ' + estados[d.estado][1] + ' btn-estado">' + estados[d.estado][0] + '</span></a></div>';
            } else if (estados[d.estado] !== undefined) {
              return '<span class="label ' + estados[d.estado][1] + ' btn-estado">' + estados[d.estado][0] + '</span>';
            } else {
              return "";
            }
          }, className: "text-center"},
        {data: "fechaRegistro", name: "entidad.fechaRegistro", render: function (e, t, d, m) {
            return formatoFecha(d.fechaRegistro, true);
          }, className: "text-center"}
      ],
      initComplete: function (s, j) {
        establecerBotonRecargaTabla("tab-lista");
      }
    });
  }
}