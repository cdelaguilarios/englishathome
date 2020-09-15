var docentesDisponibles = {};
docentesDisponibles = (function () {
  function mostrar(funcionConfirmarDocente) {
    if ($.fn.DataTable.isDataTable("#tab-lista-docentes")) {
      utilTablas.recargarDatosTabla($("#tab-lista-docentes"));
    } else {
      urlListar = (typeof (urlListar) === "undefined" ? "" : urlListar);
      urlPerfilProfesor = (typeof (urlPerfilProfesor) === "undefined" ? "" : urlPerfilProfesor);
      urlPerfilPostulante = (typeof (urlPerfilPostulante) === "undefined" ? "" : urlPerfilPostulante);
      estadosDocente = (typeof (estadosDocente) === "undefined" ? "" : estadosDocente);

      if (urlListar !== "" && urlPerfilProfesor !== "" && urlPerfilPostulante !== "" && estadosDocente !== "") {
        $("#tab-lista-docentes").DataTable({
          processing: true,
          serverSide: true,
          ajax: {
            url: urlListar,
            type: "POST",
            data: function (d) {
              d._token = $("meta[name=_token]").attr("content");
              d.tipoDocente = $("#tipo-docente-disponible").val();
              d.estadoDocente = $("#estado-docente-disponible").val();
              d.sexoDocente = $("#sexo-docente-disponible").val();
              d.idCursoDocente = $("#id-curso-docente-disponible").val();
            }
          },
          autoWidth: false,
          pageLength: 10,
          rowId: 'idEntidad',
          columns: [
            {data: "nombreCompleto", name: "nombreCompleto", render: function (e, t, d, m) {
                return '<a href=' + ((d.tipo === tipoDocenteProfesor ? urlPerfilProfesor : urlPerfilPostulante).replace('/0', '/' + d.id)) + ' title="Ver perfil del docente" target="_blank">' + d.nombreCompleto + '</a>';
              }},
            {data: "estado", name: "entidad.estado", render: function (e, t, d, m) {
                return (estadosDocente[d.estado] !== undefined ? '<span class="label ' + estadosDocente[d.estado][1] + ' btn-estado">' + estadosDocente[d.estado][0] + '</span>' : '');
              }, className: "text-center"},
            {data: "id", name: "id", orderable: false, "searchable": false, width: "5%", className: "text-center"}
          ],
          createdRow: function (r, d, i) {
            $("td", r).eq(2).html('<input type="radio" name="idDocenteDisponible" value="' + d.id + '"/>');
          },
          initComplete: function (s, j) {
            utilTablas.establecerBotonRecargaTabla($("#tab-lista-docentes"));
            utilTablas.establecerCabecerasBusquedaTabla($("#tab-lista-docentes"));
          }
        });
        $("#tipo-docente-disponible" + ", #estado-docente-disponible" + ", #sexo-docente-disponible" + ", #id-curso-docente-disponible").change(function () {
          utilTablas.recargarDatosTabla($("#tab-lista-docentes"));
        });

        $("#btn-confirmar-docente-disponible").click(function () {
          if (urlPerfilProfesor !== "" && urlPerfilPostulante !== "") {
            var docenteDisponible = $("input[name='idDocenteDisponible']:checked");
            if (docenteDisponible.length > 0 && funcionConfirmarDocente) {
              var tr = $("#tab-lista-docentes").find("#" + docenteDisponible.val())[0];
              var fila = $("#tab-lista-docentes").DataTable().row(tr);
              var datosDocente = fila.data();
              funcionConfirmarDocente(datosDocente);
            }
          }
          $("#mod-docentes-disponibles").modal("hide");
        });
      }
    }
    $("#mod-docentes-disponibles").modal("show");
  }

  return {
    mostrar: mostrar
  };
}());

