window.addEventListener("load", verificarJqueryImagenPerfil, false);
function verificarJqueryImagenPerfil() {
  ((window.jQuery && jQuery.ui) ? cargarSeccionImagenPerfil() : window.setTimeout(verificarJqueryImagenPerfil, 100));
}

function cargarSeccionImagenPerfil() {
  $("#btn-editar-imagen-perfil").click(function () {
    $("#mod-editar-imagen-perfil").modal("show");
  });

  $("#formulario-editar-imagen-perfil").validate({
    ignore: ":hidden",
    rules: {
      imagenPerfil: {
        required: true,
        validarImagen: true
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