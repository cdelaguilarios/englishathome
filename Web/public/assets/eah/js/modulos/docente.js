$(document).ready(function () {
  cargarLista();
  $("#bus-tipo, #bus-estado, #bus-sexo, #bus-curso, input[name='horario']").change(function () {
    $("#tab-lista").DataTable().ajax.reload();
  });
});

function cargarLista() {
  urlListar = (typeof (urlListar) === "undefined" ? "" : urlListar);
  estados = (typeof (estados) === "undefined" ? "" : estados);
  tipoDocenteProfesor = (typeof (tipoDocenteProfesor) === "undefined" ? "" : tipoDocenteProfesor);
  urlPerfilProfesor = (typeof (urlPerfilProfesor) === "undefined" ? "" : urlPerfilProfesor);
  urlEditarPostulante = (typeof (urlEditarPostulante) === "undefined" ? "" : urlEditarPostulante);

  if (urlListar !== "" && estados !== "" && tipoDocenteProfesor !== "" && urlPerfilProfesor !== "" && urlEditarPostulante !== "") {
    $("#tab-lista").DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: urlListar,
        type: "POST",
        data: function (d) {
          d._token = $("meta[name=_token]").attr("content");
          d.tipoDocente = $("#bus-tipo").val();
          d.estadoDocente = $("#bus-estado").val();
          d.sexoDocente = $("#bus-sexo").val();
          d.idCursoDocente = $("#bus-curso").val();
          d.horarioDocente = $("input[name='horario']").val();
        }
      },
      autoWidth: false,
      responsive: true,
      order: [[3, "desc"]],
      columns: [
        {data: "nombreCompleto", name: "nombreCompleto", render: function (e, t, d, m) {
            return '<a href=' + ((d.tipo === tipoDocenteProfesor ? urlPerfilProfesor : urlEditarPostulante).replace('/0', '/' + d.id)) + ' title="Ver perfil del profesor" target="_blank">' + d.nombreCompleto + '</a>';
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
      ],
      initComplete: function (s, j) {
        establecerBotonRecargaTabla("tab-lista");
      }
    });
  }
}


