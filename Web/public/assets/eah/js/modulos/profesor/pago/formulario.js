var formularioPagoProfesor = {};
formularioPagoProfesor = (function () {
  //Privado
  function cargarFormulario(idSeccion) {
    $("#formulario-pago-" + idSeccion).validate({
      ignore: ":hidden",
      rules: {
        fecha: {
          required: true,
          validarFecha: true
        },
        imagenComprobante: {
          validarImagen: true
        },
        monto: {
          required: true,
          validarDecimal: true
        }
      },
      submitHandler: function (f) {
        if (confirm("¿Está seguro que desea " +
                ($("#formulario-pago-" + idSeccion).find("input[name='seccionActualizar']").val() === "1" ? "actualizar" : "registrar") +
                " los datos de este pago?")) {
          $.blockUI({message: "<h4>Registrando datos...</h4>"});
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
    utilFechasHorarios.establecerCalendario($("#fecha-pago-" + idSeccion), false, false, false);

    $("#btn-cancelar-pago-" + idSeccion).click(function () {
      listaPagosProfesor.mostrar();
    });
  }
  function limpiarCampos(idSeccion) {
      $("#formulario-pago-" + idSeccion).find(":input, select").each(function (i, e) {
        if (e.name !== "fecha" && e.name !== "motivo" && e.name !== "_token" && e.type !== "hidden") {
          if ($(e).is("select")) {
            $(e).prop("selectedIndex", 0);
          } else if ($(e).is(":checkbox")) {
            $(e).attr("checked", false);
            $(e).closest("label").removeClass("checked");
          } else {
            e.value = "";
          }
        }
      });
      $("#fecha-pago-" + idSeccion).datepicker("setDate", "today");
      $("form .help-block-error").remove();
  }

  //Público
  var seccionesCargadas = [];
  function cargar(idSeccion) {
    if (!seccionesCargadas.includes(idSeccion)) {
      seccionesCargadas.push(idSeccion);

      cargarFormulario(idSeccion);
    }

    limpiarCampos(idSeccion);
  }

  return {
    cargar: cargar
  };
}());