var formularioInteresado = {};
formularioInteresado = (function ()/* - */ {
  $(document).ready(function ()/* - */ {
    cargarFormulario();
  });

  function cargarFormulario()/* - */ {
    $("#formulario-interesado").validate({
      ignore: ":hidden",
      rules: {
        nombre: {
          required: true,
          validarAlfabetico: true
        },
        apellido: {
          required: true,
          validarAlfabetico: true
        },
        telefono: {
          required: true
        },
        correoElectronico: {
          required: true,
          email: true
        },
        idCurso: {
          required: true
        },
        costoXHoraClase: {
          required: true,
          validarDecimal: true
        }
      },
      submitHandler: function (f) {
        var mensajeConfirmacion = "¿Está seguro que desea registrar a esta persona interesada como un nuevo alumno?";
        if ($("input[name='registrarComoAlumno']").val() !== "1") {
          mensajeConfirmacion = ($("#btn-guardar").text().trim() === "Guardar"
                  ? "¿Está seguro que desea guardar los cambios de los datos de la persona interesada?"
                  : "¿Está seguro que desea registrar los datos de esta persona interesada?");
        }
        if (confirm(mensajeConfirmacion)) {
          $.blockUI({message: "<h4>" + ($("#btn-guardar").text().trim() === "Guardar" ? "Guardando" : "Registrando") + " datos...</h4>"});
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
    $("#btn-registrar-alumno").click(function () {
      $("input[name='registrarComoAlumno']").val("1");
      $("#formulario-interesado").submit();
    });
    $("#btn-guardar").click(function () {
      $("input[name='registrarComoAlumno']").val("0");
      $("#formulario-interesado").submit();
    });
  }
}());