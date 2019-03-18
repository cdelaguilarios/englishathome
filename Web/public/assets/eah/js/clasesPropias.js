$(document).ready(function () {
  cargarLista();
  cargarFormularioComentarios();
});
function cargarLista() {
  urlListar = (typeof (urlListar) === "undefined" ? "" : urlListar);
  estados = (typeof (estados) === "undefined" ? "" : estados);
  estadosClase = (typeof (estadosClase) === "undefined" ? "" : estadosClase);
  usuarioActualEsAlumno = (typeof (usuarioActualEsAlumno) === "undefined" ? false : usuarioActualEsAlumno);

  if (urlListar !== "" && estados !== "" && estadosClase !== "" && usuarioActualEsAlumno !== "") {
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
      order: [[1, "desc"]],
      columns: [
        {data: "", name: "", orderable: false, "searchable": false, "className": "text-center not-mobile",
          render: function (data, type, row, meta) {
            return meta.row + meta.settings._iDisplayStart + 1;
          }},
        {data: "fechaInicio", name: "fechaInicio", render: function (e, t, d, m) {
            return '<b>Fecha:</b> ' + formatoFecha(d.fechaInicio) + ' - De ' + formatoFecha(d.fechaInicio, false, true) + ' a ' + formatoFecha(d.fechaFin, false, true) + '<br/>'
                + '<b>Duración:</b> ' + formatoHora(d.duracion) + '<br/>'
                + (d.idHistorial !== null ?
                    '<b>Notificar:</b> ' + ' <i class="fa fa-check icon-notificar-clase"></i>' + '<br/>' : '')
                + (usuarioActualEsAlumno ? '<b>Profesor:</b> ' + (d.idProfesor !== null && d.nombreProfesor !== null && d.nombreProfesor !== '' ? d.nombreProfesor + ' ' + d.apellidoProfesor : 'Sin profesor asignado') : '<b>Alumno:</b> ' + (d.idAlumno !== null && d.nombreAlumno !== null && d.nombreAlumno !== '' ? d.nombreAlumno + ' ' + d.apellidoAlumno : ''));
          }},
        {data: "estado", name: "estado", render: function (e, t, d, m) {
            return (estadosClase[d.estado] !== undefined ?
                '<span class="label ' + estadosClase[d.estado][1] + ' btn-estado">' + estadosClase[d.estado][0] + '</span>' : '');
          }, className: "text-center"},
        {data: "comentarioEntidad", name: "comentarioEntidad", render: function (e, t, d, m) {
            return (d.comentarioEntidad ? d.comentarioEntidad : '<div class="text-center"><a href="javascript:void(0);" onclick="abrirModalRFormularioComentarios(this);" class="btn btn-primary btn-xs"><i class="fa fa-commenting-o"></i> Déjanos tus comentarios de esta clase</a></div>');
          }},
        {data: "comentarioAdministrador", name: "comentarioAdministrador"}
      ],
      initComplete: function (s, j) {
        establecerBotonRecargaTabla("tab-lista");
        establecerCabecerasBusquedaTabla("tab-lista");
      }
    });
  }
  $("#bus-estado").change(function () {
    $("#tab-lista").DataTable().ajax.reload();
  });
}

function abrirModalRFormularioComentarios(elemento) {
  var tr = $(elemento).closest("tr");
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