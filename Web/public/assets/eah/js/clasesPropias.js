$(document).ready(function () {
  cargarLista();
  cargarFormularioComentarios();
  cargarFormularioConfirmacionClase();
});
function cargarLista() {
  urlListar = (typeof (urlListar) === "undefined" ? "" : urlListar);
  estadosClase = (typeof (estadosClase) === "undefined" ? "" : estadosClase);
  usuarioActualEsAlumno = (typeof (usuarioActualEsAlumno) === "undefined" ? false : usuarioActualEsAlumno);
  estadoClaseProgramada = (typeof (estadoClaseProgramada) === "undefined" ? "" : estadoClaseProgramada);

  if (urlListar !== "" && estadosClase !== "" && usuarioActualEsAlumno !== "" && estadoClaseProgramada !== "") {
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
      order: [[1, "asc"]],
      rowId: 'id',
      columns: [
        {data: "", name: "", orderable: false, "searchable": false, render: function (data, type, row, meta) {
            return meta.row + meta.settings._iDisplayStart + 1;
          }, "className": "text-center not-mobile"},
        {data: "fechaInicio", name: "fechaInicio", render: function (e, t, d, m) {
            return (d.estado === estadoClaseProgramada ? '' : '<b>Fecha:</b> ' + (d.fechaConfirmacionProfesorAlumno !== null ? formatoFecha(d.fechaConfirmacionProfesorAlumno) : formatoFecha(d.fechaInicio) + ' - De ' + formatoFecha(d.fechaInicio, false, true) + ' a ' + formatoFecha(d.fechaFin, false, true)) + '<br/>')
                    + '<b>Duración:</b> ' + formatoHora(d.duracion) + '<br/>'
                    + (usuarioActualEsAlumno ? '<b>Profesor:</b> ' + (d.idProfesor !== null && d.nombreProfesor !== null && d.nombreProfesor !== '' ? d.nombreProfesor + ' ' + d.apellidoProfesor : 'Sin profesor asignado') : '<b>Alumno:</b> ' + (d.idAlumno !== null && d.nombreAlumno !== null && d.nombreAlumno !== '' ? d.nombreAlumno + ' ' + d.apellidoAlumno : ''));
          }},
        {data: "estado", name: "estado", render: function (e, t, d, m) {
            return (estadosClase[d.estado] !== undefined ?
                    '<span class="label ' + estadosClase[d.estado][1] + ' btn-estado">' + estadosClase[d.estado][0] + '</span>' : '');
          }, className: "text-center"},
        {data: "comentarioEntidad", name: "comentarioEntidad", render: function (e, t, d, m) {
            return (d.comentarioEntidad ? d.comentarioEntidad : '<div class="text-center"><a href="javascript:void(0);" onclick="abrirModalFormularioComentarios(' + d.id + ');" class="btn btn-primary btn-xs"><i class="fa fa-commenting-o"></i> ' + (usuarioActualEsAlumno ? 'Déjanos tus comentarios de esta clase' : 'Déjanos tu avance de clases') + '</a></div>');
          }, "className": "not-mobile"},
        {data: "comentarioAdministrador", name: "comentarioAdministrador", "className": "not-mobile"}
      ],
      initComplete: function (s, j) {
        establecerBotonRecargaTabla("tab-lista");
        establecerCabecerasBusquedaTabla("tab-lista");
      }
    });
  }
}

function abrirModalFormularioComentarios(idClase) {
  var tr = $("#" + idClase);
  var fila = $("#tab-lista").DataTable().row(tr);
  var datosFila = fila.data();
  $("#formulario-comentarios").find("input[name='idClase']").val(datosFila.id);
  $("#formulario-comentarios").find("input[name='idAlumno']").val(datosFila.idAlumno);
  $("#mod-comentarios").modal("show");
}
function cargarFormularioComentarios() {
  $("#formulario-comentarios").validate({
    rules: {
      comentario: {
        required: true
      }
    },
    submitHandler: function (f) {
      if (confirm("¿Está seguro que desea guardar los cambios de estos comentarios?")) {
        $.blockUI({message: "<h4>Guardando...</h4>"});
        var datos = procesarDatosFormulario(f);
        llamadaAjax($(f).attr("action"), "POST", datos, true,
                function (d) {
                  $("body").unblock({
                    onUnblock: function () {
                      agregarMensaje("exitosos", d["mensaje"], true);
                    }
                  });
                },
                function (d) {
                  $("#mod-comentarios").modal("hide");
                  $("#formulario-comentarios").find("textarea[name='comentario']").val("");
                  $("#tab-lista").DataTable().ajax.reload();
                },
                function (de) {
                  $("body").unblock({
                    onUnblock: function () {
                      var res = de["responseJSON"];
                      if (res["mensaje"]) {
                        agregarMensaje("errores", res["mensaje"], true);
                      } else {
                        agregarMensaje("errores", res[Object.keys(res)[0]][0], true);
                      }
                    }
                  });
                }
        );
      }
    },
    highlight: function () {
    },
    unhighlight: function () {
    },
    errorElement: "div",
    errorClass: "help-block-error",
    errorPlacement: function (error, element) {
      if (element.closest("div[class*=col-sm-]").length > 0) {
        element.closest("div[class*=col-sm-]").append(error);
      } else if (element.parent(".input-group").length) {
        error.insertAfter(element.parent());
      } else {
        error.insertAfter(element);
      }
    },
    onfocusout: false,
    onkeyup: false,
    onclick: false
  });
}

function cargarFormularioConfirmacionClase() {
  $("#formulario-confirmar-clase").validate({
    ignore: ":hidden",
    rules: {
      duracion: {
        required: true,
        validarDecimal: true,
        range: [(minHorasClase * 3600), (maxHorasClase * 3600)]
      },
      idAlumno: {
        required: true
      },
      codigoVerificacionClases: {
        required: true,
        number: true
      }
    },
    submitHandler: function (f) {
      if (confirm("¿Está seguro que desea realizar la confirmación de esta clase?")) {
        $.blockUI({message: "<h4>" + "Guardando datos...</h4>"});
        f.submit();
      }
    },
    highlight: function () {
    },
    unhighlight: function () {
    },
    errorElement: "div",
    errorClass: "help-block-error",
    errorPlacement: function (error, element) {
      if (element.closest("div[class*=col-sm-]").length > 0) {
        element.closest("div[class*=col-sm-]").append(error);
      } else if (element.parent(".input-group").length) {
        error.insertAfter(element.parent());
      } else {
        error.insertAfter(element);
      }
    },
    onfocusout: false,
    onkeyup: false,
    onclick: false
  });
  establecerCampoDuracion("duracion-clase");
}