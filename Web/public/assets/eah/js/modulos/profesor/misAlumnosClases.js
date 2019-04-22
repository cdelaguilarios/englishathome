$(document).ready(function () {
  cargarLista();
  cargarFormularioAvancesClase();
  cargarFormularioConfirmacionClase();
});
function cargarLista() {
  urlListar = (typeof (urlListar) === "undefined" ? "" : urlListar);
  estadosClase = (typeof (estadosClase) === "undefined" ? "" : estadosClase);
  estadoClaseProgramada = (typeof (estadoClaseProgramada) === "undefined" ? "" : estadoClaseProgramada);
  estadoClasePendienteConfirmar = (typeof (estadoClasePendienteConfirmar) === "undefined" ? "" : estadoClasePendienteConfirmar);

  if (urlListar !== "" && estadosClase !== "" && estadoClaseProgramada !== "" && estadoClasePendienteConfirmar !== "") {
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
        {data: "", name: "", orderable: false, "searchable": false, render: function (e, t, d, m) {
            return m.row + m.settings._iDisplayStart + 1;
          }, "className": "text-center not-mobile"},
        {data: "fechaInicio", name: "fechaInicio", width: "20%", render: function (e, t, d, m) {
            var fechaConfirmacionIni = "";
            if (d.fechaConfirmacion !== null && !isNaN(Date.parse(d.fechaConfirmacion))) {
              fechaConfirmacionIni = new Date(d.fechaConfirmacion);
              fechaConfirmacionIni.setSeconds(fechaConfirmacionIni.getSeconds() - d.duracion);
            }
            return (d.estado === estadoClaseProgramada ? '' :
                    '<b>Fecha:</b> ' + (d.fechaConfirmacion !== null ?
                            formatoFecha(d.fechaConfirmacion) + ' - De ' + formatoFecha(fechaConfirmacionIni, false, true) + ' a ' + formatoFecha(d.fechaConfirmacion, false, true) :
                            formatoFecha(d.fechaInicio) + ' - De ' + formatoFecha(d.fechaInicio, false, true) + ' a ' + formatoFecha(d.fechaFin, false, true)) + '<br/>')
                    + '<b>Duración:</b> ' + formatoHora(d.duracion) + '<br/>'
                    + '<b>Estado:</b> ' + (estadosClase[d.estado] !== undefined ? '<span class="label ' + estadosClase[d.estado][1] + ' btn-estado">' + estadosClase[d.estado][0] + '</span>' : '');
          }},
        {data: "", name: "", orderable: false, "searchable": false, render: function (e, t, d, m) {
            return (d.estado === estadoClaseProgramada || d.estado === estadoClasePendienteConfirmar ? '<div class="text-center"><a href="javascript:void(0);" onclick="abrirModalFormularioConfirmacionClase(' + d.id + ');" class="btn btn-success btn-xs"><i class="fa fa-check"></i> Confirmar clase</a></div>' : '');
          }, "className": "text-center"},
        {data: "comentarioProfesor", name: "comentarioProfesor", render: function (e, t, d, m) {
            return (d.comentarioProfesor ?
                    d.comentarioProfesor :
                    (d.estado === estadoClaseProgramada || d.estado === estadoClasePendienteConfirmar ? '' : '<div class="text-center"><a href="javascript:void(0);" onclick="abrirModalFormularioAvancesClase(' + d.id + ');" class="btn btn-primary btn-xs"><i class="fa fa-commenting-o"></i> Registrar avance</a></div>'));
          }, "className": "not-mobile"},
        {data: "comentarioParaProfesor", name: "comentarioParaProfesor", "className": "not-mobile"}
      ],
      initComplete: function (s, j) {
        establecerBotonRecargaTabla("tab-lista");
        establecerCabecerasBusquedaTabla("tab-lista");
      }
    });
  }
}

function abrirModalFormularioAvancesClase(idClase) {
  var tr = $("#" + idClase);
  var datosFila = $("#tab-lista").DataTable().row(tr).data();
  $("#formulario-avances-clase").find("textarea[name='comentario']").val("");
  $("#formulario-avances-clase").find("input[name='idClase']").val(datosFila.id);
  $("#mod-avances-clase").modal("show");
}
function cargarFormularioAvancesClase() {
  $("#formulario-avances-clase").validate({
    rules: {
      comentario: {
        required: true
      }
    },
    submitHandler: function (f) {
      if (confirm("¿Está seguro que desea guardar los avances de la clase seleccionada?")) {
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
                  $("#mod-avances-clase").modal("hide");
                  $("#formulario-avances-clase").find("textarea[name='comentario']").val("");
                  $("#formulario-avances-clase").find("input[name='idClase']").val("");
                  $("#tab-lista").DataTable().ajax.reload();
                },
                function (de) {
                  $("body").unblock({
                    onUnblock: function () {
                      var res = de["responseJSON"];
                      agregarMensaje("errores", (res["mensaje"] ? res["mensaje"] : res[Object.keys(res)[0]][0]), true);
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
      if (element.closest("div[class*=col-sm-]").length > 0)
        element.closest("div[class*=col-sm-]").append(error);
      else if (element.parent(".input-group").length)
        error.insertAfter(element.parent());
      else
        error.insertAfter(element);
    },
    onfocusout: false,
    onkeyup: false,
    onclick: false
  });
}

function abrirModalFormularioConfirmacionClase(idClase) {
  var tr = $("#" + idClase);
  var datosFila = $("#tab-lista").DataTable().row(tr).data();
  $("#formulario-confirmar-clase").find("input[name='codigoVerificacion']").val("");
  $("#formulario-confirmar-clase").find("textarea[name='comentario']").val("");
  $("#formulario-confirmar-clase").find("input[name='idClase']").val(datosFila.id);
  $("#mod-confirmar-clase").modal("show");
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
      if (element.closest("div[class*=col-sm-]").length > 0)
        element.closest("div[class*=col-sm-]").append(error);
      else if (element.parent(".input-group").length)
        error.insertAfter(element.parent());
      else
        error.insertAfter(element);
    },
    onfocusout: false,
    onkeyup: false,
    onclick: false
  });
  $("#btn-cambiar-duracion").click(function () {
    duracionProximaClase = (typeof (duracionProximaClase) === "undefined" ? "" : duracionProximaClase);

    $(this).hide();
    $("#sec-duracion").hide();
    $("#duracion-clase").val(duracionProximaClase);
    $("#sec-cambio-duracion").show();
  });
  establecerCampoDuracion("duracion-clase");
}