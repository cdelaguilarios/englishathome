var docentesDisponibles = {};
docentesDisponibles = (function ()/* - */ {
  function cargar(seccion, funcionObtenerDatosAdicionales, funcionConfirmarDocente)/* - */ {
    if ($.fn.DataTable.isDataTable("#tab-lista-docentes-" + seccion)) {
      utilTablas.recargarDatosTabla($("#tab-lista-docentes-" + seccion));
    } else {
      urlPerfilProfesor = (typeof (urlPerfilProfesor) === "undefined" ? "" : urlPerfilProfesor);
      estadosProfesor = (typeof (estadosProfesor) === "undefined" ? "" : estadosProfesor);

      urlListar = (typeof (urlListar) === "undefined" ? "" : urlListar);
      if (urlListar !== "" && urlPerfilProfesor !== "" && estadosProfesor !== "") {
        $("#tab-lista-docentes-" + seccion).DataTable({
          processing: true,
          serverSide: true,
          ajax: {
            url: urlListar,
            type: "POST",
            data: function (d) {
              d.tipoDocente = $("#tipo-docente-disponible-" + seccion).val();
              d.estadoDocente = $("#estado-docente-disponible-" + seccion).val();
              d.sexoDocente = $("#sexo-docente-disponible-" + seccion).val();
              d.idCursoDocente = $("#id-curso-docente-disponible-" + seccion).val();
              if (funcionObtenerDatosAdicionales) {
                var datosAdicionales = funcionObtenerDatosAdicionales();
                $(datosAdicionales).each(function (i, o) {
                  d[o.name] = o.value;
                });
              }
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
            $("td", r).eq(2).html('<input type="radio" name="idDocenteDisponible' + util.letraCapital(seccion) + '" value="' + d.id + '"/>');
          },
          initComplete: function (s, j) {
            utilTablas.establecerBotonRecargaTabla($("#tab-lista-docentes-" + seccion));
            utilTablas.establecerCabecerasBusquedaTabla($("#tab-lista-docentes-" + seccion));
          }
        });
        $("#tipo-docente-disponible-" + seccion + ", #estado-docente-disponible-" + seccion + ", #sexo-docente-disponible-" + seccion + ", #id-curso-docente-disponible-" + seccion).change(function () {
          utilTablas.recargarDatosTabla($("#tab-lista-docentes-" + seccion));
        });

        $("#btn-confirmar-docente-disponible-" + seccion).click(function () {
          if (urlPerfilProfesor !== "" && urlPerfilPostulante !== "") {
            var docenteDisponible = $("input[name='idDocenteDisponible" + util.letraCapital(seccion) + "']:checked");
            if (docenteDisponible.length > 0 && funcionConfirmarDocente) {
              var tr = $("#tab-lista-docentes-" + seccion).find("#" + docenteDisponible.val())[0];
              var fila = $("#tab-lista-docentes-" + seccion).DataTable().row(tr);
              var datosDocente = fila.data();
              funcionConfirmarDocente(datosDocente);
            }
          }
          $("#mod-docentes-disponibles-" + seccion).modal("hide");
        });
      }
    }
    $("#mod-docentes-disponibles-" + seccion).modal("show");
  }

  return {
    cargar: cargar
  };
}());

