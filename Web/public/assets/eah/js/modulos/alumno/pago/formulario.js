var formularioPagoAlumno = {};
formularioPagoAlumno = (function () {
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
        },
        costoXHoraClase: {
          required: true,
          validarDecimal: true
        },
        periodoClases: {
          required: true,
          validarEntero: true
        },
        pagoXHoraProfesor: {
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
    $("#motivo-pago-" + idSeccion).change(function () {
      if ($(this)[0].selectedIndex !== 0) {
        $("#sec-pago-datos-clases-" + idSeccion).hide();
      } else {
        $("#sec-pago-datos-clases-" + idSeccion).show();
      }
    });

    $("#monto-pago-" + idSeccion).change(function () {
      if (!$(this).valid()) {
        $("#usar-saldo-favor-pago-" + idSeccion).attr("checked", false);
        $("#usar-saldo-favor-pago-" + idSeccion).closest("label").removeClass("checked");
      }
    });
    $("#usar-saldo-favor-pago-" + idSeccion).click(function (e) {
      if ($("#monto-pago-" + idSeccion).valid() && saldoFavorTotal > 0) {
        var seleccionado = $("#usar-saldo-favor-pago-" + idSeccion).closest("label").hasClass("checked");

        $("#monto-pago-" + idSeccion).val(util.redondear(parseFloat($("#monto-pago-" + idSeccion).val()) + saldoFavorTotal * (seleccionado ? -1 : 1), 2));

        $(this).attr("checked", !seleccionado);
        if (seleccionado) {
          $("#usar-saldo-favor-pago-" + idSeccion).closest("label").removeClass("checked");
        } else {
          $("#usar-saldo-favor-pago-" + idSeccion).closest("label").addClass("checked");
        }
      } else {
        e.stopPropagation();
        return false;
      }
    });

    $("#btn-cargar-docentes-disponibles-pago-" + idSeccion).click(function () {
      var camposFormularioPago = $("#formulario-pago-" + idSeccion).find(":input, select").not(":hidden, input[name='pagoXHoraProfesor']");
      if (!camposFormularioPago.valid()) {
        return false;
      }

      docentesDisponibles.cargar("pago-" + idSeccion, function () {
        return $("#formulario-pago-" + idSeccion).serializeArray();
      }, function (datosDocente) {
        establecerDocente(idSeccion, datosDocente);
      });
    });
    $("#btn-cancelar-pago-" + idSeccion).click(function () {
      listaPagosAlumno.mostrar();
    });
  }
  function limpiarCampos(idSeccion, soloCamposDocente) {
    $("#formulario-pago-" + idSeccion).find("input[name='idDocente']").val("");
    $("#nombre-docente-pago-" + idSeccion).html("");

    if (!soloCamposDocente) {
      $("#formulario-pago-" + idSeccion).find(":input, select").each(function (i, e) {
        if (e.name !== "fecha" && e.name !== "costoXHoraClase" && e.name !== "periodoClases" && e.name !== "_token" && e.type !== "hidden") {
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
  }

  //Público
  var seccionesCargadas = [];
  var saldoFavorTotal = 0;
  function cargar(idSeccion) {
    if (!seccionesCargadas.includes(idSeccion)) {
      seccionesCargadas.push(idSeccion);

      cargarFormulario(idSeccion);
    }

    limpiarCampos(idSeccion);
    if (saldoFavorTotal > 0) {
      $("#lbl-usar-saldo-favor-pago-" + idSeccion).text("Utilizar saldo a favor total (S/. " + util.redondear(saldoFavorTotal, 2) + ")");
      $("#sec-pago-saldo-favor-" + idSeccion).show();
    }
    $("#sec-pago-datos-clases-" + idSeccion).show();
  }
  function establecerDocente(idSeccion, datosDocente) {
    if (datosDocente !== undefined && datosDocente !== null) {
      $("#formulario-pago-" + idSeccion).find("input[name='idDocente']").val(datosDocente.id);
      $("#nombre-docente-pago-" + idSeccion).html(datosDocente.id !== ''
              ? '<i class="fa flaticon-teach"></i> <b>' + datosDocente.nombreCompleto + '</b> ' +
              '<a href=' + ((datosDocente.tipo === tipoDocenteProfesor ? urlPerfilProfesor : urlPerfilPostulante).replace('/0', '/' + datosDocente.id)) + ' title="Ver perfil del profesor" target="_blank"><i class="fa fa-eye"></i></a>'
              : '');
      $("#sec-pago-datos-docente-" + idSeccion).show();
    }
  }
  function establecerSaldoFavor(saldoFavor) {
    saldoFavorTotal = saldoFavor;
  }
  function obtenerSaldoFavor() {
    return saldoFavorTotal;
  }

  return {
    cargar: cargar,
    establecerDocente: establecerDocente,
    establecerSaldoFavor: establecerSaldoFavor,
    obtenerSaldoFavor: obtenerSaldoFavor
  };
}());