$(document).ready(function () {
  cargarLista();
});

function cargarLista() {
  urlListar = (typeof (urlListar) === "undefined" ? "" : urlListar);
  urlPerfil = (typeof (urlPerfil) === "undefined" ? "" : urlPerfil);
  urlEditar = (typeof (urlEditar) === "undefined" ? "" : urlEditar);
  estados = (typeof (estados) === "undefined" ? "" : estados);

  if (urlListar !== "" && urlPerfil !== "" && urlEditar !== "" && estados !== "") {
    $("#tab-lista").DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: urlListar,
        type: "POST",
        data: function (d) {
          d._token = $("meta[name=_token]").attr("content");
          d.tipoDocente = $("#bus-tipo-docente").val();
          d.sexoDocente = $("#bus-sexo-docente").val();
          d.idCursoDocente = $("#bus-id-curso-docente").val();
          d.fechaInicio = $("#bus-fecha-inicio").val();
          d.fechaFin = $("#bus-fecha-fin").val();
        }
      },
      autoWidth: false,
      responsive: true,
      order: [[3, "desc"]],
      columns: [
        {data: "nombre", name: "entidad.nombre", render: function (e, t, d, m) {
            return '<a href="' + (urlPerfil.replace("/0", "/" + d.id)) + '">' + (d.nombre !== null ? d.nombre : "") + " " + (d.apellido !== null ? d.apellido : "") + '</a>';
          }},
        {data: "correoElectronico", name: "entidad.correoElectronico"},
        {data: "estado", name: "entidad.estado", render: function (e, t, d, m) {
            if (estados[d.estado] !== undefined) {
              return '<span class="label ' + estados[d.estado][1] + ' btn-estado">' + estados[d.estado][0] + '</span>';
            } else {
              return "";
            }
          }, className: "text-center"},
        {data: "fechaRegistro", name: "entidad.fechaRegistro", render: function (e, t, d, m) {
            return formatoFecha(d.fechaRegistro, true);
          }, className: "text-center"},
        {data: "id", name: "id", orderable: false, "searchable": false, width: "5%", render: function (e, t, d, m) {
            return '<ul class="buttons">' +
                '<li>' +
                '<a href="' + (urlPerfil.replace("/0", "/" + d.id)) + '" title="Ver perfil"><i class="fa fa-eye"></i></a>' +
                '</li>' +
                '</ul>';
          }, className: "text-center"}
      ]
    });

    establecerCalendario("bus-fecha-inicio", true, false, false, busquedaCambio);
    establecerCalendario("bus-fecha-fin", true, false, false, busquedaCambio);
    $("#bus-tipo-docente, #bus-sexo-docente, #bus-id-curso-docente").change(busquedaCambio);
  }
}

function busquedaCambio() {
  $("#tab-lista").DataTable().ajax.reload();
}