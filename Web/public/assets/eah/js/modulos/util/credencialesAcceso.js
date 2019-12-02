var imagenPerfil = {};
imagenPerfil = (function ()/* - */ {
  window.addEventListener("load", esperarCargaJquery, false);
  function esperarCargaJquery()/* - */ {
    ((window.jQuery && jQuery.ui) ? cargarSeccion() : window.setTimeout(esperarCargaJquery, 100));
  }

  function cargarSeccion()/* - */ {
    $("#btn-editar-credenciales-acceso").click(function () {
      $("#mod-editar-credenciales-acceso").modal("show");
    });

    $("#formulario-editar-credenciales-acceso").validate({
      ignore: ":hidden",
      rules: {
        email: {
          required: true,
          email: true
        },
        password_confirmation: {
          equalTo: "#password"
        }
      },
      submitHandler: function (f) {
        if (confirm("¿Está seguro que desea guardar los cambios?")) {
          $.blockUI({message: "<h4>Guardando datos...</h4>"});
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