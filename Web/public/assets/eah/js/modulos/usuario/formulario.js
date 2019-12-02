var formularioUsuario = {};
formularioUsuario = (function ()/* - */ {
  $.validator.addMethod("validarPassword", validarPassword, "Este campo es obligatorio.");
  function validarPassword(value, element, param) {
    return (($("#modo-edicion") === undefined && value.trim() !== "") ||
            ($("#modo-edicion") !== undefined && ($("#modo-edicion").val() === "1" || ($("#modo-edicion").val() === "0" && value.trim() !== ""))));
  }

  $(document).ready(function ()/* - */ {
    cargarFormulario();
  });

  function cargarFormulario()/* - */ {
    $("#formulario-usuario").validate({
      ignore: ":hidden",
      rules: {
        nombre: {
          validarAlfabetico: true
        },
        apellido: {
          validarAlfabetico: true
        },
        email: {
          required: true,
          email: true
        },
        imagenPerfil: {
          validarImagen: true
        },
        password: {
          validarPassword: true
        },
        password_confirmation: {
          validarPassword: true,
          equalTo: "#password"
        }
      },
      submitHandler: function (f) {
        if (confirm($("#btn-guardar").text().trim() === "Guardar"
                ? "¿Está seguro que desea guardar los cambios de los datos del usuario?"
                : "¿Está seguro que desea registrar los datos de este usuario?")) {
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
  }
}());
